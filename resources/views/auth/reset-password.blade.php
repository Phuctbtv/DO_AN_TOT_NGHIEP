<x-guest-layout>
    <div class="auth-form-header">
        <a href="{{ route('login') }}" class="auth-back-link">
            <i class="fas fa-arrow-left"></i> Quay lại đăng nhập
        </a>
        <h1>Đặt lại mật khẩu 🔒</h1>
        <p>Tạo mật khẩu mới an toàn cho tài khoản của bạn</p>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="auth-alert error">
            <i class="fas fa-exclamation-circle"></i>
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        {{-- Token ẩn --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                        value="{{ old('email', $request->email) }}"
                        placeholder="example@email.com"
                        required
                        autofocus
                        autocomplete="username">
            </div>
            @error('email')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Mật khẩu mới --}}
        <div class="auth-form-group">
            <label class="auth-label" for="password">
                <i class="fas fa-lock"></i> Mật khẩu mới
            </label>
            <div class="auth-input-wrap">
                <input  type="password"
                        id="password"
                        name="password"
                        class="auth-input @error('password') is-invalid @enderror"
                        placeholder="Tối thiểu 8 ký tự"
                        required
                        autocomplete="new-password">
                <span class="auth-input-icon" onclick="togglePassword('password', this)">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            @error('password')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
            {{-- Strength indicator --}}
            <div id="strength-bar" style="height:4px;border-radius:2px;margin-top:6px;background:#e2e8f0;overflow:hidden">
                <div id="strength-fill" style="height:100%;width:0;border-radius:2px;transition:all .3s"></div>
            </div>
            <div id="strength-text" style="font-size:.73rem;margin-top:3px;color:#94a3b8"></div>
        </div>

        {{-- Xác nhận mật khẩu --}}
        <div class="auth-form-group">
            <label class="auth-label" for="password_confirmation">
                <i class="fas fa-check-double"></i> Xác nhận mật khẩu
            </label>
            <div class="auth-input-wrap">
                <input  type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="auth-input @error('password_confirmation') is-invalid @enderror"
                        placeholder="Nhập lại mật khẩu mới"
                        required
                        autocomplete="new-password">
                <span class="auth-input-icon" onclick="togglePassword('password_confirmation', this)">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <div id="match-msg" style="font-size:.73rem;margin-top:3px"></div>
            @error('password_confirmation')
                <span class="auth-field-error">{{ $message }}</span>
            @enderror
        </div>

        {{-- Yêu cầu mật khẩu --}}
        <div style="background:rgba(13,148,136,.08);border:1px solid rgba(13,148,136,.2);border-radius:10px;padding:.85rem 1rem;margin-bottom:1.2rem;font-size:.82rem;color:#475569;line-height:1.8">
            <div style="font-weight:600;color:#0d9488;margin-bottom:.3rem"><i class="fas fa-shield-check"></i> Yêu cầu mật khẩu mạnh</div>
            <div id="req-length"  class="req-item"><i class="fas fa-circle req-dot"></i> Ít nhất 8 ký tự</div>
            <div id="req-upper"   class="req-item"><i class="fas fa-circle req-dot"></i> Ít nhất 1 chữ hoa (A–Z)</div>
            <div id="req-number"  class="req-item"><i class="fas fa-circle req-dot"></i> Ít nhất 1 chữ số (0–9)</div>
        </div>

        {{-- Submit --}}
        <button type="submit" class="auth-submit-btn" id="btn-reset">
            <i class="fas fa-key"></i> Đặt lại mật khẩu
        </button>
    </form>
</x-guest-layout>

<style>
.req-item { display:flex; align-items:center; gap:.45rem; font-size:.8rem; color:#64748b; }
.req-item.ok { color:#059669; }
.req-dot { font-size:.4rem !important; }
.req-item.ok .req-dot { color:#059669; }
</style>

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

// Strength & requirement check
const pwField   = document.getElementById('password');
const confField = document.getElementById('password_confirmation');

pwField.addEventListener('input', () => {
    const val = pwField.value;
    const len   = val.length >= 8;
    const upper = /[A-Z]/.test(val);
    const num   = /[0-9]/.test(val);

    toggle('req-length', len);
    toggle('req-upper',  upper);
    toggle('req-number', num);

    const score = [len, upper, num].filter(Boolean).length;
    const fill  = document.getElementById('strength-fill');
    const text  = document.getElementById('strength-text');
    const colors = ['#ef4444','#f59e0b','#10b981'];
    const labels = ['Yếu','Trung bình','Mạnh'];
    fill.style.width  = `${(score/3)*100}%`;
    fill.style.background = colors[score-1] || '#e2e8f0';
    text.textContent  = score > 0 ? `Độ mạnh: ${labels[score-1]}` : '';
    text.style.color  = colors[score-1] || '#94a3b8';

    checkMatch();
});

confField.addEventListener('input', checkMatch);

function checkMatch() {
    const msg = document.getElementById('match-msg');
    if (!confField.value) { msg.textContent = ''; return; }
    if (pwField.value === confField.value) {
        msg.textContent = '✅ Mật khẩu khớp';
        msg.style.color = '#059669';
    } else {
        msg.textContent = '❌ Mật khẩu chưa khớp';
        msg.style.color = '#dc2626';
    }
}

function toggle(id, ok) {
    const el = document.getElementById(id);
    if (ok) el.classList.add('ok'); else el.classList.remove('ok');
}

// Disable nút khi submit
document.querySelector('form').addEventListener('submit', function(e) {
    const pw   = document.getElementById('password').value;
    const conf = document.getElementById('password_confirmation').value;
    if (pw !== conf) {
        e.preventDefault();
        document.getElementById('match-msg').textContent = '❌ Mật khẩu chưa khớp, vui lòng kiểm tra lại';
        document.getElementById('match-msg').style.color = '#dc2626';
        return;
    }
    const btn = document.getElementById('btn-reset');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
});
</script>
