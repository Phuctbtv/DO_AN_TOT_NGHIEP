@extends('layouts.app')
@section('title', 'Thêm Kho hàng - ĐẠI PHÚC')

@push('styles')
<style>
  #map-picker {
    width: 100%;
    height: 340px;
    border-radius: 0 0 10px 10px;
    border: 2px solid #e5e7eb;
    border-top: none;
    z-index: 0;
    transition: border-color .2s;
  }
  .map-wrapper:hover #map-picker { border-color: #6366f1; }
  .map-wrapper:hover .map-search-bar { border-color: #6366f1; }

  /* Search bar nằm trên map */
  .map-search-bar {
    display: flex;
    gap: .5rem;
    align-items: center;
    padding: .55rem .75rem;
    background: #fff;
    border: 2px solid #e5e7eb;
    border-bottom: 1px solid #e5e7eb;
    border-radius: 10px 10px 0 0;
    transition: border-color .2s;
    position: relative;
    z-index: 1;
  }
  .map-search-input {
    flex: 1;
    border: none;
    outline: none;
    font-size: .88rem;
    color: #374151;
    background: transparent;
  }
  .map-search-input::placeholder { color: #9ca3af; }
  .map-search-btn {
    padding: .35rem .8rem;
    background: #6366f1;
    color: #fff;
    border: none;
    border-radius: 6px;
    font-size: .82rem;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s;
  }
  .map-search-btn:hover { background: #4f46e5; }
  .map-search-btn:disabled { background: #a5b4fc; cursor: not-allowed; }

  /* Dropdown gợi ý */
  .map-suggestions {
    position: absolute;
    top: calc(100% + 2px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,.12);
    z-index: 9999;
    max-height: 240px;
    overflow-y: auto;
    display: none;
  }
  .map-suggestion-item {
    padding: .6rem 1rem;
    font-size: .84rem;
    color: #374151;
    cursor: pointer;
    border-bottom: 1px solid #f3f4f6;
    line-height: 1.4;
  }
  .map-suggestion-item:last-child { border-bottom: none; }
  .map-suggestion-item:hover { background: #eff6ff; color: #1d4ed8; }
  .map-suggestion-item .suggestion-name { font-weight: 600; }
  .map-suggestion-item .suggestion-detail { font-size: .78rem; color: #9ca3af; }

  .map-hint {
    font-size: .8rem;
    color: #6b7280;
    margin-top: .4rem;
    margin-bottom: 1.1rem;
    display: flex;
    align-items: center;
    gap: .35rem;
  }
  .coord-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
    margin-bottom: 1.1rem;
  }
</style>
@endpush

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- SIDEBAR --}}
  @include('partials.admin-sidebar', ['activeMenu' => 'warehouses'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '➕ Thêm Kho hàng mới'])
    <div style="padding:1.5rem">

      {{-- BREADCRUMB --}}
      <div style="font-size:.83rem;color:#9ca3af;margin-bottom:1.25rem">
        <a href="{{ route('admin.warehouses.index') }}" style="color:#6366f1;text-decoration:none">Kho hàng</a>
        &rsaquo; Thêm mới
      </div>

      {{-- FORM CARD --}}
      <div class="chart-container" style="max-width:760px">
        <h3 style="margin-bottom:1.5rem">🏭 Thông tin kho hàng</h3>

        <form action="{{ route('admin.warehouses.store') }}" method="POST">
          @csrf

          {{-- Tên kho --}}
          <div style="margin-bottom:1.1rem">
            <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
              Tên kho <span style="color:#ef4444">*</span>
            </label>
            <input type="text" name="name" id="name" value="{{ old('name') }}"
                   placeholder="Ví dụ: Kho Bình Thạnh, Kho Trung tâm..."
                   style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('name') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;outline:none;box-sizing:border-box">
            @error('name')
              <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
            @enderror
          </div>

          {{-- Địa chỉ --}}
          <div style="margin-bottom:1.1rem">
            <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
              Địa chỉ <span style="color:#ef4444">*</span>
            </label>
            <textarea name="address" id="address" rows="2"
                      placeholder="Nhập địa chỉ hoặc tìm kiếm trên bản đồ bên dưới..."
                      style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('address') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;outline:none;box-sizing:border-box;resize:vertical">{{ old('address') }}</textarea>
            @error('address')
              <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
            @enderror
          </div>

          {{-- MAP PICKER --}}
          <div style="margin-bottom:.5rem">
            <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.5rem">
              📍 Chọn vị trí trên bản đồ
              <span style="font-size:.78rem;font-weight:400;color:#9ca3af">tùy chọn</span>
            </label>

            {{-- Wrapper bao gồm search bar + map --}}
            <div class="map-wrapper">
              {{-- Search bar --}}
              <div class="map-search-bar" style="position:relative">
                <span style="font-size:1rem">🔍</span>
                <input type="text" id="map-search-input" class="map-search-input"
                       placeholder="Tìm địa điểm... Ví dụ: Chợ Bến Thành, Quận 1"
                       autocomplete="off">
                <button type="button" id="map-search-btn" class="map-search-btn">Tìm</button>
                <div id="map-suggestions" class="map-suggestions"></div>
              </div>

              {{-- Map --}}
              <div id="map-picker"></div>
            </div>

            <p class="map-hint">
              💡 Gõ tên địa điểm rồi nhấn <strong>Tìm</strong>, hoặc <strong>click trực tiếp</strong> lên bản đồ.
              Kéo marker để điều chỉnh chính xác hơn.
            </p>
          </div>

          {{-- Lat + Lng (2 cột) --}}
          <div class="coord-row">
            <div>
              <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
                Vĩ độ (Lat)
              </label>
              <input type="number" name="lat" id="lat" value="{{ old('lat') }}"
                     placeholder="Ví dụ: 10.762622"
                     step="any" min="-90" max="90"
                     style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('lat') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;outline:none;box-sizing:border-box;background:#f9fafb">
              @error('lat')
                <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
                Kinh độ (Lng)
              </label>
              <input type="number" name="lng" id="lng" value="{{ old('lng') }}"
                     placeholder="Ví dụ: 106.682762"
                     step="any" min="-180" max="180"
                     style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('lng') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;outline:none;box-sizing:border-box;background:#f9fafb">
              @error('lng')
                <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
              @enderror
            </div>
          </div>

          {{-- Quản lý kho --}}
          <div style="margin-bottom:1.1rem">
            <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
              Quản lý kho
              <span style="font-size:.78rem;font-weight:400;color:#9ca3af">tùy chọn</span>
            </label>
            <select name="manager_id"
                    style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('manager_id') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;background:#fff;outline:none">
              <option value="">— Chưa phân công —</option>
              @foreach($managers as $manager)
                <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                  {{ $manager->name }} ({{ $manager->email }})
                </option>
              @endforeach
            </select>
            @error('manager_id')
              <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
            @enderror
            @if($managers->isEmpty())
              <p style="color:#f59e0b;font-size:.8rem;margin-top:.3rem">
                ⚠️ Chưa có tài khoản quản lý kho. Hãy tạo user với role <strong>warehouse_manager</strong> trước.
              </p>
            @endif
          </div>

          {{-- ACTIONS --}}
          <div style="display:flex;gap:.75rem;margin-top:1.75rem">
            <button type="submit" class="btn btn-primary">✅ Lưu kho</button>
            <a href="{{ route('admin.warehouses.index') }}" class="btn btn-outline">Huỷ</a>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>

@push('scripts')
<script>
(function () {
  /* ========== KHỞI TẠO BẢN ĐỒ ========== */
  const DEFAULT_LAT = 10.7769, DEFAULT_LNG = 106.7009, DEFAULT_ZOOM = 13;
  const initLat = parseFloat(document.getElementById('lat').value) || DEFAULT_LAT;
  const initLng = parseFloat(document.getElementById('lng').value) || DEFAULT_LNG;

  const map = L.map('map-picker').setView([initLat, initLng], DEFAULT_ZOOM);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    maxZoom: 19,
  }).addTo(map);

  const markerIcon = L.icon({
    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
    iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41],
  });

  let marker = null;

  /* ========== ĐẶT MARKER ========== */
  function placeMarker(lat, lng, zoom) {
    lat = parseFloat(parseFloat(lat).toFixed(7));
    lng = parseFloat(parseFloat(lng).toFixed(7));
    document.getElementById('lat').value = lat;
    document.getElementById('lng').value = lng;

    if (marker) {
      marker.setLatLng([lat, lng]);
    } else {
      marker = L.marker([lat, lng], { icon: markerIcon, draggable: true }).addTo(map);
      marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        placeMarker(pos.lat, pos.lng);
        reverseGeocode(pos.lat, pos.lng);
      });
    }
    if (zoom) map.setView([lat, lng], zoom);
    else map.panTo([lat, lng]);
  }

  /* ========== REVERSE GEOCODING ========== */
  function reverseGeocode(lat, lng) {
    fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=vi`)
      .then(r => r.json())
      .then(data => {
        if (data && data.display_name) {
          const addrField = document.getElementById('address');
          if (!addrField.value.trim()) addrField.value = data.display_name;
        }
      }).catch(() => {});
  }

  /* ========== CLICK BẢN ĐỒ ========== */
  map.on('click', function (e) {
    placeMarker(e.latlng.lat, e.latlng.lng);
    reverseGeocode(e.latlng.lat, e.latlng.lng);
  });

  /* ========== MARKER KHỞI TẠO ========== */
  const existingLat = parseFloat(document.getElementById('lat').value);
  const existingLng = parseFloat(document.getElementById('lng').value);
  if (!isNaN(existingLat) && !isNaN(existingLng)) placeMarker(existingLat, existingLng);

  /* ========== NHẬP TAY LAT/LNG ========== */
  ['lat', 'lng'].forEach(id => {
    document.getElementById(id).addEventListener('change', function () {
      const lat = parseFloat(document.getElementById('lat').value);
      const lng = parseFloat(document.getElementById('lng').value);
      if (!isNaN(lat) && !isNaN(lng)) placeMarker(lat, lng);
    });
  });

  /* ========== TÌM KIẾM ĐỊA ĐIỂM ========== */
  const searchInput   = document.getElementById('map-search-input');
  const searchBtn     = document.getElementById('map-search-btn');
  const suggestionsEl = document.getElementById('map-suggestions');
  let debounceTimer   = null;
  let currentResults  = [];

  function showSuggestions(results) {
    currentResults = results;
    suggestionsEl.innerHTML = '';
    if (!results.length) {
      suggestionsEl.innerHTML = '<div class="map-suggestion-item" style="color:#9ca3af">Không tìm thấy địa điểm nào.</div>';
      suggestionsEl.style.display = 'block';
      return;
    }
    results.forEach((item, idx) => {
      const div = document.createElement('div');
      div.className = 'map-suggestion-item';
      const parts = item.display_name.split(', ');
      const name   = parts.slice(0, 2).join(', ');
      const detail = parts.slice(2).join(', ');
      div.innerHTML = `<div class="suggestion-name">${name}</div><div class="suggestion-detail">${detail}</div>`;
      div.addEventListener('click', function () {
        pickSuggestion(idx);
      });
      suggestionsEl.appendChild(div);
    });
    suggestionsEl.style.display = 'block';
  }

  function hideSuggestions() {
    suggestionsEl.style.display = 'none';
  }

  function pickSuggestion(idx) {
    const item = currentResults[idx];
    const lat = parseFloat(item.lat);
    const lng  = parseFloat(item.lon);
    placeMarker(lat, lng, 16);
    // Cập nhật địa chỉ
    document.getElementById('address').value = item.display_name;
    searchInput.value = item.display_name.split(', ').slice(0, 2).join(', ');
    hideSuggestions();
  }

  function doSearch(query) {
    if (!query.trim()) return;
    searchBtn.disabled = true;
    searchBtn.textContent = '...';
    hideSuggestions();

    fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=6&accept-language=vi&countrycodes=vn`)
      .then(r => r.json())
      .then(data => {
        searchBtn.disabled = false;
        searchBtn.textContent = 'Tìm';
        if (data && data.length > 0) {
          if (data.length === 1) {
            // Chỉ 1 kết quả → chọn luôn
            pickSuggestion(0), currentResults = data;
            pickSuggestion(0);
          } else {
            showSuggestions(data);
          }
        } else {
          showSuggestions([]);
        }
      })
      .catch(() => {
        searchBtn.disabled = false;
        searchBtn.textContent = 'Tìm';
      });
  }

  // Nút tìm
  searchBtn.addEventListener('click', () => doSearch(searchInput.value));

  // Enter → tìm
  searchInput.addEventListener('keydown', function (e) {
    if (e.key === 'Enter') { e.preventDefault(); doSearch(this.value); }
  });

  // Gõ tự động → debounce 400ms → tìm kiếm
  searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    if (this.value.length < 3) { hideSuggestions(); return; }
    debounceTimer = setTimeout(() => doSearch(this.value), 400);
  });

  // Click ngoài → ẩn gợi ý
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.map-search-bar')) hideSuggestions();
  });
})();
</script>
@endpush
@endsection
