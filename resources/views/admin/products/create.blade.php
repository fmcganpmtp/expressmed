@extends('layouts.admin')

@section('content')

    <style>
        .medi-uses {
            height: 600px;
            overflow-y: scroll;
        }

    </style>

    @if (count($errors) > 0)
        <div class="alert alert-danger">
            <strong>{{ _('messages.Whoops') }}!</strong> {{ _('messages.There were some problems with your input') }}.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (\Session::has('errormsg'))
        <div class="alert alert-danger">
            <ul>
                <li>{!! \Session::get('errormsg') !!}</li>
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

    <div class="modal fade" id="modalMedicaluse" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" style="max-width: 1000px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Medicine For</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="radio" id="treatment" name="medicine_for" value="treatment of">
                            <label for="treatment">Treatment of</label>
                        </div>
                        <div class="col-md-3">
                            <input type="radio" id="usedin" name="medicine_for" value="used in">
                            <label for="usedin">Used In</label>
                        </div>
                        <div class="col-md-3">
                            <input type="radio" id="usedas" name="medicine_for" value="used as">
                            <label for="usedas">Used As</label>
                        </div>
                        <div class="col-md-3">
                            <input type="radio" id="support" name="medicine_for" value="support">
                            <label for="Support">Support</label>
                        </div>
                    </div>
                </div>
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Medicine Uses</h5>
                </div>


                <div class="modal-body medi-uses">
                    <div class="row ">
                        @csrf
                        @foreach ($medical_uses as $medical_use)
                            <div class="col-md-6">

                                <div class="p-2 rounded checkbox-form">
                                    <div class="form-check"> <input class="form-check-input" name='medical_use' type="checkbox" value="{{ $medical_use->id }}" data-name="{{ $medical_use->name }}" id="med_check"> <label class="form-check-label">{{ $medical_use->name }}</label> </div>
                                </div>
                            </div>

                        @endforeach


                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id='select_medical_uses'>Save </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <h1 class="h3 mb-2 text-gray-800">Products</h1>
        <div class="card shadow mb-4">
            <div class="card-body">
                <a class="btn btn-primary btn-circle btn-lg" href="{{ route('admin.products') }}" title="{{ __('messages.Back to Listings') }}"> <i class="fa fa-arrow-left" aria-hidden="true"></i></a>
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        @csrf
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="product_name"><strong>Product Name</strong><span class="text-danger">*</span></label>
                                <input type="text" name="product_name" id="product_name" value="{{ old('product_name') }}" class="form-control">
                            </div>
                        </div>

                        {{-- <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="tagline"><strong>Tag Line</strong></label>
                                <input type="text" name="tagline" id="tagline" class="form-control" placeholder="Tag Line about Product" value="{{ old('tagline') }}">
                            </div>
                        </div> --}}

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="producttype"><strong>Product Type</strong><span class="text-danger">*</span></label>
                                <select name="producttype" id="producttype" class="form-control">
                                    <option value="" selected>Select Type</option>
                                    @foreach ($parentCategories as $category)
                                        <option value="{{ $category->id }}" data-name="{{ $category->name }}"  {{old('producttype') == $category->id?'selected':''}} >{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <div class="card-header">Select Category:</div>
                                <div class="card-body category-list-block">
                                    {{-- @foreach ($parentCategories as $category) --}}
                                    <ul>
                                        <li><a href="javascript:void(0)" id="category"> </a></li>
                                        {{-- @if (count($category->subcategory)) --}}
                                        {{-- @include('admin.category.subCategoryList',['subcategories' => $category->subcategory]) --}}
                                        {{-- @endif --}}
                                    </ul>
                                    {{-- @endforeach --}}
                                    <input type="hidden" name="selected_category" id="selected_category" value="{{ old('selected_category') }}">
                                </div>
                            </div>
                        </div>

                        {{-- Choose Product Content Start --}}
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label>Selected Product Contents</label>
                                <div id="product_content_selected">
                                    @if (!empty(old('product_contents')) && old('product_contents.0') != '')
                                        @foreach (old('product_contents') as $key => $value)
                                            <div class="contents_added">{{ old('product_contentname.' . $key) }}<input type="hidden" name="product_contentname[]" value="{{ old('product_contentname.' . $key) }}" /><input type="hidden" name="product_contents[]" value="{{ $value }}" /><a href="javascript:void(0)" class="remove_productcontents"><i class="fas fa-times-circle"></i></a></div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="product_content"><strong>Products Content</strong></label>
                                <input type="text" id="product_content" placeholder="Enter Product Contents" class="form-control" autocomplete="off">
                            </div>
                            <div id="productcontents"></div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8 py-3" id="new_productcontent_outer" style="display:none">

                            <div class="input-group mb-3">
                                <input type="text" id="text_productcontent" class="form-control" placeholder="Enter New Product Contents Here" aria-label=" Recipient's username" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="add_productcontent">Add</button>
                                </div>
                            </div>
                        </div>
                        {{-- Choose Product Content End --}}

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="product_description"><strong>Products Description</strong></label>
                                <textarea name="description" id="product_description" class="form-control">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="how_to_use"><strong>How to use</strong></label>
                                <textarea name="how_to_use" id="how_to_use" class="form-control">{{ old('how_to_use') }}</textarea>
                            </div>
                        </div>

                        {{-- <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="benefits"><strong>Benefits</strong></label>
                                <textarea name="benefits" id="benefits" class="form-control">{{ old('benefits') }}</textarea>
                            </div>
                        </div> --}}

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="side_effects"><strong>Side Effects</strong></label>
                                <textarea name="side_effects" id="side_effects" class="form-control">{{ old('side_effects') }}</textarea>
                            </div>
                        </div>

                        <div id="medicine_use_for">
                            @if (!empty(old('medicine_for')))

                                <div class="medicinefor_added"><input type="hidden" name="medicine_for" value="{{ old('medicine_for') }}" /><a href="javascript:void(0)" class="remove_medicine_for"><i class="fas fa-times-circle"></i></a></div>

                            @endif

                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">

                                <div id="medical_uses_selected">
                                    @if (!empty(old('medicine_uses')) && old('medicine_uses.0') != '')
                                        @foreach (old('medicine_uses') as $key => $value)

                                            <div class="medicaluse_added">{{ old('medicine_uses_for.' . $key) }} {{ old('medicine_uses_name.' . $key) }}<input type="hidden" name="medicine_uses_name[]" value="{{ old('medicine_uses_name.' . $key) }}" /><input type="hidden" name="medicine_uses_for[]" value="{{ old('medicine_uses_for.' . $key) }}" /><input type="hidden" name="medicine_uses[]" value="{{ $value }}" /><a href="javascript:void(0)" class="remove_productcontents"><i class="fas fa-times-circle"></i></a></div>
                                        @endforeach
                                    @endif

                                </div>
                            </div>
                        </div>



                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                {{-- <label for="medicine_use"><strong>Medicine Use</strong></label> --}}
                                {{-- <input type="text" name="medicine_use" id="medicine_use" value="{{ old('medicine_use') != '' ? old('medicine_use') : '' }}" placeholder="Enter Medicine Use" class="form-control" data-toggle="collapse" data-target="#collapse_medicine_use" aria-expanded="false" aria-controls="collapse_medicine_use" autocomplete="off"> --}}
                                <button class="btn btn-outline-primary" data-toggle="modal" data-target="#modalMedicaluse" type="button">Add Medicine Use</button>

                            </div>
                            <div class="collapse" id="collapse_medicine_use"></div>
                        </div>



                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="row">
                                <div class="col-xs-12 col-sm-4 col-md-4 form-group">
                                    <label for="productquantity"><strong>Quantity</strong><span class="text-danger">*</span></label>
                                    <input type="text" name="productquantity" id="productquantity" class="form-control" value="{{ old('productquantity') != '' ? old('productquantity') : 1 }}" placeholder="Quantity" />
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4 form-group">
                                    <label for="productprice"><strong>Price</strong><span class="text-danger">*</span></label>
                                    <input type="text" name="productprice" id="productprice" class="form-control" value="{{ old('productprice') }}" placeholder="Price" />
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4 form-group">
                                    <label for="productofferprice"><strong>Offer Price</strong></label>
                                    <input type="text" name="productofferprice" id="productofferprice" class="form-control" value="{{ old('productofferprice') }}" placeholder="Offer" />
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="product_pack"><strong>Product Pack</strong></label>
                                <input type="text" name="product_pack" id="product_pack" value="{{ old('product_pack') != '' ? old('product_pack') : '' }}" placeholder="Enter Product Pack" class="form-control" autocomplete="off">
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="features"><strong>Features</strong></label>
                                <textarea name="features" id="features" class="form-control" placeholder="Brief Features ">{{ old('features') }}</textarea>
                            </div>
                        </div>

                         <div class="col-xs-8 col-sm-8 col-md-8" id="productbrand">
                            <div class="form-group">
                                <label for="brand"><strong>Product Brand</strong></label>
                                <input type="text" name="brand" id="brand" placeholder="Search Brand" class="form-control" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample" autocomplete="off">
                            </div>
                            <div class="collapse" id="collapseExample"></div>
                            <input type="hidden" name="selected_brand" value="" id="selected_brand">
                            <div id="new_brand_outer" style="display:none">
                                <input type="text" class="form-control" name="new_brand" placeholder="Enter new Brand Here">
                                Logo : <input type="File" class="form-control" name="brand_logo">
                            </div>
                        </div>

                        {{-- Choose Product Supplier Start --}}
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label>Selected Supplier</label>
                                <div id="product_supplier_selected">
                                    @if (!empty(old('supplier')) && old('supplier.0') != '')
                                        @foreach (old('supplier') as $key => $value)
                                            <div class="suppliers_added">{{ old('product_suppliername.' . $key) }}<input type="hidden" name="product_suppliername[]" value="{{ old('product_suppliername.' . $key) }}" /><input type="hidden" name="supplier[]" value="{{ $value }}" /><a href="javascript:void(0)" class="remove_productsupplier"><i class="fas fa-times-circle"></i></a></div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="product_supplier"><strong>Supplier</strong></label>
                                <input type="text" id="product_supplier" placeholder="Enter Supplier" class="form-control" autocomplete="off">
                            </div>
                            <div id="productsupplier_drop"></div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8 py-3" id="new_productsupplier_outer" style="display:none">
                            <div class="input-group mb-3">
                                {{-- <input type="text" id="text_productsupplier" class="form-control" placeholder="Enter New Supplier Here" autocomplete="off"> --}}
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" id="btn_addnewSupplier">Add Supplier</button>
                                </div>
                            </div>
                        </div>
                        {{-- Choose Product Supplier End --}}

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="manufacturer"><strong>Manufactured By</strong></label>
                                <select name="manufacturer" id="manufacturer" class="form-control">
                                    <option value="">Select Manufacturer</option>
                                    @foreach ($manufacturers as $row)
                                        <option  @if (old('manufacturer') == $row->id){{ 'Selected' }} @endif value="{{ $row->id }}">{{ $row->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Choose Product Variants Start --}}
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label>Selected Variant Product</label>
                                <div id="product_variants_selected"></div>
                            </div>
                        </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="variant">Variant Products</label>
                                <input type="text" name="variant" id="variant" placeholder="Enter variant Products" class="form-control" autocomplete="off">
                            </div>
                            <div id="product_variant"></div>
                        </div>
                        {{-- Choose Product Variants End --}}

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <label for="storage"><strong>Stored In &#8451;</strong></label>
                                <input type="text" name="storage" id="storage" class="form-control" placeholder="eg: below 30" value="{{ old('storage') }}">
                            </div>
                        </div>

                        @if ($Taxes->isNotEmpty())
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <strong>Choose Tax</strong>
                                <div class="form-group">
                                    @foreach ($Taxes as $Taxes_row)
                                        <input type="checkbox" id="{{ $Taxes_row->tax_name }}" name="taxes[]" value="{{ $Taxes_row->id }}" {{ !empty(old('taxes')) && in_array($Taxes_row->id, old('taxes')) ? 'checked' : '' }}>
                                        <label for="{{ $Taxes_row->tax_name }}">{{ $Taxes_row->tax_name }} ({{ $Taxes_row->percentage }}%)</label>
                                        &nbsp;
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <input type="checkbox" id="need_prescription" name="need_prescription" value="1" {{ old('need_prescription') == 1 ? 'checked' : '' }}>
                                <label for="need_prescription" class="text-danger"><strong>Check if this product need Doctor's Prescription for sale</strong></label>
                            </div>
                        </div>


                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <div class="form-group">
                                <strong> Product Image </strong><span class="text-danger">(Max Image dimension width:500 x height:500 pixel, Max: 1MB)</span>
                                <div class="input-group control-group increment" required>
                                    <input type="file" name="image[]" class="form-control">
                                    <label for="thumbnail"><strong>Thumbnail:</strong></label>
                                    <input type="radio" id="thumbnail" class="make_thumnail" name="thumbnail[]" value="yes">
                                    <input type="hidden" name="thumnailhid[]" class="hid_thumnail">
                                    <div class="input-group-btn">
                                        <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                                    </div>
                                </div>
                                <div class="fields_extent"></div>
                                <div class="clone hide" style="display: none;">
                                    <div class="control-group input-group" style="margin-top:10px">
                                        <input type="file" name="image[]" class="form-control">
                                        <label for="thumbnail"><strong>Thumbnail:</strong></label>
                                        <input type="radio" class="make_thumnail" name="thumbnail[]" value="yes">
                                        <input type="hidden" name="thumnailhid[]" class="hid_thumnail">
                                        <div class="input-group-btn">
                                            <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <div class="form-group">
                                    <input type="checkbox" id="not_for_sale" name="not_for_sale" value="1" {{ old('not_for_sale') == 1 ? 'checked' : '' }}>
                                    <label for="not_for_sale" class="text-danger"><strong>Check if this product not for sale</strong></label>
                                </div>
                            </div>

                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <div class="form-group">
                                    <input type="checkbox" id="hide_from_site" name="hide_from_site" value="1" {{ old('hide_from_site') == 1 ? 'checked' : '' }}>
                                    <label for="hide_from_front" class="text-danger"><strong>Check if this product hide from website</strong></label>
                                </div>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <div class="form-group">
                                    <input type="submit" value="submit" class="btn btn-primary">
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@section('footer_scripts')

    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        //--Product Description--
        tinymce.init({
            selector: 'textarea#product_description',
            plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
            imagetools_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_sticky: true,
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_prefix: "{path}{query}-{id}-",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            font_formats:"Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago;Poppins=poppins Symbol=symbol;Sans Seriff=sans-serif; Tahoma=tahoma,arial,helvetica; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",

            content_css: '//www.tiny.cloud/css/codepen.min.css',
            link_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_class_list: [{
                    title: 'None',
                    value: ''
                },
                {
                    title: 'Some class',
                    value: 'class-name'
                }
            ],
            importcss_append: true,
            height: 400,
            file_picker_callback: function(callback, value, meta) {
                /* Provide file and text for the link dialog */
                if (meta.filetype === 'file') {
                    callback('https://www.google.com/logos/google.jpg', {
                        text: 'My text'
                    });
                }

                /* Provide image and alt text for the image dialog */
                if (meta.filetype === 'image') {
                    callback('https://www.google.com/logos/google.jpg', {
                        alt: 'My alt text'
                    });
                }

                /* Provide alternative source and posted for the media dialog */
                if (meta.filetype === 'media') {
                    callback('movie.mp4', {
                        source2: 'alt.ogg',
                        poster: 'https://www.google.com/logos/google.jpg'
                    });
                }
            },
            templates: [{
                    title: 'New Table',
                    description: 'creates a new table',
                    content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
                },
                {
                    title: 'Starting my story',
                    description: 'A cure for writers block',
                    content: 'Once upon a time...'
                },
                {
                    title: 'New list with dates',
                    description: 'New List with dates',
                    content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
                }
            ],
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_noneditable_class: "mceNonEditable",
            toolbar_mode: 'sliding',
            contextmenu: "link image imagetools table",
            images_upload_handler: function(blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', "{{ route('content.ajaxtiny') }}");
                xhr.onload = function() {
                    var json;

                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            }
        });

        //--How to use--

            tinymce.init({
            selector: 'textarea#how_to_use',
            plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
            imagetools_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_sticky: true,
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_prefix: "{path}{query}-{id}-",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            content_css: '//www.tiny.cloud/css/codepen.min.css',
            font_formats:"Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago;Poppins=poppins Symbol=symbol;Sans Seriff=sans-serif; Tahoma=tahoma,arial,helvetica; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",

            link_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_class_list: [{
                    title: 'None',
                    value: ''
                },
                {
                    title: 'Some class',
                    value: 'class-name'
                }
            ],
            importcss_append: true,
            height: 400,
            file_picker_callback: function(callback, value, meta) {
                /* Provide file and text for the link dialog */
                if (meta.filetype === 'file') {
                    callback('https://www.google.com/logos/google.jpg', {
                        text: 'My text'
                    });
                }

                /* Provide image and alt text for the image dialog */
                if (meta.filetype === 'image') {
                    callback('https://www.google.com/logos/google.jpg', {
                        alt: 'My alt text'
                    });
                }

                /* Provide alternative source and posted for the media dialog */
                if (meta.filetype === 'media') {
                    callback('movie.mp4', {
                        source2: 'alt.ogg',
                        poster: 'https://www.google.com/logos/google.jpg'
                    });
                }
            },
            templates: [{
                    title: 'New Table',
                    description: 'creates a new table',
                    content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
                },
                {
                    title: 'Starting my story',
                    description: 'A cure for writers block',
                    content: 'Once upon a time...'
                },
                {
                    title: 'New list with dates',
                    description: 'New List with dates',
                    content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
                }
            ],
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_noneditable_class: "mceNonEditable",
            toolbar_mode: 'sliding',
            contextmenu: "link image imagetools table",
            images_upload_handler: function(blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', "{{ route('content.ajaxtiny') }}");
                xhr.onload = function() {
                    var json;

                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            }
        });
        //--Benefits--


tinymce.init({
            selector: 'textarea#benefits',
            plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
            imagetools_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_sticky: true,
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_prefix: "{path}{query}-{id}-",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            font_formats:"Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago;Poppins=poppins Symbol=symbol;Sans Seriff=sans-serif; Tahoma=tahoma,arial,helvetica; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",

            content_css: '//www.tiny.cloud/css/codepen.min.css',
            link_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_class_list: [{
                    title: 'None',
                    value: ''
                },
                {
                    title: 'Some class',
                    value: 'class-name'
                }
            ],
            importcss_append: true,
            height: 400,
            file_picker_callback: function(callback, value, meta) {
                /* Provide file and text for the link dialog */
                if (meta.filetype === 'file') {
                    callback('https://www.google.com/logos/google.jpg', {
                        text: 'My text'
                    });
                }

                /* Provide image and alt text for the image dialog */
                if (meta.filetype === 'image') {
                    callback('https://www.google.com/logos/google.jpg', {
                        alt: 'My alt text'
                    });
                }

                /* Provide alternative source and posted for the media dialog */
                if (meta.filetype === 'media') {
                    callback('movie.mp4', {
                        source2: 'alt.ogg',
                        poster: 'https://www.google.com/logos/google.jpg'
                    });
                }
            },
            templates: [{
                    title: 'New Table',
                    description: 'creates a new table',
                    content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
                },
                {
                    title: 'Starting my story',
                    description: 'A cure for writers block',
                    content: 'Once upon a time...'
                },
                {
                    title: 'New list with dates',
                    description: 'New List with dates',
                    content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
                }
            ],
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_noneditable_class: "mceNonEditable",
            toolbar_mode: 'sliding',
            contextmenu: "link image imagetools table",
            images_upload_handler: function(blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', "{{ route('content.ajaxtiny') }}");
                xhr.onload = function() {
                    var json;

                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            }
        });
        //--Side effects--

tinymce.init({
            selector: 'textarea#side_effects',
            plugins: 'print preview paste importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
            imagetools_cors_hosts: ['picsum.photos'],
            menubar: 'file edit view insert format tools table help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media template link anchor codesample | ltr rtl',
            toolbar_sticky: true,
            autosave_ask_before_unload: true,
            autosave_interval: "30s",
            autosave_prefix: "{path}{query}-{id}-",
            autosave_restore_when_empty: false,
            autosave_retention: "2m",
            image_advtab: true,
            font_formats:"Andale Mono=andale mono,times; Arial=arial,helvetica,sans-serif; Arial Black=arial black,avant garde; Book Antiqua=book antiqua,palatino; Comic Sans MS=comic sans ms,sans-serif; Courier New=courier new,courier; Georgia=georgia,palatino; Helvetica=helvetica; Impact=impact,chicago;Poppins=poppins Symbol=symbol;Sans Seriff=sans-serif; Tahoma=tahoma,arial,helvetica; Terminal=terminal,monaco; Times New Roman=times new roman,times; Trebuchet MS=trebuchet ms,geneva; Verdana=verdana,geneva; Webdings=webdings; Wingdings=wingdings,zapf dingbats",

            content_css: '//www.tiny.cloud/css/codepen.min.css',
            link_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_list: [{
                    title: 'My page 1',
                    value: 'http://www.tinymce.com'
                },
                {
                    title: 'My page 2',
                    value: 'http://www.moxiecode.com'
                }
            ],
            image_class_list: [{
                    title: 'None',
                    value: ''
                },
                {
                    title: 'Some class',
                    value: 'class-name'
                }
            ],
            importcss_append: true,
            height: 400,
            file_picker_callback: function(callback, value, meta) {
                /* Provide file and text for the link dialog */
                if (meta.filetype === 'file') {
                    callback('https://www.google.com/logos/google.jpg', {
                        text: 'My text'
                    });
                }

                /* Provide image and alt text for the image dialog */
                if (meta.filetype === 'image') {
                    callback('https://www.google.com/logos/google.jpg', {
                        alt: 'My alt text'
                    });
                }

                /* Provide alternative source and posted for the media dialog */
                if (meta.filetype === 'media') {
                    callback('movie.mp4', {
                        source2: 'alt.ogg',
                        poster: 'https://www.google.com/logos/google.jpg'
                    });
                }
            },
            templates: [{
                    title: 'New Table',
                    description: 'creates a new table',
                    content: '<div class="mceTmpl"><table width="98%%"  border="0" cellspacing="0" cellpadding="0"><tr><th scope="col"> </th><th scope="col"> </th></tr><tr><td> </td><td> </td></tr></table></div>'
                },
                {
                    title: 'Starting my story',
                    description: 'A cure for writers block',
                    content: 'Once upon a time...'
                },
                {
                    title: 'New list with dates',
                    description: 'New List with dates',
                    content: '<div class="mceTmpl"><span class="cdate">cdate</span><br /><span class="mdate">mdate</span><h2>My List</h2><ul><li></li><li></li></ul></div>'
                }
            ],
            template_cdate_format: '[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]',
            template_mdate_format: '[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]',
            height: 600,
            image_caption: true,
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            noneditable_noneditable_class: "mceNonEditable",
            toolbar_mode: 'sliding',
            contextmenu: "link image imagetools table",
            images_upload_handler: function(blobInfo, success, failure) {
                var xhr, formData;
                xhr = new XMLHttpRequest();
                xhr.withCredentials = false;
                xhr.open('POST', "{{ route('content.ajaxtiny') }}");
                xhr.onload = function() {
                    var json;

                    if (xhr.status != 200) {
                        failure('HTTP Error: ' + xhr.status);
                        return;
                    }
                    json = JSON.parse(xhr.responseText);

                    if (!json || typeof json.location != 'string') {
                        failure('Invalid JSON: ' + xhr.responseText);
                        return;
                    }
                    success(json.location);
                };
                formData = new FormData();
                formData.append('_token', "{{ csrf_token() }}");
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                xhr.send(formData);
            }
        });
        //category on select
        $(document).on("click", ".category_items", function() {
            var category = $(this).attr("data_item");
            $('.active').removeClass("active");
            $(this).addClass("active");
            $("#selected_category").val(category);
        });

        //---------------------------------------------------Medicine Use Selection ajax--
        $(document).on('keyup', '#medicine_use', function() {
            var query = $(this).val().trim();
            $('#collapse_medicine_use').html('');
            if (query.length >= 2) {
                if (query != '') {
                    $.ajax({
                        url: "{{ route('products.search_medicine_use') }}",
                        type: "GET",
                        data: {
                            'keyword': query
                        },
                        beforeSend: function() {
                            $('#collapse_medicine_use').html('');
                        },
                        success: function(data) {
                            $('#collapse_medicine_use').addClass('show');
                            $('#collapse_medicine_use').html(data);
                        }
                    });
                }
            }
        });

        function select_medicine_use(name) {
            $('#medicine_use').val(name);
        }

        //---------------------------------------------------Brand Selection ajax--
        $(document).on('keyup', '#brand', function() {
            var query = $(this).val().trim();
            $('#collapseExample').html('');
            $('#selected_brand').val('');
            if (query != '') {
                $.ajax({
                    url: "{{ route('products.search_brand') }}",
                    type: "GET",
                    data: {
                        'brand': query
                    },
                    beforeSend: function() {
                        $('#collapseSupplier').html('');
                    },
                    success: function(data) {
                        $('#collapseExample').css("display", "block");
                        $('#collapseExample').html(data);
                    }
                });
            }
        });

        //brand Selection from_li
        function select_brand(id, name) {
            var selected_brand_id = id;
            $("#selected_brand").val(selected_brand_id);
            $('#brand').val(name);
            $('#collapseExample').css("display", "none");
        }

        //new brand enable
        $(document).on("click", "#newbrand", function() {
            $("#new_brand_outer").show();
            $("#selected_brand").val('');
        });

        //---------------------------------------------------Supplier Selection ajax--
        $(document).on('keyup', '#product_supplier', function() {
            var query = $(this).val().trim();
            $("#new_productsupplier_outer").hide();
            $('#productsupplier_drop').html('');
            if (query != '') {
                $.ajax({
                    url: "{{ route('products.search_supplier') }}",
                    type: "GET",
                    data: {
                        'keyword': query
                    },
                    beforeSend: function() {
                        $('#productsupplier_drop').html('');
                    },
                    success: function(data) {
                        $('#productsupplier_drop').html(data);
                    }
                });
            }
        });

        function select_productSupplier(id, name) {
            var allow = true;
            $('.suppliers_added').each(function() {
                if (name == $(this).text()) {
                    allow = false;
                }
            });
            if (allow) {
                var html_content = '<div class="suppliers_added">' + name + '<input type="hidden" name="product_suppliername[]" value="' + name + '" /><input type="hidden" name="supplier[]" value="' + id + '" /><a href="javascript:void(0)" class="remove_productsupplier"><i class="fas fa-times-circle"></i></a></div>';
                $("#product_supplier_selected").append(html_content);
                // $('#product_supplier').val('');
            } else {
                alert('This supplier already selected. Please selected other.');
            }
        }



        $(document).on("click", ".remove_productsupplier", function() {
            if (confirm('Do you want to remove this supplier?')) {
                $(this).parents('.suppliers_added').remove();
            }
        });

        $(document).on("click", "#new_productsupplier", function() {
            $("#new_productsupplier_outer").show();
            $("#text_productsupplier").val('');
        });

        //---------------------------------------------------Add New Supplier coDe--
        $(document).on('click', '#btn_addnewSupplier', function() {
            var supplier = $('#text_productsupplier').val().trim();

            if (supplier != '') {
                $.ajax({
                    url: "{{ route('products.add.newsupplier') }}",
                    type: "POST",
                    data: {
                        'name': supplier,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.result == 'success') {
                            var html = '<div class="suppliers_added">' + response.name + '<input type="hidden" name="product_suppliername[]" value="' + response.name + '" /><input type="hidden" name="supplier[]" value="' + response.ProductSupplierID + '" /><a href="javascript:void(0)" class="remove_productsupplier"><i class="fas fa-times-circle"></i></a></div>';
                            $("#product_supplier_selected").append(html);
                            $('#text_productsupplier').val('');
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        });

        //---------------------------------------------------Product content coDe--
        $(document).on('keyup', '#product_content', function() {
            var query = $(this).val().trim();
            $("#new_productcontent_outer").hide();
            $('#productcontents').html('');
            if (query != '') {
                $.ajax({
                    url: "{{ route('products.search_content') }}",
                    type: "GET",
                    data: {
                        'keyword': query
                    },
                    beforeSend: function() {
                        $('#productcontents').html('');
                    },
                    success: function(data) {
                        $('#productcontents').html(data);
                    }
                });
            }
        });

        function select_productcontent(id, name) {
            var allow = true;
            $('.contents_added').each(function() {
                if (name == $(this).text()) {
                    allow = false;
                }
            });

            if (allow) {
                var html_content = '<div class="contents_added">' + name + '<input type="hidden" name="product_contentname[]" value="' + name + '" /><input type="hidden" name="product_contents[]" value="' + id + '" /><a href="javascript:void(0)" class="remove_productcontents"><i class="fas fa-times-circle"></i></a></div>';
                $("#product_content_selected").append(html_content);
            } else {
                alert('This product content exist. Please choose other.');
            }
        }

        $(document).on("click", ".remove_productcontents", function() {
            if (confirm('Do you want to remove this product content?')) {
                $(this).parents('.contents_added').remove();
            }
        });

        $(document).on("click", "#new_productcontent", function() {
            $("#new_productcontent_outer").show();
            $("#text_productcontent").val('');
        });

        //---------------------------------------------------Add New Product content coDe--
        $(document).on('click', '#add_productcontent', function() {
            var productcontent = $('#text_productcontent').val();

            if (productcontent != '') {
                $.ajax({
                    url: "{{ route('products.add.newproductcontent') }}",
                    type: "POST",
                    data: {
                        'name': productcontent,
                        '_token': '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.result == 'success') {
                            var html_content = '<div class="contents_added">' + response.name + '<input type="hidden" name="product_contents[]" value="' + response.ProductcontentID + '" /><a href="javascript:void(0)" class="remove_productcontents"><i class="fas fa-times-circle"></i></a></div>';
                            $("#product_content_selected").append(html_content);
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        });

        //---------------------------------------------------Set Image Thumbnail--
        $(document).on("click", ".make_thumnail", function() {
            $(".hid_thumnail").val('');
            $(this).parents().children('.hid_thumnail').val('yes');
        });

        // thumbnail field extend
        $(document).ready(function() {
            $(".btn-success").click(function() {
                var html = $(".clone").html();
                $(".fields_extent").append(html);
            });
            $("body").on("click", ".btn-danger", function() {
                $(this).parents(".control-group").remove();
            });
        });

        //--Variant Products coDe--
        $(document).on('keyup', '#variant', function() {
            var query = $(this).val().trim();
            $('#product_variant').html('');
            if (query != '' && query.length > 1) {
                $.ajax({
                    url: "{{ route('products.search') }}",
                    type: "GET",
                    data: {
                        'variant': query
                    },
                    success: function(data) {
                        $('#product_variant').html(data);
                    }
                });
            }
        });

        function select_variants(id, name) {
            var allow = true;
            $('.variants_added').each(function() {
                if (name == $(this).text()) {
                    allow = false;
                }
            });

            if (allow) {
                var html_content = '<div class="variants_added">' + name + '<input type="hidden" name="variants[]" value="' + id + '" /><a href="javascript:void(0)" class="remove_variant"><i class="fas fa-times-circle"></i></a></div>';
                $("#product_variants_selected").append(html_content);
            } else {
                alert('This product variant exist. Please choose other.');
            }

        }

        $(document).ready(function() {

            var parent_cat_id = {{ !empty(old('producttype')) && old('producttype') != '' ? old('producttype') : 0 }};
            var selected_cat_id = {{ !empty(old('selected_category')) && old('selected_category') != '' ? old('selected_category') : 0 }};
            $.ajax({
                url: "{{ route('product.subcategory') }}",
                type: "POST",
                data: {
                    parent_cat_id: parent_cat_id,
                    selected_cat_id:selected_cat_id,
                    "_token": "{{ csrf_token() }}"
                },
                dataType: "json",
                success: function(data) {
                    if (data) {
                        $('#category').html(data.html);
                    } else {

                        $('#category').empty();

                    }
                }
            });
            $('#producttype').on('change', function() {
                var parent_cat_id = $(this).val();
                var cat_name = $(this).find('option:selected').text();
                if(cat_name=="All Medicines"){
                    $('#productbrand').hide();
                    $('#brand').val('');
                }else{
                    $('#productbrand').show();
                }

                $.ajax({
                    url: "{{ route('product.subcategory') }}",
                    type: "POST",
                    data: {
                        parent_cat_id: parent_cat_id,
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType: "json",

                    success: function(data) {

                        if (data) {

                            $('#category').html(data.html);
                        } else {
                            $('#category').empty();
                        }
                    }
                });

            });
        });


        $(document).ready(function() {
            $("#select_medical_uses").click(function() {

                html_content = '';
                html_content2 = '';

                var values = $("input[name='medical_use']:checked");
                var med_for = $('input[name="medicine_for"]:checked').val();

                if ((values != '') && (typeof med_for == "undefined")) {
                    swal('Please choose medicine for option');exit;

                }
                if ((!$("input[name='medical_use']").is(":checked")) && (typeof med_for != "undefined")) {

                    swal('Please choose medicine uses');exit;
                }
                $('#modalMedicaluse').modal('hide');
                html_content2+='<div class="medicinefor_added">'+'<input type="hidden" name="medicine_for" value="'+med_for+'" /><a href="javascript:void(0)" class="remove_medicine_for"><i class="fas fa-times-circle"></i></a></div>';


                $.each($("input[name='medical_use']:checked"), function() {

                    html_content += '<div class="medicaluse_added">' + med_for +' ' + $(this).attr('data-name') + '<input type="hidden" name="medicine_uses_name[]" value="' + $(this).attr('data-name') + '" /><input type="hidden" name="medicine_uses_for[]" value="'+med_for+'" /><input type="hidden" name="medicine_uses[]" value="'+med_for+'_'+ $(this).val()+'" /><a href="javascript:void(0)" class="remove_medical_use"><i class="fas fa-times-circle"></i></a></div>';


                });
                $("#medical_uses_selected").append(html_content);
                $("#medicine_use_for").html(html_content2);
                $('input[type=checkbox]').prop('checked', false);


            });
        });

        $(document).on("click", ".remove_variant", function() {
            if (confirm('Do you want to remove this product variant?')) {
                $(this).parents('.variants_added').remove();
            }
        });

        $(document).on("click", ".remove_medical_use", function() {
            if (confirm('Do you want to remove this product Medical use?')) {
                $(this).parents('.medicaluse_added').remove();
            }
        });
        //--Variant Products coDe/--
    </script>

    <style>
        .active {
            color: #FF0000;
        }

    </style>
@endsection
