<!-- Navigation -->

<style>
  /* Dropdown on hover */
.navbar-nav .dropdown:hover > .dropdown-menu {
    display: block;
    margin-top: 0; /* biar ga kelempar jauh */


}

/* Hilangkan icon panah bawaan bootstrap */
.navbar-nav .dropdown-toggle::after {
    display: none !important;
}
/* ISOLASI LOGO - Tidak mempengaruhi elemen lain */
.navbar-brand {
    display: flex;
    margin: 10px 0;
    align-items: center;
    height: 50px; /* Tinggi tetap untuk brand container */
    overflow: hidden; /* Sembunyikan bagian logo yang overflow */
}
.navbar-logo {
    /* Logo bisa diubah ukuran sesuka hati tanpa ganggu layout */
    height: 150px; /* Ubah sesuai kebutuhan */
    width: auto;
    max-height: none; /* Hilangkan batasan */
    max-width: none; /* Hilangkan batasan */
    
    /* Posisioning untuk isolasi */
    object-fit: contain;
    flex-shrink: 0; /* Logo tidak mengecil saat container sempit */
}

 .creator-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

</style>
<nav
class="navbar navbar-expand-lg navbar-light navbar-store fixed-top navbar-fixed-top"
data-aos="fade-down"
>
<div class="container">
  <a class="navbar-brand" href="{{ route('home') }}">
   <img src="/images/logo snbt.svg" alt="Logo SNBT" class="navbar-logo" />
  </a>
  <button
    class="navbar-toggler"
    type="button"
    data-toggle="collapse"
    data-target="#navbarResponsive"
    aria-controls="navbarResponsive"
    aria-expanded="false"
    aria-label="Toggle navigation"
  >
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarResponsive">
    <ul class="navbar-nav ml-auto">
     <li class="nav-item {{ Request::is('/') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('home') }}">Beranda</a>
</li>
<li class="nav-item {{ Request::is('banksoal*') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('bank-soal') }}">Latihan Soal</a>
</li>
<li class="nav-item {{ Request::is('get-tryout*') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('tryout') }}">Try Out</a>
</li>
<li class="nav-item {{ Request::is('blog*') ? 'active' : '' }}">
  <a class="nav-link" href="{{ route('blog') }}">Blog</a>
</li>

      {{-- <ul class="navbar-nav d-none d-lg-flex">
        <li class="nav-item dropdown">
          <a
            class="nav-link"
            href="#"
            id="navbarDropdown"
            role="button"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
           Panduan
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="{{ route('dashboard') }}">SNBT</a>
            <a class="dropdown-item" href="{{ route('dashboard-settings-account') }}"
              >UM UGM</a
            >
            <div class="dropdown-divider"></div>
            
          </div>
        </li>
       
      </ul> --}}
      <li class="nav-item {{ Request::is('about*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('about') }}">Tentang</a>
    </li>
        @auth
      <li class="nav-item dropdown">
    <a
      class="nav-link dropdown-toggle"
      href="#"
      id="navbarDropdown"
      role="button"
      aria-haspopup="true"
    >
  
        <div class="d-flex align-items-center" style="gap: 14px;">
        <div class="creator-avatar">
          {{ strtoupper(substr(Auth::user()->username, 0, 2)) }}
        </div>
        <div style="padding-top: 2px;"> <!-- sedikit padding buat posisi lebih rapi -->
          Hi, {{ Auth::user()->username }}
        </div>
      </div>
  
    </a>

  </a>
  <div class="dropdown-menu" aria-labelledby="navbarDropdown">
    <a class="dropdown-item" href="{{ route('dashboard') }}">Dashboard</a>
    <a class="dropdown-item" href="{{ route('dashboard-settings-account') }}">Settings</a>
    <div class="dropdown-divider"></div>
    <a class="dropdown-item" href="{{ route('logout') }}"
       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
       Logout
    </a>
  </div>
</li>
  
      <!-- Mobile Menu -->
      <ul class="navbar-nav d-block d-lg-none">
        <li class="nav-item">
          <a class="nav-link" href="{{ route('logout') }}">
            {{-- Hi, {{ Auth::user()->name }} --}}
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link d-inline-block" href={{ route('logout') }}""
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
       Logout
          </a>
        </li>
      </ul>
        @endauth
     @guest
    <li class="nav-item">
      <a
  class="btn nav-link px-4 text-white"
  href="{{ route('login', ['redirect_to' => url()->current()]) }}"
  style="background-color: #1A4F80;">
  Log In
</a>
      
    </li>
     @endguest
    </ul>

    @auth

    <!-- Mobile Menu -->
    <ul class="navbar-nav d-block d-lg-none">
      <li class="nav-item">
        <a class="nav-link" href="#">
          Hi, {{ Auth::user()->name }}
        </a>
      </li>
        <li class="nav-item">
          <a class="nav-link d-inline-block" href={{ route('logout') }}""
             onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
       Logout
          </a>
        </li>
    </ul>
    @endauth
  </div>
</div>
</nav>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
   <input type="hidden" name="redirect_to" value="{{ url()->current() }}">
  @csrf
</form>
