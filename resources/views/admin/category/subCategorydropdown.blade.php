
{{-- <option> --}}
    @foreach($subcategories as $subcategory)
        <option value="{{$subcategory->id}}"@if(count($subcategory->subcategory)>0) style="color:#000000" @endif @if($subcategory->id==$selcatId) selected  @endif>
            <a href="javascript:void(0)" id="subcategory" class="category_items" data_item="{{$subcategory->id}}" aria-valuemax="{{$subcategory->id}}">

                @if(count($subcategory->subcategory)==0)&nbsp  @endif {{$subcategory->name}} </a>
            @if(count($subcategory->subcategory))
                @include('admin.category.subCategorydropdown',['subcategories' => $subcategory->subcategory])
            @endif
        </option>
    @endforeach
{{-- </option> --}}

