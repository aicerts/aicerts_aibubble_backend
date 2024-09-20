<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
        <div class="sidebar-brand-icon">
            <img src="{{ asset('assets/logo.png') }}" alt="{{ env('APP_NAME') }} Logo" style="width: 100%; max-height: 40px;">
        </div>
    </a>


    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->
    <div class="sidebar-heading">
        Management
    </div>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDown2" aria-expanded="true" aria-controls="taTpDropDown2">
            <i class="fas fa-user-alt"></i>
            <span>Customer Management</span>
        </a>
        <div id="taTpDropDown2" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Customer Management:</h6>
                <a class="collapse-item" href="{{ route('customer.index') }}">List</a>
            </div>
        </div>
    </li>


    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Stock Symbols
    </div>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDown9" aria-expanded="true" aria-controls="taTpDropDown7">
            <i class="fas fa-user-alt"></i>
            <span>Symbols Management</span>
        </a>
        <div id="taTpDropDown9" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Symbols Management:</h6>
                <a class="collapse-item" href="{{ route('symbol.index') }}">List</a>
            </div>
        </div>
    </li>


    <!-- Nav Item - Pages Collapse Menu -->
    {{--<li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDown" aria-expanded="true" aria-controls="taTpDropDown">
            <i class="fas fa-user-alt"></i>
            <span>Admin Management</span>
        </a>
        <div id="taTpDropDown" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Admin Management:</h6>
                <a class="collapse-item" href="{{ route('users.index') }}">List</a>
    <a class="collapse-item" href="{{ route('users.create') }}">Add New</a>
    <a class="collapse-item" href="{{ route('users.import') }}">Import Data</a>
    </div>
    </div>
    </li>--}}


    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Settings Section
    </div>


    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#taTpDropDown7" aria-expanded="true" aria-controls="taTpDropDown7">
            <i class="fas fa-user-alt"></i>
            <span>Settings</span>
        </a>
        <div id="taTpDropDown7" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Settings Management:</h6>
                <a class="collapse-item" href="{{ route('settings.terms') }}">Terms & Conditions</a>
                <a class="collapse-item" href="{{ route('settings.privacy') }}">Privacy Policy</a>
            </div>
        </div>
    </li>

    {{--


    <!-- Divider -->
    <hr class="sidebar-divider">

    @hasrole('Admin')
    <!-- Heading -->
    <div class="sidebar-heading">
        Admin Section
    </div>

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages" aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-fw fa-folder"></i>
            <span>Masters</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Role & Permissions</h6>
                <a class="collapse-item" href="{{ route('roles.index') }}">Roles</a>
    <a class="collapse-item" href="{{ route('permissions.index') }}">Permissions</a>
    </div>
    </div>
    </li>

    @endhasrole

    --}}

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">


    <li class="nav-item">
        <a class="nav-link" href="#" data-toggle="modal" data-target="#logoutModal">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </a>
    </li>
    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>


</ul>