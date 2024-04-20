 
<ul style="display:none" class="ul-subcat-page" id="item_next_{{$id}}">
    @foreach($subcategories as $subcategory)
    <li><a href="{{count($subcategory->subcategory)?'javascript:void(0)': url('productlisting/category/' . $subcategory->name)}}"  id="subcategory{{$subcategory->id}}"   onClick="openDropitem({{$subcategory->id}})">
        <i class="fas fa-chevron-right"></i>{{ $subcategory->name }}

    </a></li>
    @if(count($subcategory->subcategory))
    @include('frontview_customer.categorypageSubcategories',['subcategories' => $subcategory->subcategory,'id'=>$subcategory->id])
    @endif
    @endforeach

</ul>
