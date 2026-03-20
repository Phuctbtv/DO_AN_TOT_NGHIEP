<x-guest-layout>
    <div class="auth-form-header">
        <a href="{{ url('/') }}" class="auth-back-link">
            <i class="fas fa-arrow-left"></i> Về trang chủ
        </a>
        <h1>Chào mừng trở lại 👋</h1>
        <p>Đăng nhập vào hệ thống quản lý cứu trợ</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="auth-alert success">
            <i class="fas fa-check-circle"></i>
            {{ session('status') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="auth-alert error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="auth-form-group">
            <label class="auth-label" for="email">
                <i class="fas fa-envelope"></i> Địa chỉ Email
            </label>
            <div class="auth-input-wrap">
                <input  type="email"
                        id="email"
                        name="email"
                        class="auth-input @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="example@email.com"
                        required
                        autofocus
                        autocomplete="username">
            </div>
            @error('email')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password --}}
        <div class="auth-form-group">
            <label class="auth-label" for="password">
                <i class="fas fa-lock"></i> Mật khẩu
            </label>
            <div class="auth-input-wrap">
                <input  type="password"
                        id="password"
                        name="password"
                        class="auth-input @error('password') is-invalid @enderror"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password">
                <span class="auth-input-icon" onclick="togglePassword('password', this)">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            @error('password')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Remember + Forgot --}}
        <div class="auth-options">
            <label class="auth-checkbox-label">
                <input type="checkbox" id="remember_me" name="remember" class="auth-checkbox">
                Ghi nhớ đăng nhập
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link">Quên mật khẩu?</a>
            @endif
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-submit-btn">
            <i class="fas fa-sign-in-alt"></i> Đăng nhập
        </button>

        <div class="auth-divider"><span>hoặc</span></div>

        <div class="auth-switch">
            Chưa có tài khoản?
            <a href="{{ route('register') }}">Đăng ký ngay →</a>
        </div>
    </form>
</x-guest-layout>

<script>
function togglePassword(fieldId, iconEl) {
    const field = document.getElementById(fieldId);
    const icon  = iconEl.querySelector('i');
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
