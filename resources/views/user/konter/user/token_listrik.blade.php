@extends('user.layout.app')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<style>
    /* Body styling */
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f8f9fa;
        /* Bootstrap light gray */
        color: #343a40;
        /* Bootstrap dark gray */
    }

    /* Header */
    .title {
        font-size: 2rem;
        font-weight: 700;
        color: #007bff;
        /* Bootstrap primary */
        margin-bottom: 0.5rem;
    }

    .subtitle {
        font-size: 1rem;
        color: #6c757d;
        /* Bootstrap muted */
    }

    /* Card styling */
    .card {
        border-radius: 12px;
    }

    .card .form-label {
        font-weight: 500;
        color: #495057;
        /* Bootstrap input text */
    }

    .card .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
    }

    .card .btn-primary {
        border-radius: 8px;
        transition: all 0.3s ease-in-out;
    }

    .card .btn-primary:hover {
        background-color: #0056b3;
        /* Darker primary */
        box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    }
</style>
<style>
    #loader img {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }
</style>

<style>
    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 15px;
        margin-top: 20px;
    }

    .product-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .product-card:hover {
        transform: scale(1.05);
        box-shadow: 0px 6px 10px rgba(0, 0, 0, 0.2);
    }

    .product-info {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: flex-start;
        flex-grow: 1;
        margin-left: 15px;
    }

    .product-info h4 {
        margin: 5px 0;
        font-size: 18px;
    }

    .product-info p {
        margin: 3px 0;
        color: #666;
        font-size: 14px;
    }

    .product-info a {
        align-self: flex-end;
        padding: 10px 15px;
        background-color: #007bff;
        color: #fff;
        border-radius: 5px;
        text-decoration: none;
        font-weight: bold;
        font-size: 14px;
    }

    .product-info a:hover {
        background-color: #0056b3;
    }

    .provider-label {
        font-weight: bold;
        font-size: 16px;
        color: #007bff;
        text-align: center;
    }

    .product-link.disabled {
        pointer-events: none;
        background-color: #ccc;
        color: #666;
        cursor: not-allowed;
    }
</style>



<div class="container py-5">
    <!-- Header -->
    <div class="text-center mb-4">
        <h1 class="title">Pengajuan Pulsa Listrik</h1>
        <p class="subtitle">Isi nomor Listri / Mteran yang benar</p>
    </div>

    <!-- Form -->
    <div class="card shadow-sm mx-auto p-4" style="max-width: 400px;">
        <!-- Input Nomor HP -->
        <form id="pulsaForm">
            <div class="mb-3">
                <label for="no_listrik" class="form-label">Nomor Meteran</label>
                <input type="number" id="no_listrik" name="no_listrik" placeholder="423xxxxxxx" class="form-control">
                <small id="phone-validation-message" class="text-danger" style="display: none;">Nomor Meteran tidak
                    boleh
                    lebih dari 12 karakter.</small>
                <small id="phone-validation-message2" class="text-danger" style="display: none;">Masukan Nomor
                    Meteran
                    yang sesuai diAtas 10 karakter.</small>
            </div>
            <!-- Animasi Loader -->
            <div id="loader" style="display: none; text-align: center; margin-top: 10px;">
                <div
                    style="display: inline-block; border: 4px solid #f3f3f3; border-radius: 50%; border-top: 4px solid #3498db; width: 30px; height: 30px; animation: spin 1s linear infinite;">
                </div>
                <p>Sedang memproses...</p>
            </div>

            <div id="provider-info">
                <div id="product-list" class="product-grid">
                    <!-- Produk akan diisi melalui AJAX -->
                </div>
            </div>
        </form>
    </div>
    <div class="col-12 col-sm-6">
        <!-- Mengambil data tabel  -->
        @include('user.konter.tabel.story')
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('#no_listrik').on('input', function() {
            const no_listrik = $(this).val();

            // Validasi panjang nomor telepon
            if (no_listrik.length > 12) {
                $('#phone-validation-message').show();
            } else {
                $('#phone-validation-message').hide();
            }
            // Validasi panjang nomor telepon
            if (no_listrik.length < 10) {
                $('#phone-validation-message2').show();
            } else {
                $('#phone-validation-message2').hide();
            }

            // Validasi apakah nomor telepon valid untuk tombol Beli
            const isValidPhoneNumber = no_listrik.length >= 10 && no_listrik.length <= 12;

            if (no_listrik.length >= 9) {
                $.ajax({
                    url: '/detect-provider',
                    method: 'POST',
                    data: {
                        no_listrik: no_listrik,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#provider-name').text('Provider: ' + response.provider);

                        const products = response.products;
                        const productList = $('#product-list');
                        productList.empty();

                        if (products.length > 0) {
                            products.forEach(function(product) {
                                const transactionUrl =
                                    '{{ route("transaksi-user", [":encryptedId", "phoneNumber" => ":no_listrik"]) }}'
                                    .replace(':encryptedId', encodeURIComponent(
                                        product.id))
                                    .replace(':no_listrik', encodeURIComponent(
                                        no_listrik));

                                const productCard = `
                                    <div class="product-card">
                                        <div class="provider-label">${response.provider}</div>
                                        <div class="product-info">
                                            <h4>${parseInt(product.amount).toLocaleString()}</h4>
                                            <p>Rp ${parseInt(product.price).toLocaleString()}</p>
                                        </div>
                                        <a class="btn btn-primary product-link ${
                                            isValidPhoneNumber ? '' : 'disabled'
                                        }" href="${isValidPhoneNumber ? transactionUrl : '#'}">Beli</a>
                                    </div>
                                `;
                                productList.append(productCard);
                            });
                        } else {
                            const productCard = `
                                <div class="product-card">
                                    <div class="provider-label">X</div>
                                    <div class="product-info">
                                        <p>Tidak ada produk tersedia</p>
                                    </div>
                                </div>
                            `;
                            productList.append(productCard);
                        }
                    },
                    error: function() {
                        $('#provider-name').text('Provider: Tidak ditemukan');
                        $('#product-list').html('<li>Tidak ada produk tersedia</li>');
                    }
                });
            } else {
                // Reset tampilan jika nomor telepon kurang dari 4 karakter
                $('#provider-name').text('Provider: Tidak ditemukan');
                $('#product-list').html('<li>Tidak ada produk tersedia</li>');
            }
        });
    });
</script>

@endsection