@extends('layouts.admin')

@section('content')
    @if (session('error'))
        <div class="alert alert-danger">
            <ul>
                <li>{{ session('error') }}</li>
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
        <h1 class="h3 mb-2 text-gray-800">Home Page Category Selection</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <form action="" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-4">
                            <div class="card-header">Selected Categories:</div>
                            <br>
                            <div class="form-group">
                                <ul>
                                    @foreach ($Category_data as $key => $category)
                                        @if (in_array($category->id, $arr_selected))
                                            <div class="form-group">
                                                <li>
                                                 <span>* <b>{{ $category->name }}</b></span>

                                                </li>
                                            </div>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <div class="form-group">
                                    <div class="card-header">Select Categories: <span class="text-danger">(You can add 6 categories. Selected categories displays in home page.)</span> <span class="text-danger" id="remain_category"></span></div>
                                    <div class="card-body category_list_home category-list-block" style="height: 710px;">
                                        <ul>
                                            @foreach ($parentCategories as $key => $category)
                                                <div class="form-group">
                                                    <li>
                                                        <input type="checkbox" name="categories[]" id="categories_{{ $category->id }}" class="categories_chbox" value="{{ $category->id }}" {{ in_array($category->id, $arr_selected) ? 'checked' : '' }} /> <label for="categories_{{ $key }}">{{ $category->name }}</label>
                                                        @if (count($category->subcategory))
                                                         @include('admin.contentpage.homeSubCategoryList',['subcategories' => $category->subcategory,'arr_selected'=>$arr_selected ])
                                                        @endif
                                                    </li>
                                                </div>
                                            @endforeach

                                        </ul>
                                    </div>
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
@endsection
@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $('.alert-success').delay(3000).fadeOut();
            countCalculate();
        });

        $('.categories_chbox').on('change', function() {
            var count_checked = $('.categories_chbox:checked').length;
            if ($(this).is(':checked')) {
                if ((count_checked - 1) >= 6) {
                    $(this).prop('checked', false);
                    alert('Only choose 6 categories. Exceed your limit.');
                }
            }
            countCalculate();
        });

        function countCalculate() {
            var count_checked = $('.categories_chbox:checked').length;
            $('#remain_category').text(6 - count_checked + ' left');
        }
    </script>
@endsection
