@extends('user.layout.app')

@section('content')
<style>
    .transaction-container {
        max-width: 600px;
        margin: 10px auto;
        padding: 20px;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    }

    .transaction-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .transaction-header h2 {
        margin: 0;
        font-size: 24px;
        color: #007bff;
    }

    .range-selection {
        text-align: center;
        margin-bottom: 20px;
    }

    .range-buttons {
        display: flex;
        justify-content: space-around;
        flex-wrap: wrap;
    }

    .range-btn {
        padding: 10px 20px;
        margin: 5px;
        background-color: #f8f9fa;
        color: #343a40;
        border: 1px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s, color 0.3s;
    }

    .range-btn.active {
        background-color: #007bff;
        color: #fff;
        border-color: #0056b3;
    }

    .range-btn:hover {
        background-color: #e2e6ea;
        color: #343a40;
    }

    .transaction-details {
        text-align: center;
        margin-top: 20px;
    }

    .transaction-details p {
        font-size: 18px;
        margin: 10px 0;
    }

    #price-display {
        color: #28a745;
        font-weight: bold;
    }

    #deadline-display {
        color: #dc3545;
        font-weight: bold;
    }


    .payment-methods {
        display: flex;
        justify-content: space-around;
        margin-bottom: 20px;
    }

    .payment-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .payment-btn:hover {
        background-color: #28a745;
        color: #fff;
    }

    .payment-btn:active {
        transform: scale(0.98);
    }

    /* Active state */
    .payment-btn.active {
        background-color: #28a745;
        /* Warna hijau untuk tombol aktif */
        color: white;
    }

    .transaction-details {
        margin-bottom: 20px;
        font-size: 18px;
    }

    .btn-submit {
        width: 100%;
        padding: 15px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        opacity: 0.6;
    }

    .btn-submit:enabled {
        opacity: 1;
    }

    .btn-submit:disabled {
        background-color: #d6d6d6;
    }
</style>

<!-- Form -->
<div class="transaction-container">
    <div class="alert alert-success alert-dismissible">
        <small id="message" class="form-text text-muted"></small>
    </div>
    <div class="transaction-header">
        <h2>Transaksi Anda {{$product->provider->name}}</h2>
        <p><strong>{{ $phoneNumber }}</strong> <br>
            <strong>{{ number_format($product->amount, 0, ',', '.') }}</strong>
        </p>
    </div>
    <!-- Pilihan metode pembayaran -->
    <div class="payment-methods">
        <button id="hutang-btn" class="payment-btn">Hutang</button>
        <button id="langsung-btn" class="payment-btn">Langsung</button>
    </div>

    <!-- Pilihan rentang waktu -->
    <div class="range-selection" id="range-selection" style="display: none;">
        <p>Pilih Rentang Waktu:</p>
        <div class="range-buttons">
            <button data-range="1-7" class="range-btn">1-7 Hari</button>
            <button data-range="8-14" class="range-btn">8-14 Hari</button>
            <button data-range="15-21" class="range-btn">15-21 Hari</button>
            <button data-range="22-30" class="range-btn">22-30 Hari</button>
        </div>
    </div>



    <!-- Detail transaksi -->
    <div class="transaction-details">
        <p><strong>Harga Jual:</strong> Rp <span
                id="price-display">{{ number_format($product->price, 0, ',', '.') }}</span></p>
        <p><strong>Deadline:</strong> <span id="deadline-display">-</span></p>
    </div>

    <!-- Form pengajuan -->
    <form action="{{ route('transaksi-proses_user') }}" method="POST" id="transaction-form">
        @csrf
        <input type="hidden" name="product_id" value="{{ $product->id }}">
        <input type="hidden" name="payment_method" id="payment-method" value="">
        <input type="hidden" name="price" id="final-price" value="">
        <input type="hidden" name="deadline_date" id="final-deadline" value="">
        <input type="hidden" name="repayment" id="repayment" value="repayment">

        @if ($product->provider->name == "Tagihan Listrik")
        <input type="hidden" name="phone_number" value="{{ $pengajuan->detail->no_listrik }}">
        @else
        <input type="hidden" name="phone_number" value="{{ $phoneNumber }}">
        @endif


        <div class="form-group ">
            <label for="name">Atas Nama</label>
            <input type="text" id="name" name="name" class="form-control"
                placeholder="Masukkan nama Pembeli / Nama Listrik" value="{{$pengajuan->detail->name ?? ''}}">
        </div>
        @if ($product->kategori->name == "Listrik")
        <div class="form-group mt-3">
            <label for="no_hp">No HP Tujuan</label>
            <input type="text" id="no_hp" name="no_hp" class="form-control"
                placeholder="Masukkan No Hp Agar TOKEN di kirim no tujuan" value="{{$pengajuan->detail->no_hp ?? ''}}">
        </div>
        @if (!Auth::check())
        <div class="form-group mt-3">
            <label for="submitted_by">Nama Pengaju</label>
            <input type="text" id="submitted_by" name="submitted_by" class="form-control"
                placeholder="Masukkan Anda yang pengajukan">
        </div>
        @endif
        @endif
        <div class="form-group row">
            <center>
                <label for="description" class="col-sm-12 col-form-label">Keterangan
            </center>
            <textarea class="form-control col-12 @error('description') is-invalid @enderror" name="description"
                id="description"
                placeholder="Jelaskan untuk Uangnya di kasih kesiapa ">{{ old('description') }}</textarea>
        </div>

        <br>

        <button type="submit" id="submit-btn" class="btn-submit" disabled>Submit</button>
    </form>
