/**
 * ĐẠI PHÚC - Hỗ trợ bão lũ
 * Script chính
 */

// ============ TRA CỨU CCCD ============
function searchCCCD() {
    const input  = document.getElementById('cccdInput');
    const result = document.getElementById('searchResult');

    if (!input.value || input.value.length !== 12 || isNaN(input.value)) {
        result.textContent = 'Vui lòng nhập CCCD hợp lệ (12 số)';
        result.className   = 'search-result error';
        result.style.display = 'block';
        return;
    }

    result.innerHTML = `
        <strong>✓ Tìm thấy thông tin</strong><br>
        Họ tên: Nguyễn Văn A<br>
        CCCD: ${input.value}<br>
        Tình trạng: Đã nhận hỗ trợ - 5 gói quà<br>
        Ngày cập nhật: ${new Date().toLocaleDateString('vi-VN')}
    `;
    result.className     = 'search-result success';
    result.style.display = 'block';
}

// Cho phép nhấn Enter để tra cứu
document.addEventListener('DOMContentLoaded', function () {
    const cccdInput = document.getElementById('cccdInput');
    if (cccdInput) {
        cccdInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') searchCCCD();
        });
    }

    // Khởi tạo form phản ánh
    initFeedbackForm();
});

// ============ FORM PHẢN ÁNH ============

/** Map value -> label cho loại phản ánh */
const FEEDBACK_TYPES = {
    chua_nhan:    'Chưa nhận hỗ trợ',
    sai_thong_tin:'Sai thông tin',
    khan_cap:     'Khẩn cấp',
    de_xuat:      'Đề xuất',
    khac:         'Khác',
};

/** Danh sách phản ánh đã gửi (lưu trong bộ nhớ) */
const feedbackList = [];

function initFeedbackForm() {
    const form    = document.getElementById('feedbackForm');
    const content = document.getElementById('fb_content');

    if (!form) return;

    // Đếm ký tự textarea
    if (content) {
        content.addEventListener('input', function () {
            const len = this.value.length;
            document.getElementById('charCount').textContent = len;
            if (len > 500) this.value = this.value.slice(0, 500);
        });
    }

    // Submit form
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (validateFeedbackForm()) {
            submitFeedback();
        }
    });

    // Xoá lỗi khi người dùng nhập
    ['fb_name', 'fb_phone', 'fb_cccd', 'fb_type', 'fb_content'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('input', function () { clearError(id); });
    });
}

/** Validate toàn bộ form, trả về true nếu hợp lệ */
function validateFeedbackForm() {
    let valid = true;

    const name    = document.getElementById('fb_name').value.trim();
    const phone   = document.getElementById('fb_phone').value.trim();
    const cccd    = document.getElementById('fb_cccd').value.trim();
    const type    = document.getElementById('fb_type').value;
    const content = document.getElementById('fb_content').value.trim();

    if (!name) {
        showError('fb_name', 'err_name', 'Vui lòng nhập họ và tên');
        valid = false;
    }

    const phoneReg = /^(0|\+84)[0-9]{9,10}$/;
    if (!phone) {
        showError('fb_phone', 'err_phone', 'Vui lòng nhập số điện thoại');
        valid = false;
    } else if (!phoneReg.test(phone.replace(/\s/g, ''))) {
        showError('fb_phone', 'err_phone', 'Số điện thoại không hợp lệ');
        valid = false;
    }

    if (cccd && (cccd.length !== 12 || isNaN(cccd))) {
        showError('fb_cccd', 'err_cccd', 'CCCD phải đúng 12 chữ số');
        valid = false;
    }

    if (!type) {
        showError('fb_type', 'err_type', 'Vui lòng chọn loại phản ánh');
        valid = false;
    }

    if (!content) {
        showError('fb_content', 'err_content', 'Vui lòng nhập nội dung phản ánh');
        valid = false;
    } else if (content.length < 10) {
        showError('fb_content', 'err_content', 'Nội dung quá ngắn (tối thiểu 10 ký tự)');
        valid = false;
    }

    return valid;
}

function showError(inputId, errId, message) {
    const input = document.getElementById(inputId);
    const err   = document.getElementById(errId);
    if (input) input.classList.add('is-invalid');
    if (err)   err.textContent = message;
}

function clearError(inputId) {
    const input = document.getElementById(inputId);
    const errId = 'err_' + inputId.replace('fb_', '');
    const err   = document.getElementById(errId);
    if (input) input.classList.remove('is-invalid');
    if (err)   err.textContent = '';
}

/** Giả lập gửi và thêm vào bảng */
function submitFeedback() {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';

    // Giả lập delay API 0.8s
    setTimeout(function () {
        const entry = {
            id:      feedbackList.length + 1,
            name:    document.getElementById('fb_name').value.trim(),
            type:    document.getElementById('fb_type').value,
            address: document.getElementById('fb_address').value.trim(),
            content: document.getElementById('fb_content').value.trim(),
            time:    new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' }),
            status:  'pending',
        };

        feedbackList.unshift(entry);
        renderFeedbackTable();
        showFeedbackToast('success', '✅ Gửi phản ánh thành công! Chúng tôi sẽ phản hồi trong vòng 24 giờ.');
        document.getElementById('feedbackForm').reset();
        document.getElementById('charCount').textContent = '0';

        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Gửi phản ánh';
    }, 800);
}

/** Render lại tbody của bảng */
function renderFeedbackTable() {
    const tbody = document.getElementById('feedbackTableBody');
    const badge = document.getElementById('totalCount');

    badge.textContent = feedbackList.length + ' phản ánh';

    if (feedbackList.length === 0) {
        tbody.innerHTML = `
            <tr class="empty-row">
                <td colspan="5">
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Chưa có phản ánh nào</p>
                    </div>
                </td>
            </tr>`;
        return;
    }

    tbody.innerHTML = feedbackList.map(function (item) {
        const typeLabel  = FEEDBACK_TYPES[item.type] || item.type;
        const statusHtml = item.status === 'pending'
            ? '<span class="status-badge pending">⏳ Chờ xử lý</span>'
            : '<span class="status-badge processing">🔄 Đang xử lý</span>';

        return `
            <tr>
                <td><strong>#${item.id}</strong></td>
                <td>${escapeHtml(item.name)}</td>
                <td><span class="type-badge">${typeLabel}</span></td>
                <td>${item.time}</td>
                <td>${statusHtml}</td>
            </tr>`;
    }).join('');
}

/** Hiện toast thông báo */
function showFeedbackToast(type, message) {
    const toast = document.getElementById('feedbackToast');
    toast.className    = 'feedback-toast ' + type;
    toast.innerHTML    = message;
    toast.style.display = 'block';

    setTimeout(function () {
        toast.style.display = 'none';
    }, 5000);
}

/** Reset form */
function resetFeedbackForm() {
    document.getElementById('feedbackForm').reset();
    document.getElementById('charCount').textContent = '0';
    ['fb_name', 'fb_phone', 'fb_cccd', 'fb_type', 'fb_content'].forEach(function (id) {
        clearError(id);
    });
    document.getElementById('feedbackToast').style.display = 'none';
}

/** Escape HTML để tránh XSS */
function escapeHtml(str) {
    return str.replace(/[&<>"']/g, function (c) {
        return { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[c];
    });
}
