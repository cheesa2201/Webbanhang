    <!-- Footer -->
    <footer class="bg-white border-top mt-5 pt-5 pb-4">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 mb-4">
                    <a href="shop.php" class="text-dark text-decoration-none d-flex align-items-center gap-2 mb-3 fw-bold fs-4">
                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 18px;">T</div>
                        TechShop
                    </a>
                    <p class="text-muted small">Cửa hàng cung cấp các thiết bị công nghệ chính hãng với giá cả cạnh tranh và dịch vụ bảo hành uy tín hàng đầu Việt Nam.</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-light border rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center"><i class="bi bi-facebook" style="color: #1877F2;"></i></a>
                        <a href="#" class="btn btn-sm btn-light border rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center"><i class="bi bi-youtube text-danger"></i></a>
                        <a href="#" class="btn btn-sm btn-light border rounded-circle" style="width:36px;height:36px;display:flex;align-items:center;justify-content:center"><i class="bi bi-instagram" style="color: #E4405F;"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Về chúng tôi</h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Giới thiệu TechShop</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Tuyển dụng</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Chính sách bảo mật</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Điều khoản sử dụng</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Hỗ trợ khách hàng</h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Trung tâm trợ giúp</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Chính sách bảo hành</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Chính sách đổi trả</a></li>
                        <li class="mb-2"><a href="#" class="text-muted text-decoration-none hover-primary">Hướng dẫn mua trả góp</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4 mb-4">
                    <h6 class="fw-bold mb-3">Liên hệ</h6>
                    <ul class="list-unstyled text-muted small">
                        <li class="mb-2 d-flex gap-2"><i class="bi bi-geo-alt text-primary"></i> 123 Đường Công Nghệ, Quận 1, TP. HCM</li>
                        <li class="mb-2 d-flex gap-2"><i class="bi bi-telephone text-primary"></i> 1900 1234 (Tổng đài hỗ trợ)</li>
                        <li class="mb-2 d-flex gap-2"><i class="bi bi-envelope text-primary"></i> cskh@techshop.vn</li>
                    </ul>
                </div>
            </div>
            <hr class="text-muted mt-2 mb-3">
            <div class="text-center text-muted small">
                &copy; <?php echo date('Y'); ?> TechShop. Tất cả quyền được bảo lưu.
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    
    <!-- Global AJAX Handlers -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Biến dùng chung để chèn Toast
        const toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        toastContainer.style.zIndex = '1055';
        document.body.appendChild(toastContainer);

        function showToast(message, isSuccess = true) {
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-bg-${isSuccess ? 'success' : 'danger'} border-0`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${isSuccess ? '<i class="bi bi-check-circle me-2"></i>' : '<i class="bi bi-exclamation-circle me-2"></i>'}
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            toastContainer.appendChild(toast);
            const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
            bsToast.show();
            toast.addEventListener('hidden.bs.toast', () => toast.remove());
        }

        // Xử lý Thêm Giỏ Hàng
        document.querySelectorAll('.ajax-cart-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                let qty = 1;
                const closestContainer = this.closest('.col-md-7');
                if (closestContainer) {
                    const qtyInput = closestContainer.querySelector('.qty-input');
                    if (qtyInput) qty = parseInt(qtyInput.value) || 1;
                }
                
                const formData = new FormData();
                formData.append('action', 'add_cart');
                formData.append('id', this.dataset.id);
                formData.append('name', this.dataset.name);
                formData.append('price', this.dataset.price);
                formData.append('brand', this.dataset.brand);
                formData.append('image', this.dataset.image);
                formData.append('qty', qty);

                fetch('ajax_cart_fav.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        showToast(data.message, true);
                        // Cập nhật số đếm giỏ hàng trên Header nếu có thẻ span.badge
                        const cartBadge = document.getElementById('cart-badge');
                        if(cartBadge) {
                            cartBadge.innerText = data.cart_count;
                            cartBadge.style.display = 'inline-block';
                        }
                    } else {
                        showToast(data.message, false);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Đã có lỗi xảy ra!', false);
                });
            });
        });

        // Xử lý Thêm/Bỏ Yêu Thích
        document.querySelectorAll('.ajax-fav-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('i');
                const formData = new FormData();
                formData.append('action', 'toggle_favorite');
                formData.append('id', this.dataset.id);
                formData.append('name', this.dataset.name);
                formData.append('price', this.dataset.price);
                formData.append('brand', this.dataset.brand);
                formData.append('image', this.dataset.image);

                fetch('ajax_cart_fav.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        showToast(data.message, true);
                        if(data.favorited) {
                            icon.className = 'bi bi-heart-fill text-danger';
                        } else {
                            icon.className = 'bi bi-heart';
                        }
                    } else {
                        showToast(data.message, false);
                    }
                })
                .catch(err => {
                    console.error(err);
                    showToast('Đã có lỗi xảy ra!', false);
                });
            });
        });
    });
    </script>
</body>
</html>