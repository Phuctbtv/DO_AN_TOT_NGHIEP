@extends('layouts.app')
@section('title', 'Nhập kho - ĐẠI PHÚC')

@push('styles')
<style>
  .form-card {
    background: #fff;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 24px rgba(0,0,0,.06);
    max-width: 720px;
    margin: 0 auto;
  }
  .form-card-header {
    background: linear-gradient(135deg, #0d9488 0%, #0891b2 100%);
    padding: 1.5rem 2rem;
    color: #fff;
    border-radius: 16px 16px 0 0;
  }
  .form-card-header h2 { font-size: 1.25rem; font-weight: 700; margin: 0; }
  .form-card-header p  { font-size: .85rem; opacity: .85; margin: .25rem 0 0; }

  .form-card-body { padding: 2rem; }

  .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
  .form-grid .full { grid-column: 1 / -1; }

  .field-label {
    display: block;
    font-size: .82rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: .4rem;
    text-transform: uppercase;
    letter-spacing: .4px;
  }
  .field-label .req { color: #ef4444; margin-left: 2px; }

  .field-input, .field-select {
    width: 100%;
    padding: .65rem 1rem;
    border: 1.5px solid #d1d5db;
    border-radius: 8px;
    font-size: .92rem;
    font-family: inherit;
    color: #1e293b;
    transition: border-color .2s, box-shadow .2s;
    background: #fff;
    outline: none;
  }
  .field-input:focus, .field-select:focus {
    border-color: #0d9488;
    box-shadow: 0 0 0 3px rgba(13,148,136,.12);
  }
  .field-input.error, .field-select.error { border-color: #ef4444; }
  .error-msg { color: #ef4444; font-size: .78rem; margin-top: .25rem; }

  /* Upload zone */
  .upload-zone {
    border: 2px dashed #d1d5db;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all .2s;
    position: relative;
  }
  .upload-zone:hover { border-color: #0d9488; background: #f0fdfa; }
  .upload-zone.dragging { border-color: #0d9488; background: #f0fdfa; transform: scale(1.01); }
  .upload-zone input[type="file"] {
    position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; height: 100%;
    z-index: 2;
  }
  .upload-zone .icon { font-size: 2rem; margin-bottom: .5rem; }
  .upload-zone .hint { font-size: .82rem; color: #64748b; }
  .upload-zone .filename { margin-top: .5rem; font-size: .82rem; color: #0d9488; font-weight: 600; display: none; }

  /* Image preview */
  .img-preview {
    margin-top: .75rem;
    border-radius: 8px;
    overflow: hidden;
    display: none;
    border: 2px solid #e2e8f0;
  }
  .img-preview img { width: 100%; max-height: 200px; object-fit: cover; display: block; }

  /* Submit btn */
  .submit-btn {
    width: 100%;
    padding: .85rem;
    background: linear-gradient(135deg, #0d9488, #0891b2);
    color: #fff;
    font-size: 1rem;
    font-weight: 700;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: .6rem;
    font-family: inherit;
    transition: all .2s;
    box-shadow: 0 4px 14px rgba(13,148,136,.3);
    margin-top: 1.5rem;
  }
  .submit-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(13,148,136,.4);
  }
  .submit-btn:active { transform: translateY(0); }

  @media (max-width: 640px) {
    .form-grid { grid-template-columns: 1fr; }
    .form-card-body { padding: 1.25rem; }
  }
</style>
@endpush

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- SIDEBAR --}}
  @include('partials.warehouse-sidebar', ['activeMenu' => 'stock_ins-create'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '📥 Nhập kho'])

    <div style="padding:1.5rem">

      {{-- BREADCRUMB --}}
      <div style="display:flex;align-items:center;gap:.5rem;font-size:.83rem;color:#64748b;margin-bottom:1.5rem">
        <a href="{{ route('warehouse.dashboard') }}" style="color:#0d9488;text-decoration:none">Dashboard</a>
        <span>›</span>
        <a href="{{ route('warehouse.stock_ins.index') }}" style="color:#0d9488;text-decoration:none">Lịch sử nhập kho</a>
        <span>›</span>
        <span style="color:#1e293b;font-weight:600">Tạo phiếu nhập</span>
      </div>

      {{-- FLASH ERRORS --}}
      @if($errors->any())
        <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem">
          <div style="font-weight:700;margin-bottom:.5rem">⚠️ Vui lòng kiểm tra lại:</div>
          <ul style="margin:0;padding-left:1.25rem;font-size:.88rem">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      {{-- FORM CARD --}}
      <div class="form-card">
        <div class="form-card-header">
          <h2>📥 Phiếu nhập kho</h2>
          <p>Điền đầy đủ thông tin để tạo phiếu nhập hàng hóa vào kho</p>
        </div>

        <div class="form-card-body">
          <form action="{{ route('warehouse.stock_ins.store') }}" method="POST" enctype="multipart/form-data" id="stockInForm">
            @csrf

            <div class="form-grid">

              {{-- Kho nhận hàng --}}
              <div class="full">
                <label class="field-label">🏭 Kho nhận hàng</label>

                @if($myWarehouse)
                  {{-- Chỉ 1 kho: hiển thị thẻ thông tin, auto-fill hidden input --}}
                  <div style="
                    display:flex;align-items:center;gap:1rem;
                    padding:1rem 1.25rem;
                    background:linear-gradient(135deg,#f0fdfa,#e0f2fe);
                    border:2px solid #5eead4;
                    border-radius:10px;
                  ">
                    <div style="font-size:1.8rem">🏭</div>
                    <div style="flex:1">
                      <div style="font-weight:700;font-size:1rem;color:#0f172a">{{ $myWarehouse->name }}</div>
                      <div style="font-size:.8rem;color:#64748b;margin-top:.15rem">📍 {{ $myWarehouse->address }}</div>
                    </div>
                    <div style="background:#0d9488;color:#fff;font-size:.72rem;font-weight:700;padding:.25rem .65rem;border-radius:20px;white-space:nowrap">
                      ✓ Kho của bạn
                    </div>
                  </div>
                  <input type="hidden" name="warehouse_id" value="{{ $myWarehouse->id }}">

                @elseif($warehouses->isEmpty())
                  {{-- Chưa được phân công kho nào --}}
                  <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:10px;padding:1rem 1.25rem;color:#b91c1c;font-size:.9rem">
                    ⚠️ Tài khoản của bạn chưa được phân công quản lý kho nào. Vui lòng liên hệ Admin.
                  </div>

                @else
                  {{-- Quản lý nhiều kho: mới dùng dropdown --}}
                  <select id="warehouse_id" name="warehouse_id"
                          class="field-select @error('warehouse_id') error @enderror" required>
                    <option value="">-- Chọn kho --</option>
                    @foreach($warehouses as $wh)
                      <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>
                        {{ $wh->name }} — {{ $wh->address }}
                      </option>
                    @endforeach
                  </select>
                  @error('warehouse_id')
                    <div class="error-msg">{{ $message }}</div>
                  @enderror
                @endif
              </div>

              {{-- Chọn nhu yếu phẩm --}}
              <div class="full">
                <label class="field-label" for="supply_id">📦 Nhu yếu phẩm <span class="req">*</span></label>
                <select id="supply_id" name="supply_id"
                        class="field-select @error('supply_id') error @enderror" required>
                  <option value="">-- Chọn mặt hàng --</option>
                  @foreach($supplies as $supply)
                    <option value="{{ $supply->id }}" {{ old('supply_id') == $supply->id ? 'selected' : '' }}>
                      {{ $supply->name }} ({{ $supply->unit }})
                    </option>
                  @endforeach
                </select>
                @error('supply_id')
                  <div class="error-msg">{{ $message }}</div>
                @enderror
              </div>

              {{-- Số lượng --}}
              <div>
                <label class="field-label" for="quantity">🔢 Số lượng <span class="req">*</span></label>
                <input id="quantity" type="number" name="quantity" min="1"
                       value="{{ old('quantity') }}"
                       placeholder="VD: 500"
                       class="field-input @error('quantity') error @enderror" required>
                @error('quantity')
                  <div class="error-msg">{{ $message }}</div>
                @enderror
              </div>

              {{-- Ngày nhập --}}
              <div>
                <label class="field-label" for="received_date">📅 Ngày nhập <span class="req">*</span></label>
                <input id="received_date" type="date" name="received_date"
                       value="{{ old('received_date', date('Y-m-d')) }}"
                       class="field-input @error('received_date') error @enderror" required>
                @error('received_date')
                  <div class="error-msg">{{ $message }}</div>
                @enderror
              </div>

              {{-- Nguồn tài trợ --}}
              <div class="full">
                <label class="field-label" for="donor_info">🤝 Nguồn tài trợ</label>
                <input id="donor_info" type="text" name="donor_info"
                       value="{{ old('donor_info') }}"
                       placeholder="VD: Công ty TNHH ABC, Nhà hảo tâm Nguyễn Văn A..."
                       class="field-input @error('donor_info') error @enderror">
                @error('donor_info')
                  <div class="error-msg">{{ $message }}</div>
                @enderror
              </div>

              {{-- Upload ảnh --}}
              <div class="full">
                <label class="field-label">🖼️ Ảnh hàng hóa</label>
                <div class="upload-zone" id="uploadZone">
                  <input type="file" name="image" id="imageInput"
                         accept="image/jpg,image/jpeg,image/png,image/webp"
                         onchange="previewImage(event)">
                  <div class="icon">📷</div>
                  <div style="font-weight:600;color:#374151;margin-bottom:.25rem">Nhấp hoặc kéo thả ảnh vào đây</div>
                  <div class="hint">JPG, PNG, WEBP — Tối đa 5MB</div>
                  <div class="filename" id="fileName"></div>
                </div>
                <div class="img-preview" id="imgPreview">
                  <img id="previewImg" src="" alt="Preview">
                </div>
                @error('image')
                  <div class="error-msg">{{ $message }}</div>
                @enderror
              </div>

            </div>{{-- end grid --}}

            <button type="submit" class="submit-btn" id="submitBtn">
              <span id="submitIcon">💾</span>
              <span id="submitText">Lưu phiếu nhập kho</span>
            </button>

          </form>
        </div>
      </div>{{-- end form-card --}}

    </div>
  </main>
</div>
@endsection

@push('scripts')
<script>
function previewImage(event) {
  const file = event.target.files[0];
  if (!file) return;

  // Validation client-side
  const maxSize = 5 * 1024 * 1024;
  if (file.size > maxSize) {
    alert('⚠️ Ảnh không được vượt quá 5MB!');
    event.target.value = '';
    return;
  }

  document.getElementById('fileName').style.display = 'block';
  document.getElementById('fileName').textContent = '📎 ' + file.name;

  const reader = new FileReader();
  reader.onload = (e) => {
    document.getElementById('previewImg').src = e.target.result;
    document.getElementById('imgPreview').style.display = 'block';
  };
  reader.readAsDataURL(file);
}

// Submit spinner
document.getElementById('stockInForm').addEventListener('submit', function() {
  const btn  = document.getElementById('submitBtn');
  const icon = document.getElementById('submitIcon');
  const text = document.getElementById('submitText');
  btn.disabled  = true;
  btn.style.opacity = '.75';
  icon.textContent = '⏳';
  text.textContent = 'Đang lưu...';
});
</script>
@endpush
