<ul>
    @foreach ($subcategories as $subcategory)
        <li>
            <a href="{{ route('categories.product', $subcategory->name) }}" id="subcategory" class="category_items" data_item="{{ $subcategory->id }}">{{ $subcategory->name }}</a>
            @if (count($subcategory->subcategory))
                @include('admin.category.subCategoryInclude',['subcategories' => $subcategory->subcategory])
            @endif
        </li>
    @endforeach
</ul>
