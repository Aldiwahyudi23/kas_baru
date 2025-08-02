@extends('user.layout.app')

@section('content')
<div class="card">
    <div class="card-header bg-primary">
        <h3 class="card-title text-white">Form Pemindahan Dana</h3>
    </div>
    
    <form action="{{ route('bank.transfer.submit') }}" method="POST">
        @csrf
        <div class="card-body">
            <!-- Informasi Rekening Sumber -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <h4 class="mb-3">Rekening Sumber</h4>
                    <div class="alert alert-light">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Bank:</strong>
                                <p>{{ $sourceAccount->bank_name }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>No. Rekening:</strong>
                                <p>{{ $sourceAccount->account_number }}</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Pemilik:</strong>
                                <p>{{ $sourceAccount->warga->name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <strong>Saldo Tersedia:</strong>
                                <h4 class="text-success">Rp {{ number_format($sourceAccount->latestBalance->balance ?? 0, 2, ',', '.') }}</h4>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="source_account_id" value="{{ $sourceAccount->id }}">
                </div>
            </div>

            <!-- Form Transfer -->
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="destination_account_id">Rekening Tujuan</label>
                        <select class="form-control select2bs4 @error('destination_account_id') is-invalid @enderror" 
                            id="destination_account_id" name="destination_account_id" required>
                            <option value="">-- Pilih Rekening Tujuan --</option>
                            @foreach($destinationAccounts as $account)
                            <option value="{{ $account->id }}" 
                                {{ old('destination_account_id') == $account->id ? 'selected' : '' }}>
                                {{ $account->bank_name }} - {{ $account->account_number }} ({{ $account->account_holder_name }})
                            </option>
                            @endforeach
                        </select>
                        @error('destination_account_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
               
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="amount">Jumlah Transfer</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                id="amount" name="amount" min="1000" step="1000" 
                                value="{{ old('amount') }}" required>
                        </div>
                        <small class="text-muted">Minimal transfer Rp 1.000</small>
                        @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="admin_fee">Biaya Admin</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" class="form-control @error('admin_fee') is-invalid @enderror" 
                                id="admin_fee" name="admin_fee" min="0" step="1000" 
                                value="{{ old('admin_fee', 0) }}">
                        </div>
                        @error('admin_fee')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="description">Keterangan</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                    id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                @error('description')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="alert alert-info">
                <h5>Ringkasan Transfer</h5>
                <div class="row">
                    <div class="col-md-6">
                        <p>Jumlah Transfer: <span id="summary_amount">Rp 0</span></p>
                        <p>Biaya Admin: <span id="summary_fee">Rp 0</span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="font-weight-bold">Total Dibebankan: <span id="summary_total">Rp 0</span></p>
                        <p>Saldo Setelah Transfer: <span id="summary_balance">Rp {{ number_format($sourceAccount->latestBalance->balance ?? 0, 2, ',', '.') }}</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane"></i> Proses Transfer
            </button>
           
        </div>
    </form>
</div>

@section('scripts')
<script>
$(document).ready(function() {
    // Inisialisasi select2
    $('.select2bs4').select2({
        theme: 'bootstrap4'
    });

    // Hitung ringkasan transfer
    function calculateSummary() {
        const amount = parseFloat($('#amount').val()) || 0;
        const fee = parseFloat($('#admin_fee').val()) || 0;
        const currentBalance = parseFloat("{{ $sourceAccount->latestBalance->balance ?? 0 }}");
        const total = amount + fee;
        const newBalance = currentBalance - total;

        // Update ringkasan
        $('#summary_amount').text('Rp ' + amount.toLocaleString('id-ID'));
        $('#summary_fee').text('Rp ' + fee.toLocaleString('id-ID'));
        $('#summary_total').text('Rp ' + total.toLocaleString('id-ID'));
        $('#summary_balance').text('Rp ' + newBalance.toLocaleString('id-ID'));

        // Validasi saldo
        if (newBalance < 0) {
            $('#summary_balance').addClass('text-danger');
        } else {
            $('#summary_balance').removeClass('text-danger');
        }
    }

    // Hitung saat nilai berubah
    $('#amount, #admin_fee').on('input', calculateSummary);

    // Validasi form
    $('form').submit(function(e) {
        const amount = parseFloat($('#amount').val()) || 0;
        const fee = parseFloat($('#admin_fee').val()) || 0;
        const currentBalance = parseFloat("{{ $sourceAccount->latestBalance->balance ?? 0 }}");
        const total = amount + fee;

        if (total > currentBalance) {
            alert('Saldo tidak mencukupi untuk melakukan transfer ini');
            e.preventDefault();
            return false;
        }

        if ($('#destination_account_id').val() === "{{ $sourceAccount->id }}") {
            alert('Rekening sumber dan tujuan tidak boleh sama');
            e.preventDefault();
            return false;
        }
    });
});
</script>
@endsection
@endsection