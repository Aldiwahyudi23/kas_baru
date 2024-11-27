<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <?php

    use App\Models\CompanyInformation;

    //Untuk memanggil Informasi Perusahan di dalam model CompanyInformasi,
    $companyInfo = CompanyInformation::first(); // Ambil data informasi perusahaan

    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset($companyInfo->logo) }}" type="image/png">

    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/fontawesome-free/css/all.min.css') }}">
    <!-- overlayScrollbars -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css' ) }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ asset('admin-lte/dist/css/adminlte.min.css') }}">
    <!-- summernote -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/summernote/summernote-bs4.min.css')}}">
    <!-- CodeMirror -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/codemirror/codemirror.css')}}">
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/codemirror/theme/monokai.css')}}">
    <!-- SimpleMDE -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/simplemde/simplemde.min.css')}}">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css')}}">
    <link rel="stylesheet"
        href="{{ asset('admin-lte/plugins/datatables-responsive/css/responsive.bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/datatables-buttons/css/buttons.bootstrap4.min.css')}}">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <!-- Bootstrap4 Duallistbox -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/bootstrap4-duallistbox/bootstrap-duallistbox.min.css')}}">
    <!-- Bootstrap Color Picker -->
    <link rel="stylesheet"
        href="{{ asset('admin-lte/plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}">
    <!-- Ekko Lightbox -->
    <link rel="stylesheet" href="{{ asset('admin-lte/plugins/ekko-lightbox/ekko-lightbox.css')}}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">


    @yield('style')
    <!-- Styles -->
    @livewireStyles
    <!-- Untuk tanda wajib isi bintang merah -->
    <style>
        .text-danger {
            color: red;
            /* Mengatur warna bintang menjadi merah */
        }
    </style>

</head>



