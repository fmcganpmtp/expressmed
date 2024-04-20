<ul>
    @foreach ($subcategories as $subcategory)
        @if ($subcategory->id != $categories->id)
            <li>
                <a href="javascript:void(0)" id="subcategory" class="category_items @if ($subcategory->id == $categories->parent_id) {{ 'active' }} @endif" data_item="{{ $subcategory->id }}">{{ $subcategory->name }}</a>
                @if (count($subcategory->subcategory))
                    @include('admin.category.subCategoryListEdit',['subcategories' => $subcategory->subcategory])
                @endif
            </li>
        @endif
    @endforeach
</ul>
