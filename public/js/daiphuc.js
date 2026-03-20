/* ============================================================
   ĐẠI PHÚC - HỖ TRỢ BÃO LŨ  |  JavaScript + Mock Data
   ============================================================ */

// ============ MOCK DATA ============
const MOCK = {
  // Marker trên bản đồ (vùng miền Trung Việt Nam)
  markers: [
    { lat: 16.0544, lng: 108.2022, name: 'Xã Hòa Bình, Đà Nẵng', status: 'urgent', people: 45, note: 'Cần lương thực gấp' },
    { lat: 16.4637, lng: 107.5909, name: 'TP Huế - KĐT An Vân Dương', status: 'helping', people: 120, note: 'Đang vận chuyển 50 thùng mì' },
    { lat: 15.8794, lng: 108.3350, name: 'Hội An - Cẩm Nam', status: 'stable', people: 80, note: 'Đã nhận đủ nhu yếu phẩm' },
    { lat: 15.5694, lng: 108.4743, name: 'Quảng Ngãi - Sơn Tịnh', status: 'urgent', people: 200, note: 'Mực nước dâng cao, cần cứu hộ' },
    { lat: 14.3607, lng: 108.9914, name: 'Bình Định - An Nhơn', status: 'helping', people: 90, note: 'Xe TX-045 đang trên đường' },
    { lat: 16.7540, lng: 107.1855, name: 'Quảng Trị - Hải Lăng', status: 'urgent', people: 150, note: 'Thiếu nước sạch nghiêm trọng' },
    { lat: 15.1201, lng: 108.8002, name: 'Quảng Ngãi - Đức Phổ', status: 'stable', people: 65, note: 'Ổn định, đã sơ tán xong' },
    { lat: 16.0678, lng: 108.1500, name: 'Đà Nẵng - Hòa Vang', status: 'helping', people: 35, note: 'Đội tình nguyện đang hỗ trợ' },
  ],

  // Bảng tin minh bạch
  activities: [
    { time: '2 phút trước', text: 'Xe TX-089 giao 120 gói quà tại xã Hòa Bình, Đà Nẵng', badge: 'Hoàn thành', type: 'success' },
    { time: '5 phút trước', text: 'Kho Quảng Trị xuất 50 thùng mỳ + 200L nước sạch', badge: 'Xuất kho', type: 'info' },
    { time: '12 phút trước', text: 'Xe TX-045 khởi hành từ kho Huế đến Quảng Ngãi', badge: 'Đang giao', type: 'warning' },
    { time: '18 phút trước', text: 'Tiếp nhận 2 tấn gạo từ nhà tài trợ ABC Corp', badge: 'Nhập kho', type: 'info' },
    { time: '25 phút trước', text: 'Hộ dân Nguyễn Văn A (Hội An) xác nhận đã nhận hàng', badge: 'Xác nhận', type: 'success' },
    { time: '32 phút trước', text: 'Xe TX-102 giao 80 phần quà tại Hải Lăng, Quảng Trị', badge: 'Hoàn thành', type: 'success' },
    { time: '40 phút trước', text: 'Đăng ký cứu trợ mới: 12 hộ dân xã Sơn Tịnh', badge: 'Chờ duyệt', type: 'warning' },
    { time: '1 giờ trước', text: 'Kho Đà Nẵng nhập 500kg quần áo từ chiến dịch quyên góp', badge: 'Nhập kho', type: 'info' },
  ],

  // Tra cứu CCCD mock
  lookupData: {
    '012345678901': {
      name: 'Nguyễn V** A',
      area: 'Xã Hòa Bình, Đà Nẵng',
      status: 'Đã nhận hàng',
      statusType: 'success',
      date: '18/03/2026',
      photo: true,
    },
    '098765432109': {
      name: 'Trần T** B',
      area: 'Hải Lăng, Quảng Trị',
      status: 'Đang vận chuyển',
      statusType: 'warning',
      date: '20/03/2026',
      photo: false,
    },
    '111222333444': {
      name: 'Lê V** C',
      area: 'Cẩm Nam, Hội An',
      status: 'Chờ phê duyệt',
      statusType: 'info',
      date: '—',
      photo: false,
    },
  },

  // Admin dashboard
  adminTrips: [
    { id: 'TX-089', driver: 'Phạm Văn D', from: 'Kho Đà Nẵng', to: 'Xã Hòa Bình', items: 120, status: 'Hoàn thành', statusType: 'success' },
    { id: 'TX-045', driver: 'Nguyễn Hữu E', from: 'Kho Huế', to: 'Sơn Tịnh, Q.Ngãi', items: 80, status: 'Đang giao', statusType: 'warning' },
    { id: 'TX-102', driver: 'Trần Minh F', from: 'Kho Q.Trị', to: 'Hải Lăng', items: 95, status: 'Hoàn thành', statusType: 'success' },
    { id: 'TX-078', driver: 'Lê Quang G', from: 'Kho Đà Nẵng', to: 'Hòa Vang', items: 60, status: 'Chuẩn bị', statusType: 'info' },
    { id: 'TX-111', driver: 'Võ Thanh H', from: 'Kho Huế', to: 'An Nhơn, B.Định', items: 150, status: 'Đang giao', statusType: 'warning' },
  ],

  // Warehouse
  warehouseItems: [
    { name: 'Gạo', unit: 'kg', inStock: 4500, incoming: 2000, outgoing: 1200 },
    { name: 'Mỳ tôm', unit: 'thùng', inStock: 890, incoming: 300, outgoing: 450 },
    { name: 'Nước suối', unit: 'lít', inStock: 12000, incoming: 5000, outgoing: 8000 },
    { name: 'Quần áo', unit: 'bộ', inStock: 2300, incoming: 500, outgoing: 1800 },
    { name: 'Chăn màn', unit: 'cái', inStock: 450, incoming: 100, outgoing: 300 },
    { name: 'Thuốc y tế', unit: 'hộp', inStock: 180, incoming: 50, outgoing: 120 },
  ],

  // Driver deliveries
  driverDeliveries: [
    { id: 1, address: 'Thôn 3, Xã Hòa Bình, Đà Nẵng', household: 'Nguyễn Văn A', items: '10 gói quà', distance: '2.3 km', status: 'pending', phone: '0901234567' },
    { id: 2, address: 'Tổ 5, Cẩm Nam, Hội An', household: 'Trần Thị B', items: '8 gói quà', distance: '5.1 km', status: 'delivering', phone: '0912345678' },
    { id: 3, address: 'Khối 4, TT Hải Lăng, Q.Trị', household: 'Lê Văn C', items: '15 gói quà', distance: '12.7 km', status: 'completed', phone: '0923456789' },
  ],

  // Resident timeline
  residentTimeline: [
    { step: 1, title: 'Đã đăng ký', desc: 'Yêu cầu đã gửi lúc 08:30 ngày 18/03', status: 'done' },
    { step: 2, title: 'Admin phê duyệt', desc: 'Được duyệt lúc 09:15 ngày 18/03', status: 'done' },
    { step: 3, title: 'Đang vận chuyển', desc: 'Xe TX-089 đang trên đường giao', status: 'active' },
    { step: 4, title: 'Đã nhận hàng', desc: 'Chờ xác nhận từ hộ dân', status: 'pending' },
  ],
};

