<nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <div class="m-header">
        <a href="../dashboard/index.html" class="b-brand text-primary">
          <!-- ========   Change your logo from here   ============ -->
          <img src="{{asset('admin/assets/images/logo-dark.svg')}}" class="img-fluid logo-lg" alt="logo">
        </a>
      </div>
      <div class="navbar-content">
        <ul class="pc-navbar">
          <li class="pc-item">
            <a href="../dashboard/index.html" class="pc-link">
              <span class="pc-micon"><i class="ti ti-dashboard"></i></span>
              <span class="pc-mtext">Dashboard</span>
            </a>
          </li>
  
          <li class="pc-item pc-caption">
            <label>Academics</label>
            <i class="ti ti-dashboard"></i>
          </li>
          <li class="pc-item">
            <a href="{{route('academic-terms.index')}}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-typography"></i></span>
              <span class="pc-mtext">Academic Term</span>
            </a>
          </li>
          <li class="pc-item pc-hasmenu">
            <a href="#!" class="pc-link">
              <span class="pc-micon"><i class="ti ti-school"></i></span>
              <span class="pc-mtext">Academic Management</span>
              <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            </a>
            <ul class="pc-submenu">
              <li class="pc-item">
                <a class="pc-link" href="{{route('departments.index')}}">Departments</a>
              </li>
              <li class="pc-item">
                <a class="pc-link" href="{{route('programs.index')}}">Programs</a>
              </li>
              <li class="pc-item">
                <a class="pc-link" href="{{route('curricula.index')}}">Curriculum</a>
              </li>
            </ul>
          </li>
          <li class="pc-item pc-hasmenu">
            <a href="#!" class="pc-link">
              <span class="pc-micon"><i class="ti ti-clipboard-check"></i></span>
              <span class="pc-mtext">Exam Management</span>
              <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
            </a>
            <ul class="pc-submenu">
              <li class="pc-item">
                <a class="pc-link" href="{{route('exams.index')}}">All Exams</a>
              </li>
              <li class="pc-item">
                <a class="pc-link" href="{{route('exams.create')}}">Create Exam</a>
              </li>
            </ul>
          </li>
  
          <li class="pc-item pc-caption">
            <label>Admin</label>
            <i class="ti ti-news"></i>
          </li>
          <li class="pc-item">
            <a href="{{route('create-staff')}}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-user-plus"></i></span>
              <span class="pc-mtext">Register</span>
            </a>
          </li>
          <li class="pc-item">
            <a href="{{route('staff-list')}}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-user-plus"></i></span>
              <span class="pc-mtext">Staff List</span>
            </a>
          </li>
  
          <li class="pc-item pc-caption">
            <label>Settings</label>
            <i class="ti ti-brand-chrome"></i>
          </li>
          <li class="pc-item">
            <a href="{{route('admin.roles')}}" class="pc-link">
              <span class="pc-micon"><i class="ti ti-shield-lock"></i></span>
              <span class="pc-mtext">Roles and Permissions</span>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>