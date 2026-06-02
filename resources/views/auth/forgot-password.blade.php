<x-guest-layout>
    <div class="auth-form-header">
        <a href="{{ route('login') }}" class="auth-back-link">
            <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
        </a>
        <h1>Quên mật khẩu? 🔑</h1>
        <p>Nhập email của bạn, chúng tôi sẽ gửi link đặt lại mật khẩu ngay lập tức</p>
    </div>

    {{-- Session Status (gửi thành công) --}}
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

    <form method="POST" action="{{ route('password.email') }}">
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
                        autocomplete="email">
            </div>
            @error('email')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mô tả quá trình --}}
        <div style="background:rgba(13,148,136,.08);border:1px solid rgba(13,148,136,.2);border-radius:10px;padding:.85rem 1rem;margin-bottom:1.2rem;font-size:.83rem;color:#0d9488;line-height:1.65">
            <i class="fas fa-info-circle" style="margin-right:6px"></i>
            Sau khi gửi, hãy kiểm tra hộp thư <strong>Inbox</strong> (và thư mục <strong>Spam</strong>) để tìm email đặt lại mật khẩu. Link có hiệu lực trong <strong>60 phút</strong>.
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-submit-btn" id="btn-send-reset">
            <i class="fas fa-paper-plane"></i> Gửi link đặt lại mật khẩu
        </button>

        <div class="auth-switch" style="margin-top:1.2rem">
            Nhớ ra mật khẩu rồi?
            <a href="{{ route('login') }}">Đăng nhập ngay →</a>
        </div>
    </form>
</x-guest-layout>

<script>
// Disable nút sau khi submit để tránh double-click
document.querySelector('form').addEventListener('submit', function() {
    const btn = document.getElementById('btn-send-reset');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
});
</script>
