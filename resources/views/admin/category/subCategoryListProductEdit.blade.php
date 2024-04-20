@foreach ($subcategories as $subcategory)
    <ul>
        <li><a href="javascript:void(0)" id="subcategory" class="category_items @if ($subcategory->id == $product->category_id) {{ 'active' }} @endif" data_item="{{ $subcategory->id }}">{{ $subcategory->name }}</a></li>
        @if (count($subcategory->subcategory))

            @include('admin.category.subCategoryListProductEdit',['subcategories' => $subcategory->subcategory,'product'=>$product])
        @endif
    </ul>
@endforeach
