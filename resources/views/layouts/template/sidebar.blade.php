<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion toggled" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/home">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-cube"></i>
        </div>
        <div class="sidebar-brand-text mx-3">PerDin</sup></div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link {{ request()->segment('1') == '' ? 'active' : '' }}" href="{{ url('home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Master Menu for Super Admin (type == 1) -->
    @if (auth()->user() && auth()->user()->type == 1)
        <li class="nav-item active">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMaster"
                aria-expanded="true" aria-controls="collapseMaster">
                <i class="fas fa-fw fa-cog"></i>
                <span>Master</span>
            </a>
            <div id="collapseMaster" class="collapse" aria-labelledby="headingMaster" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <a class="collapse-item" href="{{ route('users.index') }}">User</a>
                    <a class="collapse-item" href="{{ route('roles.index') }}">Role</a>
                    <a class="collapse-item" href="{{ route('products.index') }}">Products</a>
                </div>
            </div>
        </li>
    @endif


    <!-- Manager Menu (type == 2) -->
    @if (auth()->user() && auth()->user()->type == 2)
        <li class="nav-item active">
            <a class="nav-link collapsed" href="{{ route('manager.index') }}">
                <i class="fas fa-fw fa-briefcase"></i>
                <span>Work Order</span>
            </a>
        </li>
    @endif



    <!-- Operator Menu (type == 3) -->
    @if (auth()->user() && auth()->user()->type == 3)
        <li class="nav-item active">
            <a class="nav-link collapsed" href="{{ route('operator.index') }}">
                <i class="fas fa-fw fa-briefcase"></i>
                <span>Work Order</span>
            </a>
        </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->

<!-- Tambahkan kode JavaScript untuk pengalihan ke halaman login -->
<script>
    // Cek apakah pengguna belum terautentikasi atau properti 'type' pada pengguna adalah null
    @if (!auth()->check() || auth()->user()->type === null)
        // Alihkan pengguna ke halaman login
        window.location = "{{ route('login') }}";
    @endif
</script>
