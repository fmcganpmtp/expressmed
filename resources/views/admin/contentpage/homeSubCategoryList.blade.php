
<ul>
    @foreach($subcategories as $subcategory)
        <li>
            <input type="checkbox" name="categories[]" id="categories_{{ $subcategory->id }}"  class="categories_chbox" value="{{ $subcategory->id }}" {{ in_array($subcategory->id, $arr_selected) ? 'checked' : '' }} /> <label for="categories_{{ $key }}">{{ $subcategory->name }}</label>

            @if(count($subcategory->subcategory))
                @include('admin.contentpage.homeSubCategoryList',['subcategories' => $subcategory->subcategory])
            @endif
        </li>
    @endforeach
</ul>

