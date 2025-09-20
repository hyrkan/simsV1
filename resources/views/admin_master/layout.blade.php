<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>Sample Page | Mantis Bootstrap 5 Admin Template</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description" content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
  <meta name="keywords" content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
  <meta name="author" content="CodedThemes">

  <!-- [Favicon] icon -->
  <link rel="icon" href="{{asset('admin/assets/images/favicon.svg')}}" type="image/x-icon"> <!-- [Google Font] Family -->
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap" id="main-font-link">
<!-- [Tabler Icons] https://tablericons.com -->
<link rel="stylesheet" href="{{asset('admin/assets/fonts/tabler-icons.min.css')}}">	
<!-- [Feather Icons] https://feathericons.com -->
<link rel="stylesheet" href="{{asset('admin/assets/fonts/feather.css')}}">
<!-- [Font Awesome Icons] https://fontawesome.com/icons -->
<link rel="stylesheet" href="{{asset('admin/assets/fonts/fontawesome.css')}}">		
<!-- [Material Icons] https://fonts.google.com/icons -->
<link rel="stylesheet" href="{{asset('admin/assets/fonts/material.css')}}">
<!-- [Template CSS Files] -->
<link rel="stylesheet" href="{{asset('admin/assets/css/style.css')}}" id="main-style-link" >
<link rel="stylesheet" href="{{asset('admin/assets/css/style-preset.css')}}">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->
<body>
  @include('admin_master.sidebar')
  @include('admin_master.header')

    <!-- [ Main Content ] start -->
    <div class="pc-container">
      <div class="pc-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
          <div class="page-block">
            <div class="row align-items-center">
              <div class="col-md-12">
                <div class="page-header-title">
                  <h5 class="m-b-10">Sample Page</h5>
                </div>
                <ul class="breadcrumb">
                  <li class="breadcrumb-item"><a href="../dashboard/index.html">Home</a></li>
                  <li class="breadcrumb-item"><a href="javascript: void(0)">Other</a></li>
                  <li class="breadcrumb-item" aria-current="page">Sample Page</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <!-- [ breadcrumb ] end -->

        <div class="row">
          <div class="col-sm-12">
              @yield('content')
          </div>
        </div>
      </div>
    </div>
  
    @include('admin_master.footer')
  <!-- Required Js -->
  <script src="{{asset('admin/assets/js/plugins/popper.min.js')}}"></script>
  <script src="{{asset('admin/assets/js/plugins/simplebar.min.js')}}"></script>
  <script src="{{asset('admin/assets/js/plugins/bootstrap.min.js')}}"></script>
  <script src="{{asset('admin/assets/js/fonts/custom-font.js')}}"></script>
  <script src="{{asset('admin/assets/js/pcoded.js')}}"></script>
  <script src="{{asset('admin/assets/js/plugins/feather.min.js')}}"></script>
  </body>
</html>