<body class=" hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">

        <!-- Preloader -->
        <!-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="animation__wobble" src="dist/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div> -->

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-dark">
            @include('user.layout.navbar')
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->

            <a href="index3.html" class="brand-link">
                <img src="{{ asset($companyInfo->logo) }}" alt="AdminLTE Logo"
                    class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light"> {{$companyInfo -> company_name}}</span>
            </a>

            <!--Sidebar-->
            <div class="sidebar">

                @include('user.layout.sidebar')

            </div>
            <!--/.sidebar -->
        </aside>

        <!--Content Wrapper.Contains page content-->
        <div class="content-wrapper ">
            <!--Content Header(Page header) -->
            <div class="content-header">

            </div>
            <!--/.content-header -->

            <!--Main content-->
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
                <!--/.container - fluid-->
            </section>
            <!--/.content -->
        </div>
        <!--/.content-wrapper -->

        <!--Control Sidebar-->
        <aside class="control-sidebar control-sidebar-dark">
            <!--Control sidebar content goes here-->
        </aside>
        <!--/.control-sidebar -->
        @include('user.layout.footer')


        <!--Control Sidebar-->
        <aside class="control-sidebar control-sidebar-dark">
            <!--Control sidebar content goes here-->
        </aside>
        <!--/.control-sidebar -->
    </div>
    <!--. / wrapper-->
    <!--Untuk alert-->
    @include('sweetalert::alert')
    @yield('script')
    @stack('modals')

    @livewireScripts

    <!--REQUIRED SCRIPTS-->
    <!--Scrip untuk mengambil kode tambahan dari file lain-->
    <!--jQuery-->
    <script src="{{ asset('admin-lte/plugins/jquery/jquery.min.js') }}"></script>
    <!-- Bootstrap -->
    <script src="{{ asset('admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <!-- overlayScrollbars -->
    <script src="{{ asset('admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('admin-lte/dist/js/adminlte.js') }}"></script>

    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="plugins/jquery-mousewheel/jquery.mousewheel.js')}}"></script>
    <script src="plugins/raphael/raphael.min.js')}}"></script>
    <script src="plugins/jquery-mapael/jquery.mapael.min.js')}}"></script>
    <script src="plugins/jquery-mapael/maps/usa_states.min.js')}}"></script>
    <!-- ChartJS -->
    <script src="{{ asset('admin-lte/plugins/chart.js/Chart.min.js' ) }}"></script>

    <!-- AdminLTE dashboard demo (This is only for demo purposes) -->
    <script src="{{ asset('admin-lte/dist/js/pages/dashboard2.js')}}"></script>

    <!-- Summernote -->
    <script src="{{ asset('admin-lte/plugins/summernote/summernote-bs4.min.js')}}"></script>
    <!-- CodeMirror -->
    <script src="{{ asset('admin-lte/plugins/codemirror/codemirror.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/codemirror/mode/css/css.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/codemirror/mode/xml/xml.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/codemirror/mode/htmlmixed/htmlmixed.js')}}"></script>
    <!-- DataTables  & Plugins -->
    <script src="{{ asset('admin-lte/plugins/datatables/jquery.dataTables.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-responsive/js/dataTables.responsive.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/jszip/jszip.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/pdfmake/pdfmake.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/pdfmake/vfs_fonts.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
    <script src="{{ asset('admin-lte/plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>
    <!-- Select2 -->
    <script src="{{ asset('admin-lte/plugins/select2/js/select2.full.min.js')}}"></script>
    <!-- Bootstrap4 Duallistbox -->
    <script src="{{ asset('admin-lte/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js')}}"></script>
    <!-- bootstrap color picker -->
    <script src="{{ asset('admin-lte/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js')}}"></script>
    <!-- Ekko Lightbox -->
    <script src="{{ asset('admin-lte/plugins/ekko-lightbox/ekko-lightbox.min.js')}}"></script>
    <!-- Filterizr-->
    <script src="{{ asset('admin-lte/plugins/filterizr/jquery.filterizr.min.js')}}"></script>
    <!-- AdminLTE for demo purposes -->

    <script>
        function preview(selector, temporaryFile, width = 200) {
            $(selector).empty();
            $(selector).append(`<img src="${window.URL.createObjectURL(temporaryFile)}" width="${width}">`);
        }
    </script>
    <!-- Untuk class textarea -->
    <script>
        $(function() {
            // Summernote untuk semua textarea dengan class 'summernote-textarea'
            $('.summernote-textarea').summernote();

            // CodeMirror untuk semua textarea dengan class 'codemirror'
            $('textarea.codemirror').each(function() {
                CodeMirror.fromTextArea(this, {
                    mode: "htmlmixed",
                    theme: "monokai"
                });
            });
        });
    </script>

    <!-- Untuk class tabel -->
    <script>
        $(function() {
            $(".datatable").each(function() {
                $(this).DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                    "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
                }).buttons().container().appendTo($(this).closest('.dataTables_wrapper').find(
                    '.col-md-6:eq(0)'));
            });
            $(".datatable1").each(function() {
                $(this).DataTable({
                    "responsive": true,
                    "lengthChange": false,
                    "autoWidth": false,
                }).buttons().container().appendTo($(this).closest('.dataTables_wrapper').find(
                    '.col-md-6:eq(0)'));
            });
        });
    </script>
    <script>
        $(function() {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            //color picker with addon
            $('.my-colorpicker2').colorpicker()

            $('.my-colorpicker2').on('colorpickerChange', function(event) {
                $('.my-colorpicker2 .fa-square').css('color', event.color.toString());
            })
        });
    </script>

    <!-- Untuk Semua tombol jika di klik jadi diable -->
    <script>
        document.getElementById('adminForm').addEventListener('submit', function(event) {
            // event.preventDefault(); // Hapus atau komentari ini untuk uji coba

            const submitButton = document.getElementById('submitBtns');
            submitButton.disabled = true; // Disable tombol
            submitButton.textContent = 'Loading...'; // Ubah teks tombol
        });
    </script>
    <!-- Untuk Semua tombol jika di klik jadi diable -->
    <script>
        document.getElementById('adminForm1').addEventListener('submit', function(event) {
            // event.preventDefault(); // Hapus atau komentari ini untuk uji coba

            const submitButton = document.getElementById('submitBtns1');
            submitButton.disabled = true; // Disable tombol
            submitButton.textContent = 'Loading...'; // Ubah teks tombol
        });
    </script>
    <!-- Untuk Semua tombol jika di klik jadi diable -->
    <script>
        document.getElementById('adminForm2').addEventListener('submit', function(event) {
            // event.preventDefault(); // Hapus atau komentari ini untuk uji coba

            const submitButton = document.getElementById('submitBtns2');
            submitButton.disabled = true; // Disable tombol
            submitButton.textContent = 'Loading...'; // Ubah teks tombol
        });
    </script>

    <!-- Untuk Tombol is_aktif agar memproses di latar belakang -->
    <script>
        function toggleAccess(button) {
            var url = $(button).data('url');
            var currentStatus = $(button).data('active'); // Status saat ini
            var newStatus = currentStatus === 1 ? 0 : 1; // Status baru

            $.ajax({
                url: url,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    is_active: newStatus
                },
                success: function(response) {
                    if (response.success) {
                        // Update data-active dengan status baru
                        $(button).data('active', response.new_status);
                        // Update tombol dan statusnya
                        if (response.new_status == 1) {
                            $(button).removeClass('btn-danger').addClass('btn-success').text('ON');
                        } else {
                            $(button).removeClass('btn-success').addClass('btn-danger').text('OFF');
                        }


                        // SweetAlert success
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Status has been updated successfully!'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update status!'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong!'
                    });
                }
            });
        }
    </script>

    <!-- Untuk Foto -->
    <script>
        $(function() {
            $(document).on('click', '[data-toggle="lightbox"]', function(event) {
                event.preventDefault();
                $(this).ekkoLightbox({
                    alwaysShowClose: true
                });
            });

            $('.filter-container').filterizr({
                gutterPixels: 3
            });
            $('.btn[data-filter]').on('click', function() {
                $('.btn[data-filter]').removeClass('active');
                $(this).addClass('active');
            });
        })
    </script>

    <!-- Untuk memunculkan Input tanda bukti Tf -->
    <script>
        // Function to toggle the visibility of the transfer receipt input based on selected payment method
        function toggleTransferReceipt() {
            var paymentMethod = document.getElementById('payment_method').value;
            var transferReceipt = document.getElementById('transfer_receipt');
            transferReceipt.style.display = (paymentMethod === 'transfer') ? 'block' : 'none';
        }
    </script>

    <!-- Untuk semua input Amount, Copy code untuk inputnya -->

    <!-- <div class="form-group">
        <label for="amount">Jumlah Pembayaran <span class="text-danger">*</span></label>
        <input type="text" name="amount_display" id="amount_display"
            value="{{ old('amount') ? number_format(old('amount'), 2, ',', '.') : '' }}"
            class="form-control col-12 @error('amount') is-invalid @enderror"
            placeholder="Masukkan nominal yang diajukan"
            oninput="formatIndonesian(this)">
        <input type="hidden" name="amount" id="amount" value="{{ old('amount') }}">
        @error('amount')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div> -->
    <script>
        function formatIndonesian(element) {
            // Ambil nilai asli tanpa format
            let rawValue = element.value.replace(/\./g, '').replace(',', '.');

            // Pastikan hanya angka dan desimal yang valid
            if (!/^\d*(\.\d{0,2})?$/.test(rawValue)) {
                rawValue = element.dataset.previousValue || '';
            }

            // Simpan nilai sebelumnya untuk validasi selanjutnya
            element.dataset.previousValue = rawValue;

            // Format angka sesuai Indonesia (titik ribuan, koma desimal)
            const [integer, decimal] = rawValue.split('.');
            const formattedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            const formattedValue = decimal !== undefined ?
                `${formattedInteger},${decimal.slice(0, 2)}` :
                formattedInteger;

            element.value = formattedValue;

            // Simpan nilai asli (tanpa format) ke input hidden
            document.getElementById('amount').value = rawValue;
        }

        // Inisialisasi ulang format saat halaman dimuat (untuk nilai lama)
        document.addEventListener('DOMContentLoaded', function() {
            const displayInput = document.getElementById('amount_display');
            if (displayInput && displayInput.value) {
                formatIndonesian(displayInput);
            }
        });
    </script>

</body>

</html>