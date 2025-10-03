<style>
  .footer {
     background-color: #1A4F80;
      color: white;
      padding: 3rem 1rem;
      margin-top: 0px;
  }
  
  .footer-logo {
      font-size: 2.2rem;
      font-weight: bold;
      margin-bottom: 1rem;
  }
  
  
  .version-title {
      margin: 1rem 0 0.5rem 0;
      font-size: 1rem;
  }
  
  .download-btn {
      background-color: #000;
      color: white;
      padding: 8px 15px;
      border-radius: 6px;
      display: inline-flex;
      align-items: center;
      text-decoration: none;
      margin-right: 10px;
      margin-bottom: 10px;
  }
  
  .download-btn:hover {
      color: #f8f9fa;
      background-color: #212529;
  }
  
  .download-btn img {
      height: 20px;
      margin-right: 8px;
  }
  
  .installation-link {
      color: #ffc107;
      text-decoration: none;
      font-weight: bold;
  }
  
  .installation-link:hover {
      text-decoration: underline;
  }
  
  .footer-menu {
      list-style: none;
      padding-left: 0;
  }
  
  .footer-menu li {
      margin-bottom: 0.5rem;
      margin-left: 0.5rem;
  }
  
  .footer-menu a {
      color: white;
      text-decoration: none;
  }
  
  .footer-menu a:hover {
      text-decoration: underline;
  }
  
  .social-item {
      display: flex;
      align-items: center;
      margin-bottom: 0.8rem;
  }
  
  .social-icon {
      width: 20px;
      height: 20px;
      margin-right: 10px;
  }
</style>

<footer class="footer" style="margin-bottom: 0;">
    <div class="container">
        <div class="row">
            <!-- Brand section with logo and download options -->
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0" >
                <div class="footer-logo">
                    SoalIn
                </div>
                {{-- <div class="version-title">Mobile version</div>
                <div class="d-flex flex-wrap">
                    <a href="#" class="download-btn">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/googleplay.svg" alt="Play Store">
                        <span>Google Play</span>
                    </a>
                    <a href="#" class="download-btn">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/apple.svg" alt="App Store">
                        <span>App Store</span>
                    </a>
                </div>
                <div class="version-title">Desktop version</div>
                <div class="d-flex flex-wrap">
                    <a href="#" class="download-btn">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/windows.svg" alt="Windows">
                        <span>Windows</span>
                    </a>
                    <a href="#" class="download-btn">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/apple.svg" alt="Mac">
                        <span>Mac</span>
                    </a>
                </div>
                <div class="mt-3">
                    <a href="#" class="installation-link">Cara Instalasi?</a>
                </div> --}}
            </div>
            <!-- Company info column -->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0"  >
                <h5 class="mb-3">Perusahaan</h5>
                <ul class="footer-menu">
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Karir</a></li>
                </ul>
            </div>
            <!-- Guide column -->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0"  >
                <h5 class="mb-3">Panduan</h5>
                <ul class="footer-menu">
                    <li><a href="#">SNBP</a></li>
                    <li><a href="#">SNBT</a></li>
                    <li><a href="#">UTBK</a></li>
                </ul>
            </div>
            <!-- Copyright column -->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0"  >
                <h5 class="mb-3">Hak Cipta</h5>
                <ul class="footer-menu">
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                </ul>
            </div>
            <!-- Follow us column -->
            <div class="col-lg-2 col-md-6 mb-4 mb-md-0" >
                <h5 class="mb-3">Ikuti Kami</h5>
                <div class="footer-menu">
                    <div class="social-item">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/facebook.svg" alt="Facebook" class="social-icon">
                        <a href="#">Facebook</a>
                    </div>
                    <div class="social-item">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/instagram.svg" alt="Instagram" class="social-icon">
                        <a href="#">Instagram</a>
                    </div>
                    <div class="social-item">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/youtube.svg" alt="Youtube" class="social-icon">
                        <a href="#">Youtube</a>
                    </div>
                    <div class="social-item">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/linkedin.svg" alt="LinkedIn" class="social-icon">
                        <a href="#">LinkedIn</a>
                    </div>
                </div>
            </div>
            <!-- Contact column -->
            <div class="col-lg-1 col-md-6"  >
                <h5 class="mb-3">Kontak Kami</h5>
                <div class="footer-menu">
                    <div class="social-item">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/whatsapp.svg" alt="Whatsapp" class="social-icon">
                        <a href="#">Whatsapp</a>
                    </div>
                    <div class="social-item">
                        <img src="https://cdnjs.cloudflare.com/ajax/libs/simple-icons/3.0.1/gmail.svg" alt="Email" class="social-icon">
                        <a href="mailto:cs@soalin.com">cs@soalin.com</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </footer>
  <footer class="mt-0 mb-0" style=" background-color: #20609b;border: none;">
      <div class="container">
        <div class="row">
          <div class="col-12 text-center">
            <p class="pt-4 pb-2" style="color: white;">
              2025 Copyright Store. All Rights Reserved.
            </p>
          </div>
        </div>
      </div>
  </footer>