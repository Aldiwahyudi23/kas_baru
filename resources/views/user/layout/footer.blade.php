<style>
    .footer-menu {
        position: fixed;
        bottom: 0;
        width: 100%;
        background-color: #343a40;
        /* Dark mode color */
        color: #fff;
        z-index: 1000;
        display: flex;
        justify-content: space-around;
        box-shadow: 0 -1px 5px rgba(0, 0, 0, 0.1);
        border-top-left-radius: 25px;
        /* Lengkungan di kiri atas */
        border-top-right-radius: 25px;
        /* Lengkungan di kanan atas */
        overflow: hidden;
    }

    .footer-menu .menu {
        display: flex;
        width: 100%;
        justify-content: space-around;
        align-items: center;
        padding: 10px 0;
    }

    .footer-menu .menu-item {
        text-align: center;
        flex: 1;
        color: #adb5bd;
        text-decoration: none;
        font-size: 14px;
        transition: color 0.2s ease;
    }

    .footer-menu .menu-item i {
        font-size: 18px;
        margin-bottom: 5px;
        display: block;
    }

    .footer-menu .menu-item.active,
    .footer-menu .menu-item:hover {
        color: #00d1b2;
        /* Highlight color */
    }

    .footer-menu .menu-item span {
        display: block;
        font-size: 12px;
    }

    /* Styling untuk menu profil */
    .footer-menu .menu-item.profile {
        flex: 1;
        position: relative;
    }

    .footer-menu .menu-item.profile img {
        width: 50px;
        /* Ukuran lebih besar untuk profil */
        height: 50px;
        border-radius: 50%;
        /* Foto bulat */
        object-fit: cover;
        border: 3px solid #00d1b2;
        /* Tambahkan border */
        background-color: #fff;
        /* Jika foto kosong, tetap terlihat */
    }

    .footer-menu .menu-item.profile::before {
        content: "";
        position: absolute;
        top: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        /* Lebih besar dari foto */
        height: 10px;
        background-color: #343a40;
        border-top-left-radius: 15px;
        border-top-right-radius: 15px;
    }

    /* Sembunyikan nama di bawah profil */
    .footer-menu .menu-item.profile span {
        display: none;
    }

    /* Only show footer on small screens */
    @media (min-width: 768px) {
        .footer-menu {
            display: none;
        }
    }
</style>


<footer class="footer-menu d-md-none">
    <nav class="menu">
        <a href="{{ route('user.dashboard') }}"
            class="menu-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <a href="{{ route('kas.index') }}" class="menu-item {{ request()->routeIs('kas.index') ? 'active' : '' }}">
            <i class="fas fa-money-bill"></i>
            <span>Bayar</span>
        </a>
        <!-- Menu Profil -->
        <a href="user/profile" class="menu-item profile {{ request()->routeIs('profile') ? 'active' : '' }}">
            @if(Auth::user()->profile_photo_path)
            <img src="{{ asset(Auth::user()->profile_photo_path) }}" alt="Profile Picture">
            @endif
        </a>
        <a href="{{ route('pinjaman.index') }}"
            class="menu-item {{ request()->routeIs('pinjaman.index') ? 'active' : '' }}">
            <i class="fas fa-hand-holding-usd"></i>
            <span>Pinjam</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-cogs"></i>
            <span>Setting</span>
        </a>
    </nav>
</footer>