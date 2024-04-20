    <!--nav-->
    <nav class="navbar navbar-expand-lg navbar-light nav-header">
        <div class="width-container">
            <!--<a class="navbar-brand" href="index.html"></a>-->
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse inner-main-links" id="navbarNav">
                <ul class="navbar-nav head-nav-links">
                    <div class="dropdown">
                        <li class="nav-item active">
                            <a class="nav-link" href="javascript:void(0)" id="dropdownCategorieslist" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">All Categories <i class="fas fa-chevron-down"></i><span class="sr-only">(current)</span></a>
                            <div class="dropdown-menu" aria-labelledby="dropdownCategorieslist" style="max-height: 350px; overflow-y: scroll;">
                                @foreach($AllCategories as $AllCategories_Row)
                                    <a class="dropdown-item" href="{{ route('shopping.productlisting', $AllCategories_Row->name) }}">
                                        @if($AllCategories_Row->image != '')
                                            <img src="{{ asset('assets/uploads/category') }}/{{ $AllCategories_Row->image }}">
                                        @else
                                            <img src="{{ asset('front_view/images/grid.png') }}">
                                        @endif
                                        {{ $AllCategories_Row->name }}
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    </div>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    @if(!empty($ContentPages))
                        @foreach($ContentPages as $ContentPages_Row)
                            @php $PagePosition = explode(',', $ContentPages_Row->page_position); @endphp
                            @if(in_array('Top', $PagePosition))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('view.contentpage', $ContentPages_Row->seo_url) }}">{{ $ContentPages_Row->page }}</a>
                                </li>
                            @endif
                        @endforeach
                    @endif
                </ul>
            </div>
        </div>
    </nav>
    <!--END-nav-->
