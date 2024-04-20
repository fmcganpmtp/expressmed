<ul class="submenu dropdown-menu">
    @foreach($subcategories as $subcategory)
            <li><a href="{{count($subcategory->subcategory)?'javascript:void(0)': url('productlisting/category/' . $subcategory->name)}}" id="subcategory" class="category_items" data_item="{{$subcategory->id}}">{{$subcategory->name}}</a>
            @if(count($subcategory->subcategory))
            @include('frontview_customer.subCategoryList',['subcategories' => $subcategory->subcategory])

            @endif
            </li>
    @endforeach

</ul>
