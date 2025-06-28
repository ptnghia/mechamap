@extends('admin.layouts.dason')

@section('title', 'Cài Đặt Thanh Toán')

@section('page-title')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0 font-size-18">Cài Đặt Thanh Toán</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">MechaMap</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.settings.general') }}">Cài Đặt</a></li>
                    <li class="breadcrumb-item active">Thanh Toán</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-md-3 mb-4">
        @include('admin.settings.partials.sidebar')
    </div>

    <div class="col-md-9">
        <!-- Payment Methods -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Phương Thức Thanh Toán</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="mdi mdi-information-outline me-2"></i>
                    Cấu hình các phương thức thanh toán cho marketplace MechaMap
                </div>

                <form action="{{ route('admin.settings.payment.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- VNPay Configuration -->
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="mdi mdi-credit-card me-2"></i>
                                    VNPay
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="vnpay_enabled" name="vnpay_enabled" value="1">
                                    <label class="form-check-label" for="vnpay_enabled">Kích hoạt</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vnpay_merchant_id" class="form-label">Merchant ID</label>
                                        <input type="text" class="form-control" id="vnpay_merchant_id" name="vnpay_merchant_id" placeholder="Nhập Merchant ID">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="vnpay_secret_key" class="form-label">Secret Key</label>
                                        <input type="password" class="form-control" id="vnpay_secret_key" name="vnpay_secret_key" placeholder="Nhập Secret Key">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="vnpay_environment" class="form-label">Môi Trường</label>
                                <select class="form-select" id="vnpay_environment" name="vnpay_environment">
                                    <option value="sandbox">Sandbox (Test)</option>
                                    <option value="production">Production (Live)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- MoMo Configuration -->
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="mdi mdi-wallet me-2"></i>
                                    MoMo
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="momo_enabled" name="momo_enabled" value="1">
                                    <label class="form-check-label" for="momo_enabled">Kích hoạt</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="momo_partner_code" class="form-label">Partner Code</label>
                                        <input type="text" class="form-control" id="momo_partner_code" name="momo_partner_code" placeholder="Nhập Partner Code">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="momo_access_key" class="form-label">Access Key</label>
                                        <input type="text" class="form-control" id="momo_access_key" name="momo_access_key" placeholder="Nhập Access Key">
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="momo_secret_key" class="form-label">Secret Key</label>
                                <input type="password" class="form-control" id="momo_secret_key" name="momo_secret_key" placeholder="Nhập Secret Key">
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer -->
                    <div class="card border mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="mdi mdi-bank me-2"></i>
                                    Chuyển Khoản Ngân Hàng
                                </h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="bank_transfer_enabled" name="bank_transfer_enabled" value="1">
                                    <label class="form-check-label" for="bank_transfer_enabled">Kích hoạt</label>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bank_name" class="form-label">Tên Ngân Hàng</label>
                                        <input type="text" class="form-control" id="bank_name" name="bank_name" placeholder="VD: Vietcombank">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bank_account_number" class="form-label">Số Tài Khoản</label>
                                        <input type="text" class="form-control" id="bank_account_number" name="bank_account_number" placeholder="Nhập số tài khoản">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bank_account_name" class="form-label">Tên Chủ Tài Khoản</label>
                                        <input type="text" class="form-control" id="bank_account_name" name="bank_account_name" placeholder="Nhập tên chủ tài khoản">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="bank_branch" class="form-label">Chi Nhánh</label>
                                        <input type="text" class="form-control" id="bank_branch" name="bank_branch" placeholder="VD: Chi nhánh Hà Nội">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Lưu Cài Đặt
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Payment Settings -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Cài Đặt Chung</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.payment.general') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="default_currency" class="form-label">Đơn Vị Tiền Tệ</label>
                                <select class="form-select" id="default_currency" name="default_currency">
                                    <option value="VND">VND - Việt Nam Đồng</option>
                                    <option value="USD">USD - US Dollar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="commission_rate" class="form-label">Tỷ Lệ Hoa Hồng (%)</label>
                                <input type="number" class="form-control" id="commission_rate" name="commission_rate" min="0" max="100" step="0.1" placeholder="VD: 5.0">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="min_withdrawal" class="form-label">Số Tiền Rút Tối Thiểu</label>
                                <input type="number" class="form-control" id="min_withdrawal" name="min_withdrawal" min="0" placeholder="VD: 100000">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_withdrawal" class="form-label">Số Tiền Rút Tối Đa</label>
                                <input type="number" class="form-control" id="max_withdrawal" name="max_withdrawal" min="0" placeholder="VD: 50000000">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="auto_approval" name="auto_approval" value="1">
                            <label class="form-check-label" for="auto_approval">
                                Tự động duyệt giao dịch dưới 1,000,000 VND
                            </label>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="mdi mdi-content-save me-1"></i> Lưu Cài Đặt
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
// Toggle payment method configurations
document.querySelectorAll('.form-check-input').forEach(function(checkbox) {
    checkbox.addEventListener('change', function() {
        const cardBody = this.closest('.card').querySelector('.card-body');
        const inputs = cardBody.querySelectorAll('input, select');
        
        inputs.forEach(function(input) {
            if (input !== checkbox) {
                input.disabled = !checkbox.checked;
            }
        });
    });
});
</script>
@endsection