// ============ MAP INITIALIZATION ============
function initMap(elementId) {
  if (!document.getElementById(elementId)) return null;

  const map = L.map(elementId).setView([16.0544, 108.0], 8);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 18,
  }).addTo(map);

  const icons = {
    urgent: L.divIcon({
      className: 'custom-marker',
      html: '<div style="width:16px;height:16px;background:#ef4444;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>',
      iconSize: [16, 16], iconAnchor: [8, 8],
    }),
    helping: L.divIcon({
      className: 'custom-marker',
      html: '<div style="width:16px;height:16px;background:#eab308;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>',
      iconSize: [16, 16], iconAnchor: [8, 8],
    }),
    stable: L.divIcon({
      className: 'custom-marker',
      html: '<div style="width:16px;height:16px;background:#22c55e;border-radius:50%;border:3px solid #fff;box-shadow:0 2px 6px rgba(0,0,0,.3);"></div>',
      iconSize: [16, 16], iconAnchor: [8, 8],
    }),
  };

  MOCK.markers.forEach(m => {
    const statusText = { urgent: '🔴 Cần gấp', helping: '🟡 Đang hỗ trợ', stable: '🟢 Đã ổn định' };
    L.marker([m.lat, m.lng], { icon: icons[m.status] })
      .addTo(map)
      .bindPopup(`
        <div style="min-width:200px">
          <strong style="font-size:14px">${m.name}</strong><br>
          <span style="font-size:12px;color:#64748b">${statusText[m.status]}</span><br>
          <hr style="margin:6px 0;border-color:#e2e8f0">
          <span style="font-size:12px">👥 ${m.people} hộ dân</span><br>
          <span style="font-size:12px">📝 ${m.note}</span>
        </div>
      `);
  });

  return map;
}

