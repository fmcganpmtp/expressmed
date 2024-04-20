   @foreach ($subcategories as $subcategory)
       @php $sub_parent_id=$subcategory->parent->id; @endphp

       <li><a class="dropdown-item {{ $sub_parent_id == $parent_id || count($subcategory->subcategory) ? 'title-class' : 'sub-class' }}" href="{{ url('productlisting/category/' . $subcategory->name) }}" id="subcategory" data_item="{{ $subcategory->id }}" style="{{ $sub_parent_id == $parent_id || count($subcategory->subcategory) ? 'color: #000000' : 'color: #757575;' }}">{{ $subcategory->name }}</b></a>
           @if (count($subcategory->subcategory))
               @include('frontview_customer.homesubCategories',['subcategories' => $subcategory->subcategory])

           @endif

       </li>
   @endforeach
