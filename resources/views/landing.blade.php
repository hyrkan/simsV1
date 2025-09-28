<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Index - MySchool Bootstrap Template</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{ asset('landing/assets/img/favicon.png') }}" rel="icon">
  <link href="{{ asset('landing/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('landing/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ asset('landing/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ asset('landing/assets/css/main.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: MySchool
  * Template URL: https://bootstrapmade.com/myschool-bootstrap-school-template/
  * Updated: Jul 28 2025 with Bootstrap v5.3.7
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid container-xl position-relative d-flex align-items-center justify-content-between">

      <a href="index.html" class="logo d-flex align-items-center">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <!-- <img src="assets/img/logo.webp" alt=""> -->
        <i class="bi bi-buildings"></i>
        <h1 class="sitename">MySchool</h1>
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="index.html" class="active">Home</a></li>
          <li class="dropdown"><a href="about.html"><span>About</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="about.html">About Us</a></li>
              <li><a href="admissions.html">Admissions</a></li>
              <li><a href="academics.html">Academics</a></li>
              <li><a href="faculty-staff.html">Faculty &amp; Staff</a></li>
              <li><a href="campus-facilities.html">Campus &amp; Facilities</a></li>
            </ul>
          </li>

          <li><a href="students-life.html">Students Life</a></li>
          <li><a href="news.html">News</a></li>
          <li><a href="events.html">Events</a></li>
          <li><a href="alumni.html">Alumni</a></li>
          <li class="dropdown"><a href="#"><span>More Pages</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="news-details.html">News Details</a></li>
              <li><a href="event-details.html">Event Details</a></li>
              <li><a href="privacy.html">Privacy</a></li>
              <li><a href="terms-of-service.html">Terms of Service</a></li>
              <li><a href="404.html">Error 404</a></li>
              <li><a href="starter-page.html">Starter Page</a></li>
            </ul>
          </li>

          <li class="dropdown"><a href="#"><span>Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="#">Dropdown 1</a></li>
              <li class="dropdown"><a href="#"><span>Deep Dropdown</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
                <ul>
                  <li><a href="#">Deep Dropdown 1</a></li>
                  <li><a href="#">Deep Dropdown 2</a></li>
                  <li><a href="#">Deep Dropdown 3</a></li>
                  <li><a href="#">Deep Dropdown 4</a></li>
                  <li><a href="#">Deep Dropdown 5</a></li>
                </ul>
              </li>
              <li><a href="#">Dropdown 2</a></li>
              <li><a href="#">Dropdown 3</a></li>
              <li><a href="#">Dropdown 4</a></li>
            </ul>
          </li>
          <li><a href="contact.html">Contact</a></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>

    </div>
  </header>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="hero-container">
        <div class="hero-content">
          <h1>Shaping Minds for Tomorrow's World</h1>
          <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin quis magna vel dolor mattis hendrerit. Vestibulum sodales dignissim ipsum id commodo.</p>
          <div class="cta-buttons">
            <a href="{{ route('admission') }}" class="btn-apply">Apply Now</a>
            <a href="#" class="btn-tour">Campus Tour</a>
          </div>
          <div class="announcement">
            <div class="announcement-badge">New</div>
            <p>Fall 2025 Applications Open - Early Decision Deadline December 15</p>
          </div>
        </div>
      </div>

      <div class="highlights-container container">
        <div class="row gy-4">
          <div class="col-md-4">
            <div class="highlight-item">
              <div class="icon">
                <i class="bi bi-mortarboard-fill"></i>
              </div>
              <h3>98% Graduate Success</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="highlight-item">
              <div class="icon">
                <i class="bi bi-people-fill"></i>
              </div>
              <h3>16:1 Student-Faculty Ratio</h3>
              <p>Proin quis magna vel dolor mattis hendrerit.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="highlight-item">
              <div class="icon">
                <i class="bi bi-globe-americas"></i>
              </div>
              <h3>Global Community</h3>
              <p>Vestibulum sodales dignissim ipsum id commodo.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="event-banner">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-2">
              <div class="event-date">
                <span class="month">OCT</span>
                <span class="day">28</span>
              </div>
            </div>
            <div class="col-md-8">
              <h3>Open Campus Day</h3>
              <p>Experience our vibrant campus life, meet faculty members, and learn about our academic programs.</p>
            </div>
            <div class="col-md-2">
              <a href="#" class="btn-register">Register</a>
            </div>
          </div>
        </div>
      </div>

    </section><!-- /Hero Section -->
  </main>

  <footer id="footer" class="footer position-relative dark-background">
    <div class="container">
      <div class="row align-items-center py-4">
        <div class="col-md-6">
          <div class="d-flex align-items-center">
            <a href="index.html" class="logo d-flex align-items-center me-4">
              <i class="bi bi-buildings me-2"></i>
              <span class="sitename">MySchool</span>
            </a>
            <div class="contact-info">
              <span class="me-3"><i class="bi bi-telephone me-1"></i> +1 (555) 987-6543</span>
              <span><i class="bi bi-envelope me-1"></i> hello@myschool.com</span>
            </div>
          </div>
        </div>
        <div class="col-md-6 text-md-end">
          <div class="legal-links">
            <a href="#" class="me-3">Privacy Policy</a>
            <a href="#" class="me-3">Terms of Service</a>
            <span class="text-muted">© 2025 MySchool. All rights reserved.</span>
          </div>
        </div>
      </div>
    </div>
  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>

  <!-- Vendor JS Files -->
  <script src="{{ asset('landing/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('landing/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ asset('landing/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('landing/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ asset('landing/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>

  <!-- Main JS File -->
  <script src="{{ asset('landing/assets/js/main.js') }}"></script>

</body>

</html>