// ============ MAP FOR ADMIN DASHBOARD ============
function initAdminMap(elementId) {
  if (!document.getElementById(elementId)) return null;

  const map = L.map(elementId).setView([16.0544, 108.0], 8);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap',
    maxZoom: 18,
  }).addTo(map);

  // Simulated vehicle positions
  const vehicles = [
    { lat: 16.1, lng: 108.15, id: 'TX-045', status: 'Đang giao' },
    { lat: 15.95, lng: 108.28, id: 'TX-089', status: 'Hoàn thành' },
    { lat: 16.7, lng: 107.2, id: 'TX-102', status: 'Đang giao' },
  ];

  vehicles.forEach(v => {
    L.marker([v.lat, v.lng], {
      icon: L.divIcon({
        className: 'vehicle-marker',
        html: `<div style="background:#0d9488;color:#fff;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:700;white-space:nowrap;box-shadow:0 2px 6px rgba(0,0,0,.2);">🚛 ${v.id}</div>`,
        iconSize: [80, 24], iconAnchor: [40, 12],
      }),
    })
      .addTo(map)
      .bindPopup(`<strong>${v.id}</strong><br>${v.status}`);
  });

  return map;
}

// ============ CHARTS (Admin Dashboard) ============
function initAdminCharts() {
  // Biểu đồ cột - Số chuyến xe theo ngày
  const tripsCtx = document.getElementById('tripsChart');
  if (tripsCtx) {
    new Chart(tripsCtx, {
      type: 'bar',
      data: {
        labels: ['14/03', '15/03', '16/03', '17/03', '18/03', '19/03', '20/03'],
        datasets: [{
          label: 'Chuyến xe',
          data: [12, 19, 15, 25, 22, 30, 28],
          backgroundColor: 'rgba(13,148,136,0.7)',
          borderRadius: 6,
          borderSkipped: false,
        }],
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: { beginAtZero: true, grid: { color: '#f1f5f9' } },
          x: { grid: { display: false } },
        },
      },
    });
  }

  // Biểu đồ tròn - Trạng thái hộ dân
  const statusCtx = document.getElementById('statusChart');
  if (statusCtx) {
    new Chart(statusCtx, {
      type: 'doughnut',
      data: {
        labels: ['Đã nhận', 'Đang giao', 'Chờ duyệt'],
        datasets: [{
          data: [8500, 2800, 1267],
          backgroundColor: ['#22c55e', '#eab308', '#3b82f6'],
          borderWidth: 0,
        }],
      },
      options: {
        responsive: true,
        cutout: '65%',
        plugins: {
          legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true } },
        },
      },
    });
  }
}

// ============ GEOLOCATION ============
function getLocation(latField, lngField, btnElement) {
  if (!navigator.geolocation) {
    alert('Trình duyệt không hỗ trợ GPS');
    return;
  }
  if (btnElement) btnElement.textContent = '⏳ Đang lấy vị trí...';

  navigator.geolocation.getCurrentPosition(
    pos => {
      const lat = pos.coords.latitude.toFixed(6);
      const lng = pos.coords.longitude.toFixed(6);
      if (document.getElementById(latField)) document.getElementById(latField).value = lat;
      if (document.getElementById(lngField)) document.getElementById(lngField).value = lng;
      if (btnElement) btnElement.textContent = '✅ Đã lấy vị trí';
    },
    () => {
      alert('Không thể lấy vị trí. Vui lòng cho phép truy cập GPS.');
      if (btnElement) btnElement.textContent = '📍 Lấy vị trí hiện tại';
    },
    { enableHighAccuracy: true, timeout: 10000 }
  );
}

