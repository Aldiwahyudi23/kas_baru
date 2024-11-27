<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Inactive</title>
    <!-- Include SweetAlert CSS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <script>
        // SweetAlert Configuration
        Swal.fire({
            icon: 'Peringatan',
            title: 'Account Tidak Aktif',
            text: 'Akun anda tidak bisa di gunakan, tidak aktif. hubungi admin utama untuk mengaktifkan.',
            confirmButtonText: 'Contact Admin',
            footer: '<a href="mailto:admin@example.com">admin@example.com</a>'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect user or keep them on the page
                window.location.href = '/'; // Change to desired URL
            }
        });
    </script>
</body>

</html>