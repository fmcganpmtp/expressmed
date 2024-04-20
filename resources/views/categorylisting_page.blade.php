@extends('layouts.frontview.app')


@section('content')
    <!--nav-->
    @include('layouts.frontview.topmenubar_frontview')
    <!--END-nav-->


    <div class="width-container">
        <div class="row">
            <div class="col-md-12 left-menu-bar-outer">


                <ul class="navbar-nav med-cat-page">
                    @foreach ($AllmedcinesubCategories as $AllCategories_Row)
                        <li class="dropright">
                            <a href="#!" onClick="openDropitem({{ $AllCategories_Row->id }})">
                                @if ($AllCategories_Row->image != '')
                                    <img src="{{ asset('assets/uploads/category') }}/{{ $AllCategories_Row->image }}">
                                @else
                                    <img src="{{ asset('front_view/images/grid.png') }}">
                                @endif
                                {{ $AllCategories_Row->name }}
                            </a>
                            @if (count($AllCategories_Row->subcategory))

                                @include('frontview_customer.categorypageSubcategories',['subcategories' => $AllCategories_Row->subcategory,'id'=>$AllCategories_Row->id])

                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>






@endsection

@section('footer_scripts')
    <script>
        function openDropitem(id) {
            $("#item_next_" + id).toggle();
        }
    </script>
@endsection