// ============ CCCD LOOKUP ============
function lookupCCCD() {
  const cccd = document.getElementById('cccdInput')?.value?.trim();
  const resultDiv = document.getElementById('lookupResult');
  if (!cccd || !resultDiv) return;

  const data = MOCK.lookupData[cccd];
  if (data) {
    const statusColors = { success: '#dcfce7;color:#16a34a', warning: '#fef3c7;color:#d97706', info: '#dbeafe;color:#2563eb' };
    resultDiv.innerHTML = `
      <div class="lookup-result animate-in">
        <h4 style="margin-bottom:1rem;font-size:1.05rem;">📋 Kết quả tra cứu</h4>
        <table style="width:100%">
          <tr><td style="color:#64748b;padding:.5rem 0;width:140px">Họ tên:</td><td style="font-weight:600">${data.name}</td></tr>
          <tr><td style="color:#64748b;padding:.5rem 0">Khu vực:</td><td>${data.area}</td></tr>
          <tr><td style="color:#64748b;padding:.5rem 0">Trạng thái:</td><td><span class="status-badge" style="background:${statusColors[data.statusType]};padding:.3rem .8rem;border-radius:999px;font-size:.8rem;font-weight:600">${data.status}</span></td></tr>
          <tr><td style="color:#64748b;padding:.5rem 0">Ngày nhận:</td><td>${data.date}</td></tr>
          <tr><td style="color:#64748b;padding:.5rem 0">Ảnh minh chứng:</td><td>${data.photo ? '📸 <a href="#" style="color:#0d9488;font-weight:500">Xem ảnh</a>' : '<span style="color:#94a3b8">Chưa có</span>'}</td></tr>
        </table>
      </div>`;
  } else {
    resultDiv.innerHTML = `
      <div class="lookup-result animate-in" style="text-align:center;padding:2rem">
        <div style="font-size:2.5rem;margin-bottom:.5rem">🔍</div>
        <p style="font-weight:600;margin-bottom:.25rem">Không tìm thấy thông tin</p>
        <p style="color:#64748b;font-size:.85rem">Vui lòng kiểm tra lại số CCCD hoặc liên hệ hotline 1900.636.838</p>
      </div>`;
  }
}

// ============ TOAST NOTIFICATION ============
function showToast(message) {
  const existing = document.querySelector('.toast');
  if (existing) existing.remove();

  const toast = document.createElement('div');
  toast.className = 'toast';
  toast.textContent = message;
  document.body.appendChild(toast);
  setTimeout(() => toast.remove(), 4000);
}

// ============ FORM HANDLERS ============
function handleRegistration(e) {
  e.preventDefault();
  showToast('✅ Đã gửi yêu cầu thành công! Admin sẽ phê duyệt trong thời gian sớm nhất.');
  e.target.reset();
  // Close modal if using Alpine
  const modal = e.target.closest('[x-data]');
  if (modal && modal.__x) {
    modal.__x.$data.open = false;
  }
}

function handleFeedback(e) {
  e.preventDefault();
  showToast('✅ Đã gửi phản hồi thành công! Cảm ơn bạn đã đóng góp.');
  e.target.reset();
}

function handleWarehouseForm(e, type) {
  e.preventDefault();
  showToast(`✅ Đã ${type === 'in' ? 'nhập' : 'xuất'} kho thành công!`);
  e.target.reset();
}

// ============ QR SCANNER (Driver) ============
function initQRScanner() {
  const scannerDiv = document.getElementById('qr-reader');
  if (!scannerDiv) return;

  // Mock QR scan - in real app would use html5-qrcode
  scannerDiv.innerHTML = `
    <div style="text-align:center;padding:2rem;background:#f1f5f9;border-radius:8px">
      <div style="font-size:3rem;margin-bottom:.5rem">📷</div>
      <p style="font-weight:600;margin-bottom:.5rem">Camera QR Scanner</p>
      <p style="font-size:.8rem;color:#64748b;margin-bottom:1rem">Hướng camera vào mã QR của hộ dân</p>
      <button class="btn btn-teal btn-sm" onclick="mockQRScan()">📱 Quét thử (Mock)</button>
    </div>`;
}

function mockQRScan() {
  showToast('✅ Đã quét QR: Hộ dân Nguyễn Văn A - Xã Hòa Bình');
}

// ============ FILE UPLOAD PREVIEW ============
function handleFileUpload(input, previewId) {
  const preview = document.getElementById(previewId);
  if (!input.files?.length || !preview) return;

  preview.innerHTML = '';
  Array.from(input.files).forEach(file => {
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = e => {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.style.cssText = 'width:80px;height:80px;object-fit:cover;border-radius:8px;margin:.25rem';
        preview.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  });
}

// ============ COUNTER ANIMATION ============
function animateCounters() {
  document.querySelectorAll('[data-count]').forEach(el => {
    const target = parseInt(el.getAttribute('data-count'));
    const duration = 2000;
    const step = target / (duration / 16);
    let current = 0;

    const timer = setInterval(() => {
      current += step;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = Math.floor(current).toLocaleString('vi-VN');
    }, 16);
  });
}

// ============ SMOOTH SCROLL ============
document.addEventListener('DOMContentLoaded', () => {
  // Animate counters when visible
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        animateCounters();
        observer.disconnect();
      }
    });
  });
  const statsRow = document.querySelector('.stats-row');
  if (statsRow) observer.observe(statsRow);

  // Smooth scroll for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      e.preventDefault();
      const target = document.querySelector(a.getAttribute('href'));
      if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
  });

  // Initialize QR scanner if on driver page
  initQRScanner();
});
