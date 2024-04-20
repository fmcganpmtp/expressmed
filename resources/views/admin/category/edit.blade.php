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
        <h1 class="h3 mb-2 text-gray-800">Update</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.categories') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form method="post" action="" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Title:</strong>
                                <input type="text" name="name" value="{{ $categories->name }}" class="form-control" required />
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="card-header">Parent Category:</div>
                                <div class="card-body category-list-block">
                                    <ul>
                                        <li><a href="javascript:void(0)" id="category" data_item="0" class="category_items @if ($categories->parent_id == 0) {{ 'active' }} @endif">Parent</a></li>
                                    </ul>
                                    @foreach ($parentCategories as $category)
                                        <ul>
                                            <li>
                                                <a href="javascript:void(0)" id="category" data_item="{{ $category->id }}" class="category_items @if ($category->id == $categories->parent_id) {{ 'active' }} @endif">{{ $category->name }}</a>
                                                @if (count($category->subcategory))
                                                    @include('admin.category.subCategoryListEdit',['subcategories' => $category->subcategory])
                                                @endif
                                            </li>
                                        </ul>
                                    @endforeach
                                    <input type="hidden" name="selected_category" value="{{ $categories->parent_id }}" id="selected_category">
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div id="image-block">
                                @if ($categories->image != '')
                                    <img src="{{ asset('/assets/uploads/category/') }}/{{ $categories->image }}" alt="{{ $categories->image }}" width="50px" />
                                    <a href="javascript:void(0)" onclick="removeImage({{ $categories->id }})" class="btn btn-danger btn-circle btn-md"><i class="far fa-times-circle"></i></a>
                                @endif
                            </div>
                            <div class="form-group">
                                <strong>Image:</strong><span class="text-danger">(Max Image dimension width:50 x height:50 pixel, Max: 1MB)</span>
                                <input id="profile_pic" type="File" class="course-img" name="image">
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong>Description:</strong>
                                <textarea id="description" name="description" class="form-control">{{ $categories->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <input type="submit" value="Update" class="btn btn-primary">
                            </div>
                        </div>
                    </div>
                </form>
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

        function removeImage(id = null){
            if(confirm('Do you want to remove image?')){
                if(id != null){
                    $.ajax({
                        type:'POST',
                        url:'{{ route("categories.removeImage") }}',
                        data:{id: id, '_token':'{{csrf_token()}}'},
                        success:function(response){
                            if(response.result){
                                $('#image-block').parent().prepend('<span class="text-success" id="alert_image">Category image removed successfully.</span>');
                                $('#alert_image').delay(2000).fadeOut();
                                $('#image-block').remove();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                } else {
                    alert('Image remove failed. Something went wrong.');
                }
            }
        }
    </script>

    <style>
        .active {
            color: #FF0000;
        }

    </style>
@endsection
