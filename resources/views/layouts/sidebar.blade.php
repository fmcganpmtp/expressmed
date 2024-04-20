<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ url('admin') }}">
        <img src="{{ asset('img/logo.png') }}" width="100%">
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider my-0">

    <!-- Nav Item - Dashboard -->
    <li class="nav-item active">
        <a class="nav-link" href="{{ route('admin.Index') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Dashboard</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading -->

    <!-- Nav Item - Pages Collapse Menu -->
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.list') }}">
            <i class="fa fa-user" aria-hidden="true"></i>
            <span>Administrators</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('customersupport.index') }}">
            <i class="fa fa-headset" aria-hidden="true"></i>
            <span>Customer Support</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.testimonials') }}">
            <i class="fa fa-comment" aria-hidden="true"></i>
            <span>Testimonials</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.careers') }}">
            <i class="fa fa-briefcase" aria-hidden="true"></i>
            <span>Careers</span>
        </a>

    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.news') }}">
            <i class="fas fa-newspaper"></i>
            <span>News And Events</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.customers') }}">
            <i class="fa fa-users"></i>
            <span>Customers</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.prescription') }}">
            <i class="fas fa-file-medical"></i>
            <span>Prescription</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.generalprescription') }}">
            <i class="fas fa-file-medical"></i>
            <span>General Prescription</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#products" aria-expanded="true" aria-controls="products">
            <i class="fas fa-boxes"></i>
            <span>Products</span>
        </a>
        <div id="products" class="collapse" aria-labelledby="headingPages">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.products') }}">
                    <i class="fas fa-list"></i>
                    <span>Listings</span>
                </a>
            </div>
           <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.brands') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Brands</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.categories') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Categories</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.producttype') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Type</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.productcontent') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Content</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.taxes') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Tax</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.supplier') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Supplier</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.medicineUse') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Use</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.manufacturers') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Manufacturer</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('products.removed') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Removed Products</span>
                </a>
            </div>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.stores') }}">
            <i class="fas fa-bars"></i>
            <span>Stores</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('teams.index') }}">
            <i class="fas fa-users"></i>
            <span>Teams</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('doctor.index')}}">
        <i class="fas fa-user-md"></i>
            <span>Doctors</span>
        </a>
    </li>


    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.orders') }}">
            <i class="fas fa-bars"></i>
            <span>Orders</span>
        </a>
    </li>
    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
           aria-expanded="true" aria-controls="collapsePages">
            <i class="fas fa-truck-loading"></i>
            <span>Delivary App</span>
        </a>
        <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href=""><i class="fas fa-truck"></i><span>&nbsp;Delivary Types</span></a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href=""><i class="fas fa-people-carry"></i><span>&nbsp;Delivary Boys</span></a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href=""><i class="fas fa-bars"></i><span> Scheduled Orders</span></a>
            </div>
        </div>
    </li> --}}

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.promotionbanner') }}">
            <i class="fas fa-ad"></i>
            <span>Promotion Banners</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages2" aria-expanded="true" aria-controls="collapsePages2">
            <i class="fa fa-window-maximize"></i>
            <span>Page Setup</span>
        </a>
        <div id="collapsePages2" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.contentpages') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Content Pages</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.home.categories') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Home Display Category</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.home.offersection') }}">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Offer Link Section</span>
                </a>
            </div>

        </div>
    </li>

    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#promotions" aria-expanded="true" aria-controls="promotions">
            <i class="fas fa-ad"></i>
            <span>Promotions</span>
        </a>
        <div id="promotions" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Options</span>
                </a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Featured Products</span></a>
            </div>
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Promotion Banners</span></a>
            </div>
        </div>
    </li> --}}

    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.settings') }}">
            <i class="fas fa-cog"></i>
            <span>General Settings</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('admin.socialmedia') }}">
            <i class="fas fa-share-square"></i>
            <span>Social Media Links</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link collapsed" href="{{ route('newsletter.index') }}">
            <i class="fa fa-envelope"></i>
            <span>News Letter</span>
        </a>
    </li>

    <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Reports" aria-expanded="true" aria-controls="Reports">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
        </a>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.product') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Product Reports</span>
                </a>
            </div>
        </div>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.order') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Order Reports</span>
                </a>
            </div>
        </div>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="{{ route('admin.reports.sales') }}" >
                    <i class="fas fa-grip-horizontal"></i>
                        <span>Sales Reports</span>
                </a>
            </div>
        </div>
    </li>

    {{-- <li class="nav-item">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#Reports" aria-expanded="true" aria-controls="Reports">
            <i class="fas fa-file-alt"></i>
            <span>Reports</span>
        </a>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Product Reports</span>
                </a>
            </div>
        </div>
        <div id="Reports" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <a class="collapse-item" href="">
                    <i class="fas fa-grip-horizontal"></i>
                    <span>Order Reports</span>
                </a>
            </div>
        </div>
    </li> --}}

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
