<?php
declare(strict_types=1);

class Order
{
    public function __construct(private PDO $pdo) {}

    public function getPaymentMethods(): array
    {
        return $this->pdo
            ->query("SELECT * FROM phuong_thuc_thanh_toan WHERE trang_thai='hien' ORDER BY id_phuong_thuc")
            ->fetchAll();
    }

    public function getOrdersByUser(int $uid): array
    {
        $s = $this->pdo->prepare("
            SELECT dh.*, pt.ten_phuong_thuc
            FROM don_hang dh
            LEFT JOIN phuong_thuc_thanh_toan pt ON pt.id_phuong_thuc = dh.id_phuong_thuc
            WHERE dh.id_nguoi_dung = :u
            ORDER BY dh.ngay_dat DESC
        ");
        $s->execute([':u' => $uid]);
        return $s->fetchAll();
    }

    public function getByIdForUser(int $oid, int $uid): ?array
    {
        $s = $this->pdo->prepare("
            SELECT dh.*, pt.ten_phuong_thuc
            FROM don_hang dh
            LEFT JOIN phuong_thuc_thanh_toan pt ON pt.id_phuong_thuc = dh.id_phuong_thuc
            WHERE dh.id_don_hang = :o AND dh.id_nguoi_dung = :u
            LIMIT 1
        ");
        $s->execute([':o' => $oid, ':u' => $uid]);
        $o = $s->fetch();
        return $o ?: null;
    }

    public function getItems(int $oid): array
    {
        $s = $this->pdo->prepare("
            SELECT ct.*, sp.ten_san_pham, sp.ma_san_pham,
                   sp.hinh_anh_chinh, sp.hinh_anh_chinh AS hinh_anh
            FROM chi_tiet_don_hang ct
            JOIN san_pham sp ON sp.id_san_pham = ct.id_san_pham
            WHERE ct.id_don_hang = :o
        ");
        $s->execute([':o' => $oid]);
        return $s->fetchAll();
    }

    public function createFromCart(int $uid, array $d): array
    {
        try {
            $this->pdo->beginTransaction();

            $m = (int)($d['id_phuong_thuc'] ?? 0);
            $n = trim($d['ten_nguoi_nhan'] ?? '');
            $phone = trim($d['so_dien_thoai_nhan'] ?? '');
            $a = trim($d['dia_chi_giao_hang'] ?? '');
            $note = trim($d['ghi_chu'] ?? '');

            if (!$m || !$n || !$phone || !$a) {
                throw new RuntimeException('Vui lòng nhập đầy đủ thông tin.');
            }

            $cs = $this->pdo->prepare("
                SELECT id_gio_hang
                FROM gio_hang
                WHERE id_nguoi_dung = :u
                LIMIT 1
                FOR UPDATE
            ");
            $cs->execute([':u' => $uid]);
            $cart = $cs->fetch();

            if (!$cart) {
                throw new RuntimeException('Giỏ hàng trống.');
            }

            $is = $this->pdo->prepare("
                SELECT ct.*, sp.ten_san_pham, sp.so_luong_ton, sp.trang_thai
                FROM chi_tiet_gio_hang ct
                JOIN san_pham sp ON sp.id_san_pham = ct.id_san_pham
                WHERE ct.id_gio_hang = :g
                FOR UPDATE
            ");
            $is->execute([':g' => $cart['id_gio_hang']]);
            $items = $is->fetchAll();

            if (!$items) {
                throw new RuntimeException('Giỏ hàng trống.');
            }

            $total = 0;
            foreach ($items as $it) {
                if ($it['trang_thai'] !== 'dang_ban' || (int)$it['so_luong_ton'] < (int)$it['so_luong']) {
                    throw new RuntimeException('Sản phẩm không đủ tồn kho: ' . $it['ten_san_pham']);
                }

                $total += (int)$it['so_luong'] * (float)$it['don_gia'];
            }

            $os = $this->pdo->prepare("
                INSERT INTO don_hang (
                    id_nguoi_dung,
                    id_phuong_thuc,
                    tong_tien,
                    ten_nguoi_nhan,
                    so_dien_thoai_nhan,
                    dia_chi_giao_hang,
                    ghi_chu
                ) VALUES (
                    :u, :m, :t, :n, :phone, :a, :note
                )
            ");

            $os->execute([
                ':u' => $uid,
                ':m' => $m,
                ':t' => $total,
                ':n' => $n,
                ':phone' => $phone,
                ':a' => $a,
                ':note' => $note ?: null,
            ]);

            $oid = (int)$this->pdo->lastInsertId();

            $ds = $this->pdo->prepare("
                INSERT INTO chi_tiet_don_hang (
                    id_don_hang,
                    id_san_pham,
                    so_luong,
                    don_gia,
                    thanh_tien
                ) VALUES (
                    :o, :p, :q, :d, :tt
                )
            ");

            $us = $this->pdo->prepare("
                UPDATE san_pham
                SET so_luong_ton = so_luong_ton - :q1,
                    trang_thai = CASE
                        WHEN so_luong_ton - :q2 <= 0 THEN 'het_hang'
                        ELSE trang_thai
                    END
                WHERE id_san_pham = :p
            ");

            foreach ($items as $it) {
                $qty = (int)$it['so_luong'];
                $unitPrice = (float)$it['don_gia'];

                $ds->execute([
                    ':o' => $oid,
                    ':p' => $it['id_san_pham'],
                    ':q' => $qty,
                    ':d' => $unitPrice,
                    ':tt' => $qty * $unitPrice,
                ]);

                $us->execute([
                    ':q1' => $qty,
                    ':q2' => $qty,
                    ':p' => $it['id_san_pham'],
                ]);
            }

            $this->pdo->prepare("
                INSERT INTO thanh_toan(id_don_hang, id_phuong_thuc, so_tien)
                VALUES(:o, :m, :t)
            ")->execute([
                ':o' => $oid,
                ':m' => $m,
                ':t' => $total,
            ]);

            $this->pdo->prepare("
                INSERT INTO van_chuyen(id_don_hang, phi_van_chuyen)
                VALUES(:o, 0)
            ")->execute([
                ':o' => $oid,
            ]);

            $this->pdo->prepare("
                DELETE FROM chi_tiet_gio_hang
                WHERE id_gio_hang = :g
            ")->execute([
                ':g' => $cart['id_gio_hang'],
            ]);

            $this->pdo->commit();

            return [
                'success' => true,
                'id_don_hang' => $oid,
            ];
        } catch (Throwable $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function stats(): array
    {
        return [
            'totalOrders' => (int)$this->pdo->query("SELECT COUNT(*) FROM don_hang")->fetchColumn(),
            'revenue' => (float)$this->pdo->query("SELECT COALESCE(SUM(tong_tien),0) FROM don_hang")->fetchColumn(),
            'pending' => (int)$this->pdo->query("SELECT COUNT(*) FROM don_hang WHERE trang_thai_don_hang='cho_xac_nhan'")->fetchColumn(),
        ];
    }

    public function getAllOrders(): array
    {
        return $this->pdo->query("
            SELECT dh.*, nd.ho_ten, nd.email, pt.ten_phuong_thuc
            FROM don_hang dh
            JOIN nguoi_dung nd ON nd.id_nguoi_dung = dh.id_nguoi_dung
            LEFT JOIN phuong_thuc_thanh_toan pt ON pt.id_phuong_thuc = dh.id_phuong_thuc
            ORDER BY dh.ngay_dat DESC
        ")->fetchAll();
    }

    public function updateStatus(int $id, string $st): bool
    {
        if (!in_array($st, ['cho_xac_nhan', 'da_xac_nhan', 'dang_giao', 'da_giao', 'da_huy'], true)) {
            return false;
        }

        $s = $this->pdo->prepare("
            UPDATE don_hang
            SET trang_thai_don_hang = :s
            WHERE id_don_hang = :id
        ");

        return $s->execute([
            ':s' => $st,
            ':id' => $id,
        ]);
    }
}