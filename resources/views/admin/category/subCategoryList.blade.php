
<ul>
    @foreach($subcategories as $subcategory)
        <li>
            <a href="javascript:void(0)" id="subcategory" class="category_items  @if ($subcategory->id == $old_sub_category) {{ 'active' }} @endif" data_item="{{$subcategory->id}}">{{$subcategory->name}}</a>
            @if(count($subcategory->subcategory))
                @include('admin.category.subCategoryList',['subcategories' => $subcategory->subcategory,'old_sub_category'=>$old_sub_category])
            @endif
        </li>
    @endforeach
</ul>

