@extends('layouts.admin')

@section('content')

    @if (count($errors) > 0)

        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    @if (session('success'))
        <div class="alert alert-success">
            <ul>
                <li>{{ session('success') }}</li>
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Product Category</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <div class="card-body">
                        <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.categories') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                        <form action="" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <strong>Category Name:</strong>
                                        <input type="text" name="name" value="{{ old('name') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <div class="card-header">Parent Category:</div>
                                        <div class="card-body category-list-block">
                                            @php

                                                $old_sub_category  = (!empty(old('selected_category')) && old('selected_category') != '' ? old('selected_category') : 0);
                                                @endphp
                                            @foreach ($parentCategories as $category)
                                                <ul>
                                                    <li>
                                                        <a href="javascript:void(0)" id="category" data_item="{{ $category->id }}" class="category_items @if ($category->id == $old_sub_category) {{ 'active' }} @endif">{{ $category->name }}</a>
                                                        @if (count($category->subcategory))
                                                            @include('admin.category.subCategoryList',['subcategories' => $category->subcategory,'old_sub_category'=>$old_sub_category ])
                                                        @endif
                                                    </li>
                                                </ul>
                                            @endforeach
                                            <input type="hidden" name="selected_category" value="" id="selected_category">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <strong> Description: </strong>
                                        <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                                    </div>
                                </div>
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <strong> Image </strong><span class="text-danger">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>
                                        <input type="file" name="image" value="{{ old('image') }}" class="form-control">
                                    </div>
                                </div>
                                <div class="col-xs-8 col-sm-8 col-md-8">
                                    <div class="form-group">
                                        <input type="submit" value="submit" class="btn btn-primary">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer_scripts')

    <script type="text/javascript">
        $(document).on("click", ".category_items", function() {
            var category = $(this).attr("data_item");
            $('.active').removeClass("active");
            $(this).addClass("active");
            $("#selected_category").val(category);
        });
    </script>

    <style>
        .active {
            color: #FF0000;
        }

    </style>

@endsection