</div>
<!--Untuk alert-->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const hutangBtn = $('#hutang-btn');
        const langsungBtn = $('#langsung-btn');
        const rangeSelection = $('#range-selection');
        const priceDisplay = $('#price-display');
        const deadlineDisplay = $('#deadline-display');
        const paymentMethodInput = $('#payment-method');
        const finalPriceInput = $('#final-price');
        const finalDeadlineInput = $('#final-deadline');
        const submitBtn = $('#submit-btn');
        const submittedByInput = $('#name'); // Input Nama
        const productId = '{{ Crypt::encryptString($product->id) }}';

        // Reset fungsi untuk Hutang
        function resetRangeSelection() {
            $('.range-btn').removeClass('active'); // Reset tombol rentang waktu
            deadlineDisplay.text('-'); // Reset tampilan deadline
            priceDisplay.text('-'); // Reset tampilan harga
            finalPriceInput.val(''); // Reset input harga
            finalDeadlineInput.val(''); // Reset input deadline
        }

        // Fungsi untuk update harga dan deadline
        function updatePriceAndDeadline(price, deadline) {
            priceDisplay.text(price.toLocaleString());
            deadlineDisplay.text(deadline);
            finalPriceInput.val(price);
            finalDeadlineInput.val(deadline);
        }

        // Fungsi untuk periksa apakah ada tombol rentang waktu aktif
        function checkActiveRangeButton() {
            const activeButton = $('.range-btn.active');
            if (activeButton.length > 0) {
                const range = activeButton.data('range');
                $.ajax({
                    url: '{{ route('calculate-price') }}',
                    method: 'POST',
                    data: {
                        range: range,
                        product_id: productId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        updatePriceAndDeadline(response.price, response.deadline); // Update data
                    },
                    error: function() {
                        alert('Gagal mengambil data harga dan deadline.');
                    }
                });
            } else {
                resetRangeSelection(); // Reset jika tidak ada tombol aktif
            }
        }

        // Fungsi untuk periksa apakah submit button bisa diaktifkan
        function checkSubmitStatus() {
            const isNameFilled = submittedByInput.val().trim() !== '';
            const isHutangValid = paymentMethodInput.val() !== 'hutang' || $('.range-btn.active').length > 0;

            // Aktifkan/Nonaktifkan tombol submit
            submitBtn.prop('disabled', !(isNameFilled && isHutangValid));
        }

        // Event handler untuk tombol Hutang
        hutangBtn.on('click', function() {
            hutangBtn.addClass('active');
            langsungBtn.removeClass('active');
            paymentMethodInput.val('hutang');

            rangeSelection.show(); // Tampilkan pilihan rentang waktu
            checkActiveRangeButton(); // Periksa tombol rentang waktu aktif
            checkSubmitStatus(); // Periksa status tombol submit
        });

        // Event handler untuk tombol Langsung
        langsungBtn.on('click', function() {
            langsungBtn.addClass('active');
            hutangBtn.removeClass('active');
            paymentMethodInput.val('langsung');

            rangeSelection.hide(); // Sembunyikan rentang waktu
            const currentPrice = parseFloat('{{ $product->price }}');
            const currentDeadline = 'Sekarang';

            updatePriceAndDeadline(currentPrice, currentDeadline); // Update data
            checkSubmitStatus(); // Periksa status tombol submit
        });

        // Event handler untuk tombol Rentang Waktu
        $('.range-btn').on('click', function() {
            $('.range-btn').removeClass('active'); // Reset tombol lainnya
            $(this).addClass('active'); // Tandai tombol aktif

            const range = $(this).data('range');
            $.ajax({
                url: '{{ route('calculate-price') }}',
                method: 'POST',
                data: {
                    range: range,
                    product_id: productId,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    updatePriceAndDeadline(response.price, response
                        .deadline); // Update data
                    checkSubmitStatus(); // Periksa status tombol submit
                },
                error: function() {
                    alert('Gagal mengambil data harga dan deadline.');
                }
            });
        });

        // Event handler untuk input Nama
        submittedByInput.on('input', function() {
            checkSubmitStatus(); // Periksa status tombol submit
        });

        // Inisialisasi awal
        checkSubmitStatus(); // Pastikan tombol submit disetel sesuai kondisi awal
    });
</script>

<script>
    document.getElementById('transaction-form').addEventListener('submit', function(event) {
        // event.preventDefault(); // Hapus atau komentari ini untuk uji coba

        const submitButton = document.getElementById('submit-btn');
        submitButton.disabled = true; // Disable tombol
        submitButton.textContent = 'Loading...'; // Ubah teks tombol
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Nomor telepon yang akan dicek
        const phoneNumber = "{{ $phoneNumber }}";

        // Kirim permintaan AJAX ke server untuk memeriksa nomor
        fetch(`/check-phone/${phoneNumber}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Jika nomor ditemukan, isi nama dan tampilkan pesan
                    document.getElementById('name').value = data.name;
                    document.getElementById('name').text = data.name;
                    document.getElementById('message').textContent =
                        "Hallo Kel. Besar Ma HAYA, Selamat membeli pulsa!";
                } else {
                    // Jika nomor tidak ditemukan, beri pesan berbeda
                    document.getElementById('message').textContent =
                        "Nomor ini bukan milik anggota Keluarga.";
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('message').textContent = "Terjadi kesalahan saat memproses data.";
            });
    });
</script>
@endsection