<x-guest-layout>
    <div class="auth-form-header">
        <a href="/" class="auth-back-link">
            <i class="fas fa-arrow-left"></i> Về trang chủ
        </a>
        <h1>Tạo tài khoản mới ✨</h1>
        <p>Đăng ký để theo dõi tình trạng hỗ trợ của bạn</p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="auth-alert error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Role mặc định: resident --}}
        <input type="hidden" name="role" value="resident">

        {{-- Họ và tên --}}
        <div class="auth-form-group">
            <label class="auth-label" for="name">
                <i class="fas fa-user"></i> Họ và tên
            </label>
            <input  type="text"
                    id="name"
                    name="name"
                    class="auth-input @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    placeholder="Nguyễn Văn A"
                    required
                    autofocus
                    autocomplete="name">
            @error('name')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Email + Phone (2 cột) --}}
        <div class="auth-form-row">
            <div class="auth-form-group">
                <label class="auth-label" for="email">
                    <i class="fas fa-envelope"></i> Email
                </label>
                <input  type="email"
                        id="email"
                        name="email"
                        class="auth-input @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="example@email.com"
                        required
                        autocomplete="username">
                @error('email')
                    <span class="auth-field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="phone">
                    <i class="fas fa-phone"></i> Điện thoại
                    <span class="optional-badge">Tuỳ chọn</span>
                </label>
                <input  type="tel"
                        id="phone"
                        name="phone"
                        class="auth-input @error('phone') is-invalid @enderror"
                        value="{{ old('phone') }}"
                        placeholder="0912 345 678">
                @error('phone')
                    <span class="auth-field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- CCCD --}}
        <div class="auth-form-group">
            <label class="auth-label" for="identity_card">
                <i class="fas fa-id-card"></i> Số CCCD / CMND
                <span class="optional-badge">Tuỳ chọn</span>
            </label>
            <input  type="text"
                    id="identity_card"
                    name="identity_card"
                    class="auth-input @error('identity_card') is-invalid @enderror"
                    value="{{ old('identity_card') }}"
                    placeholder="12 chữ số"
                    maxlength="20">
            <div class="auth-hint">Dùng để tra cứu tình trạng hỗ trợ và xác minh danh tính</div>
            @error('identity_card')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Password + Confirm (2 cột) --}}
        <div class="auth-form-row">
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
                            autocomplete="new-password">
                    <span class="auth-input-icon" onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                @error('password')
                    <span class="auth-field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="auth-form-group">
                <label class="auth-label" for="password_confirmation">
                    <i class="fas fa-lock"></i> Xác nhận
                </label>
                <div class="auth-input-wrap">
                    <input  type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="auth-input @error('password_confirmation') is-invalid @enderror"
                            placeholder="••••••••"
                            required
                            autocomplete="new-password">
                    <span class="auth-input-icon" onclick="togglePassword('password_confirmation', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                @error('password_confirmation')
                    <span class="auth-field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-submit-btn">
            <i class="fas fa-user-plus"></i> Đăng ký tài khoản
        </button>

        <div class="auth-divider"><span>hoặc</span></div>

        <div class="auth-switch">
            Đã có tài khoản?
            <a href="{{ route('login') }}">Đăng nhập ngay →</a>
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
