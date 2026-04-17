        <!-- Footer terms -->
        <div class="mt-4 text-center text-muted" style="font-size: 12px; max-width: 300px; margin: 0 auto;">
            Bằng cách thao tác, bạn đồng ý với <a href="#" class="text-custom text-decoration-none">Điều khoản sử dụng</a> và <a href="#" class="text-custom text-decoration-none">Chính sách bảo mật</a>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePasswords = document.querySelectorAll('.toggle-password');
            togglePasswords.forEach(function(icon) {
                icon.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const input = document.querySelector(targetId);
                    const iconElement = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        iconElement.classList.remove('bi-eye-slash');
                        iconElement.classList.add('bi-eye');
                    } else {
                        input.type = 'password';
                        iconElement.classList.remove('bi-eye');
                        iconElement.classList.add('bi-eye-slash');
                    }
                });
            });
        });
    </script>
</body>
</html>
