@extends('layouts.app')
@section('title', 'Thêm Nhu yếu phẩm - ĐẠI PHÚC')

@section('content')
<div class="dash-layout" x-data="{ sidebarOpen: true }">

  {{-- SIDEBAR --}}
  @include('partials.admin-sidebar', ['activeMenu' => 'supplies-create'])

  {{-- MAIN --}}
  <main class="dash-main">
    @include('partials.dashboard-header', ['pageTitle' => '➕ Thêm Nhu yếu phẩm'])
    <div style="padding:1.5rem">

      {{-- BREADCRUMB --}}
      <div style="font-size:.83rem;color:#9ca3af;margin-bottom:1.25rem">
        <a href="{{ route('admin.supplies.index') }}" style="color:#6366f1;text-decoration:none">Nhu yếu phẩm</a>
        &rsaquo; Thêm mới
      </div>

      {{-- FORM CARD --}}
      <div class="chart-container" style="max-width:680px">
        <h3 style="margin-bottom:1.5rem">📝 Thông tin nhu yếu phẩm</h3>

        <form action="{{ route('admin.supplies.store') }}" method="POST">
          @csrf

          {{-- Danh mục --}}
          <div style="margin-bottom:1.1rem">
            <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
              Danh mục <span style="color:#ef4444">*</span>
            </label>
            <select name="category_id"
                    style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('category_id') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;background:#fff;outline:none">
              <option value="">— Chọn danh mục —</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                  {{ $cat->name }}
                </option>
              @endforeach
            </select>
            @error('category_id')
              <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
            @enderror
          </div>

          {{-- Tên --}}
          <div style="margin-bottom:1.1rem">
            <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
              Tên nhu yếu phẩm <span style="color:#ef4444">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name') }}"
                   placeholder="Ví dụ: Mì gói, Nước suối..."
                   style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('name') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;outline:none;box-sizing:border-box">
            @error('name')
              <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
            @enderror
          </div>

          {{-- Đơn vị + Mức cảnh báo (2 cột) --}}
          <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.1rem">
            <div>
              <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
                Đơn vị tính <span style="color:#ef4444">*</span>
              </label>
              <input type="text" name="unit" value="{{ old('unit') }}"
                     placeholder="Ví dụ: thùng, kg, lít..."
                     style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('unit') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;outline:none;box-sizing:border-box">
              @error('unit')
                <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
              @enderror
            </div>
            <div>
              <label style="display:block;font-size:.88rem;font-weight:600;color:#374151;margin-bottom:.4rem">
                Mức cảnh báo tồn kho <span style="color:#ef4444">*</span>
              </label>
              <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', 0) }}" min="0"
                     style="width:100%;padding:.6rem .8rem;border:1px solid {{ $errors->has('min_stock_alert') ? '#ef4444' : '#d1d5db' }};border-radius:8px;font-size:.9rem;outline:none;box-sizing:border-box">
              @error('min_stock_alert')
                <p style="color:#ef4444;font-size:.8rem;margin-top:.3rem">{{ $message }}</p>
              @enderror
            </div>
          </div>

          {{-- ACTIONS --}}
          <div style="display:flex;gap:.75rem;margin-top:1.75rem">
            <button type="submit" class="btn btn-primary">✅ Lưu nhu yếu phẩm</button>
            <a href="{{ route('admin.supplies.index') }}" class="btn btn-outline">Huỷ</a>
          </div>
        </form>
      </div>

    </div>
  </main>
</div>
@endsection
