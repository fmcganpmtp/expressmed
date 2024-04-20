<?php
use Illuminate\Support\Facades\Route;

// Auth::routes();

//------------------Admin/Login--
Route::get('/login/admin', [App\Http\Controllers\Auth\LoginController::class, 'showAdminLoginForm'])->name('login.admin');
Route::post('/login/admin', [App\Http\Controllers\Auth\LoginController::class, 'adminLogin']);
Route::get('/admin/logout', [App\Http\Controllers\Auth\LoginController::class, 'admin_logout'])->name('admin.logout');

//------------------Admin/Administrators--
Route::get('/admin', [App\Http\Controllers\Admin\AdminController::class, 'Index'])->name('admin.Index');

//------------------Admin/Profile--
Route::get('/admin/profile', [App\Http\Controllers\Admin\AdminController::class, 'profile'])->name('admin.profile');
Route::post('/admin/profile', [App\Http\Controllers\Admin\AdminController::class, 'updateprofile'])->name('admin.updateprofile');
Route::post('/admin/change-password', [App\Http\Controllers\Admin\AdminController::class, 'changePassword'])->name('admin.changePassword');
Route::get('/access_restricted', [App\Http\Controllers\Admin\AdminController::class, 'access_restricted'])->name('admin.access_restricted');

//------------------Admin/Administrators--
Route::get('/admin/list', [App\Http\Controllers\Admin\AdminController::class, 'listAdmin'])->name('admin.list')->middleware('AclCheck:admin_list');
Route::get('/admin/create', [App\Http\Controllers\Admin\AdminController::class, 'create'])->name('admin.create')->middleware('AclCheck:create_admins');
Route::post('/admin/create', [App\Http\Controllers\Admin\AdminController::class, 'store'])->name('admin.create.store')->middleware('AclCheck:create_admins');
Route::get('/admin/view/{id}', [App\Http\Controllers\Admin\AdminController::class, 'show'])->name('admin.show')->middleware('AclCheck:create_admins');
Route::get('/admin/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'edit'])->name('admin.edit')->middleware('AclCheck:edit_admins');
Route::post('/admin/edit/{id}', [App\Http\Controllers\Admin\AdminController::class, 'update'])->name('admin.edit')->middleware('AclCheck:edit_admins');
Route::post('/admin/delete/{id}', [App\Http\Controllers\Admin\AdminController::class, 'destroy'])->name('admin.delete')->middleware('AclCheck:delete_admins');

//------------------Admin/Administrators/Roles--
Route::get('/admin/roles', [App\Http\Controllers\Admin\RoleController::class, 'index'])->name('admin.roles')->middleware('AclCheck:role_list');
Route::get('/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'create'])->name('roles.create')->middleware('AclCheck:role_create');
Route::post('/roles/create', [App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.create')->middleware('AclCheck:role_create');
Route::get('/roles/edit/{id}', [App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit')->middleware('AclCheck:role_edit');
Route::post('/roles/edit/{id}', [App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.edit')->middleware('AclCheck:role_edit');
Route::post('/roles/delete/{id}', [App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.delete')->middleware('AclCheck:role_delete');

//------------------Admin/Administrators/Permissions--
Route::get('/admin/permissions', [App\Http\Controllers\Admin\PermissionsController::class, 'index'])->name('admin.permissions')->middleware('AclCheck:permission_list');
Route::get('/permissions/create', [App\Http\Controllers\Admin\PermissionsController::class, 'create'])->name('permissions.create')->middleware('AclCheck:permission_create');
Route::post('/permissions/create', [App\Http\Controllers\Admin\PermissionsController::class, 'store'])->name('permissions.create')->middleware('AclCheck:permission_create');
Route::get('/permissions/edit/{id}', [App\Http\Controllers\Admin\PermissionsController::class, 'edit'])->name('permissions.edit')->middleware('AclCheck:permission_edit');
Route::post('/permissions/edit/{id}', [App\Http\Controllers\Admin\PermissionsController::class, 'update'])->name('permissions.edit')->middleware('AclCheck:permission_edit');
Route::post('/permissions/delete/{id}', [App\Http\Controllers\Admin\PermissionsController::class, 'destroy'])->name('permissions.delete')->middleware('AclCheck:permission_delete');

//------------------Admin/Testimonials--
Route::get('admin/testimonials', [App\Http\Controllers\Admin\TestimonialsController::class, 'index'])->name('admin.testimonials')->middleware('AclCheck:testimonials_list');
Route::get('/testimonials/create', [App\Http\Controllers\Admin\TestimonialsController::class, 'create'])->name('testimonials.create')->middleware('AclCheck:testimonials_create');
Route::post('/testimonials/create', [App\Http\Controllers\Admin\TestimonialsController::class, 'store'])->name('testimonials.create')->middleware('AclCheck:testimonials_create');
Route::get('/testimonials/edit/{id}', [App\Http\Controllers\Admin\TestimonialsController::class, 'edit'])->name('testimonials.edit')->middleware('AclCheck:testimonials_edit');
Route::post('/testimonials/edit/{id}', [App\Http\Controllers\Admin\TestimonialsController::class, 'update'])->name('testimonials.edit')->middleware('AclCheck:testimonials_edit');
Route::post('/testimonials/delete/{id}', [App\Http\Controllers\Admin\TestimonialsController::class, 'destroy'])->name('testimonials.destroy')->middleware('AclCheck:testimonials_delete');
Route::post('/testimonials/removeimage', [App\Http\Controllers\Admin\TestimonialsController::class, 'remove_testimonialimage'])->name('testimonials.removeImage')->middleware('AclCheck:testimonials_edit');

//------------------Admin/Careers--
Route::get('admin/careers', [App\Http\Controllers\Admin\CareersController::class, 'index'])->name('admin.careers')->middleware('AclCheck:career_list');
Route::get('/careers/create', [App\Http\Controllers\Admin\CareersController::class, 'create'])->name('careers.create')->middleware('AclCheck:career_create');
Route::post('/careers/create', [App\Http\Controllers\Admin\CareersController::class, 'store'])->name('careers.create')->middleware('AclCheck:career_create');
Route::get('/careers/view/{id}', [App\Http\Controllers\Admin\CareersController::class, 'show'])->name('careers.show')->middleware('AclCheck:career_details');
Route::get('/careers/edit/{id}', [App\Http\Controllers\Admin\CareersController::class, 'edit'])->name('careers.edit')->middleware('AclCheck:career_edit');
Route::post('/careers/edit/{id}', [App\Http\Controllers\Admin\CareersController::class, 'update'])->name('careers.edit')->middleware('AclCheck:career_edit');
Route::post('/careers/delete/{id}', [App\Http\Controllers\Admin\CareersController::class, 'destroy'])->name('careers.destroy')->middleware('AclCheck:career_delete');
Route::post('/careers/status', [App\Http\Controllers\Admin\CareersController::class, 'update_status'])->name('careers.status')->middleware('AclCheck:career_edit');
Route::post('/careers/message/applicant/{id}', [App\Http\Controllers\Admin\CareersController::class, 'send_message'])->name('careers.send_message')->middleware('AclCheck:career_details');

//------------------Admin/NewsAndEvents--
Route::get('admin/news', [App\Http\Controllers\Admin\NewsController::class, 'index'])->name('admin.news')->middleware('AclCheck:news_list');
Route::get('/news/create', [App\Http\Controllers\Admin\NewsController::class, 'create'])->name('news.create')->middleware('AclCheck:news_create');
Route::post('/news/create', [App\Http\Controllers\Admin\NewsController::class, 'store'])->name('news.create')->middleware('AclCheck:news_create');
Route::get('/news/edit/{id}', [App\Http\Controllers\Admin\NewsController::class, 'edit'])->name('news.edit')->middleware('AclCheck:news_edit');
Route::post('/news/edit/{id}', [App\Http\Controllers\Admin\NewsController::class, 'update'])->name('news.edit')->middleware('AclCheck:news_edit');
Route::post('/news/delete/{id}', [App\Http\Controllers\Admin\NewsController::class, 'destroy'])->name('news.destroy')->middleware('AclCheck:news_delete');
Route::get('/news/view/{id}', [App\Http\Controllers\Admin\NewsController::class, 'show'])->name('news.show')->middleware('AclCheck:news_view');
Route::post('/news/removeMedia', [App\Http\Controllers\Admin\NewsController::class, 'removeMedia'])->name('news.removeMedia')->middleware('AclCheck:news_edit');
Route::post('/content/ajaxtiny', [App\Http\Controllers\Admin\NewsController::class, 'ajaxtiny'])->name('content.ajaxtiny');

//------------------Admin/Customers--
Route::get('admin/customers', [App\Http\Controllers\Admin\CustomerController::class, 'index'])->name('admin.customers')->middleware('AclCheck:customer_list');
Route::get('/customers/vieworders/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'listorders'])->name('customers.orders')->middleware('AclCheck:customer_orders');
Route::get('/customers/view/{id}', [App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.view')->middleware('AclCheck:customer_view');
Route::post('/customers/changeStatus', [App\Http\Controllers\Admin\CustomerController::class, 'changeStatus'])->name('customers.changeStatus')->middleware('AclCheck:customer_manage');

//------------------Admin/Prescription--
Route::get('admin/prescription', [App\Http\Controllers\Admin\PrescriptionController::class, 'index'])->name('admin.prescription')->middleware('AclCheck:prescription_list');
Route::get('prescription/delete', [App\Http\Controllers\Admin\PrescriptionController::class, 'destroy'])->name('prescription.destroy')->middleware('AclCheck:delete_prescription');
Route::post('prescription/manage', [App\Http\Controllers\Admin\PrescriptionController::class, 'manage_prescription'])->name('manage.prescription')->middleware('AclCheck:prescription_manage');
Route::get('admin/general-prescription', [App\Http\Controllers\Admin\GeneralprescriptionController::class, 'index'])->name('admin.generalprescription')->middleware('AclCheck:generalprescription_list');
Route::post('generalprescription/manage', [App\Http\Controllers\Admin\GeneralprescriptionController::class, 'manage_generalprescription'])->name('manage.generalprescription');
Route::get('generalprescription/delete', [App\Http\Controllers\Admin\PrescriptionController::class, 'destroy'])->name('generalprescription.destroy')->middleware('AclCheck:delete_generalprescription');

//------------------Admin/Products--
Route::get('/admin/products', [App\Http\Controllers\Admin\ProductsController::class, 'index'])->name('admin.products')->middleware('AclCheck:list_products');
Route::get('/products/create', [App\Http\Controllers\Admin\ProductsController::class, 'create'])->name('products.create')->middleware('AclCheck:create_products');
Route::post('/products/create', [App\Http\Controllers\Admin\ProductsController::class, 'store'])->name('products.create')->middleware('AclCheck:create_products');
Route::get('/products/view/{id}', [App\Http\Controllers\Admin\ProductsController::class, 'show'])->name('products.view')->middleware('AclCheck:view_products');
Route::get('/products/edit/{id}', [App\Http\Controllers\Admin\ProductsController::class, 'edit'])->name('products.edit')->middleware('AclCheck:edit_products');
Route::get('/products/duplicate/{id}', [App\Http\Controllers\Admin\ProductsController::class, 'create_duplicate'])->name('products.duplicate')->middleware('AclCheck:product_duplicate');
Route::post('/products/edit/{id}', [App\Http\Controllers\Admin\ProductsController::class, 'update'])->name('products.update')->middleware('AclCheck:edit_products');
Route::post('/products/delete/{id}', [App\Http\Controllers\Admin\ProductsController::class, 'destroy'])->name('products.destroy')->middleware('AclCheck:product_delete');
Route::post('/products/import_bulk', [App\Http\Controllers\Admin\ProductsController::class, 'import_bulk'])->name('products.import.bulk')->middleware('AclCheck:product_import');
Route::get('/products/removed', [App\Http\Controllers\Admin\ProductsController::class, 'removed_products'])->name('products.removed')->middleware('AclCheck:delete_product_list');

Route::get('/products/search_content', [App\Http\Controllers\Admin\ProductsController::class, 'search_productcontent'])->name('products.search_content');
Route::post('/products/add/new_productcontent', [App\Http\Controllers\Admin\ProductsController::class, 'add_newproductcontent'])->name('products.add.newproductcontent');
Route::get('/products/search_brand', [App\Http\Controllers\Admin\ProductsController::class, 'search_brand'])->name('products.search_brand');
Route::get('/products/search_supplier', [App\Http\Controllers\Admin\ProductsController::class, 'search_supplier'])->name('products.search_supplier');
Route::post('/products/add/new_supplier', [App\Http\Controllers\Admin\ProductsController::class, 'add_newsupplier'])->name('products.add.newsupplier');
Route::get('/products/medicine_use', [App\Http\Controllers\Admin\ProductsController::class, 'search_medicine_use'])->name('products.search_medicine_use');
Route::get('/products/search', [App\Http\Controllers\Admin\ProductsController::class, 'search'])->name('products.search');
Route::post('/products/subcategories', [App\Http\Controllers\Admin\ProductsController::class, 'product_subcategory'])->name('product.subcategory');
Route::post('/products/update/subcategory', [App\Http\Controllers\Admin\ProductsController::class, 'product_editsubcategory'])->name('product.edit_subcategory');
Route::post('/products/find_subcategory', [App\Http\Controllers\Admin\ProductsController::class, 'find_subcategories'])->name('find.subcategories');
Route::post('/products/update/sellstatus', [App\Http\Controllers\Admin\ProductsController::class, 'update_sellstatus'])->name('product.update.sellstatus');
Route::post('/products/update/hideoption', [App\Http\Controllers\Admin\ProductsController::class, 'update_hideoption'])->name('product.update.hideoption');
Route::post('/products/approve', [App\Http\Controllers\Admin\ProductsController::class, 'product_approval'])->name('approve.product');




Route::post('/products/removeMedia', [App\Http\Controllers\Admin\ProductsController::class, 'removeMedia'])->name('products.removeMedia');
Route::post('/products/setThumbnail', [App\Http\Controllers\Admin\ProductsController::class, 'setThumbnail'])->name('products.setThumbnail');
Route::post('/products/review/update', [App\Http\Controllers\Admin\ProductsController::class, 'update_productreview'])->name('product.review.update');
Route::post('/products/review/delete', [App\Http\Controllers\Admin\ProductsController::class, 'destroy_productreview'])->name('product.review.delete');

//------------------Admin/Products/Brands--
Route::get('/admin/brands', [App\Http\Controllers\Admin\ProductBrandController::class, 'index'])->name('admin.brands')->middleware('AclCheck:brand_list');
Route::get('/brands/create', [App\Http\Controllers\Admin\ProductBrandController::class, 'create'])->name('brands.create')->middleware('AclCheck:brand_create');
Route::post('/brands/create', [App\Http\Controllers\Admin\ProductBrandController::class, 'store'])->name('brands.create')->middleware('AclCheck:brand_create');
Route::get('/brands/edit/{id}', [App\Http\Controllers\Admin\ProductBrandController::class, 'edit'])->name('brands.edit')->middleware('AclCheck:brand_edit');
Route::post('/brands/edit/{id}', [App\Http\Controllers\Admin\ProductBrandController::class, 'update'])->name('brands.update')->middleware('AclCheck:brand_edit');
Route::post('/brands/delete/{id}', [App\Http\Controllers\Admin\ProductBrandController::class, 'destroy'])->name('brands.destroy')->middleware('AclCheck:brand_delete');
Route::post('/brands/removeimage', [App\Http\Controllers\Admin\ProductBrandController::class, 'remove_brandimage'])->name('brands.removeImage')->middleware('AclCheck:brand_edit');

//------------------Admin/Products/categories--
Route::get('/admin/categories', [App\Http\Controllers\Admin\CategoriesController::class, 'index'])->name('admin.categories')->middleware('AclCheck:category_list');
Route::get('/categories/create', [App\Http\Controllers\Admin\CategoriesController::class, 'create'])->name('categories.create')->middleware('AclCheck:category_create');
Route::post('/categories/create', [App\Http\Controllers\Admin\CategoriesController::class, 'store'])->name('categories.create')->middleware('AclCheck:category_create');
Route::get('/categories/edit/{id}', [App\Http\Controllers\Admin\CategoriesController::class, 'edit'])->name('categories.edit')->middleware('AclCheck:category_edit');
Route::post('/categories/edit/{id}', [App\Http\Controllers\Admin\CategoriesController::class, 'update'])->name('categories.update')->middleware('AclCheck:category_edit');
Route::post('/categories/delete/{id}', [App\Http\Controllers\Admin\CategoriesController::class, 'destroy'])->name('categories.destroy')->middleware('AclCheck:category_delete');
Route::post('/categories/removeimage', [App\Http\Controllers\Admin\CategoriesController::class, 'remove_image'])->name('categories.removeImage')->middleware('AclCheck:category_edit');
Route::post('/categoriesoffer/update/', [App\Http\Controllers\Admin\CategoriesController::class, 'categoriesoffer_update'])->name('categoriesoffer.update');


//------------------Admin/Products/Tax--
Route::get('/admin/taxes', [App\Http\Controllers\Admin\TaxController::class, 'index'])->name('admin.taxes')->middleware('AclCheck:tax_list');
Route::post('/taxes/create', [App\Http\Controllers\Admin\TaxController::class, 'store'])->name('taxes.create')->middleware('AclCheck:tax_create');
Route::post('/taxes/update', [App\Http\Controllers\Admin\TaxController::class, 'update'])->name('taxes.update')->middleware('AclCheck:tax_edit');
Route::post('/taxes/changestatus', [App\Http\Controllers\Admin\TaxController::class, 'changestatus'])->name('taxes.changestatus')->middleware('AclCheck:tax_edit');

//------------------Admin/Products/Type--
Route::get('/admin/producttype', [App\Http\Controllers\Admin\ProductTypeController::class, 'index'])->name('admin.producttype')->middleware('AclCheck:product_type_list');
Route::post('/producttype/create', [App\Http\Controllers\Admin\ProductTypeController::class, 'store'])->name('producttype.create')->middleware('AclCheck:product_type_create');
Route::post('/producttype/update', [App\Http\Controllers\Admin\ProductTypeController::class, 'update'])->name('producttype.update')->middleware('AclCheck:product_type_edit');
Route::post('/producttype/delete/{id}', [App\Http\Controllers\Admin\ProductTypeController::class, 'destroy'])->name('producttype.destroy')->middleware('AclCheck:product_type_delete');

//------------------Admin/Products/Content--
Route::get('/admin/productcontent', [App\Http\Controllers\Admin\ProductContentController::class, 'index'])->name('admin.productcontent')->middleware('AclCheck:product_content_list');
Route::get('/productcontent/create', [App\Http\Controllers\Admin\ProductContentController::class, 'create'])->name('productcontent.create')->middleware('AclCheck:product_content_create');
Route::post('/productcontent/create', [App\Http\Controllers\Admin\ProductContentController::class, 'store'])->name('productcontent.store')->middleware('AclCheck:product_content_create');
Route::get('/productcontent/edit/{id}', [App\Http\Controllers\Admin\ProductContentController::class, 'edit'])->name('productcontent.edit')->middleware('AclCheck:product_content_edit');
Route::post('/productcontent/edit/{id}', [App\Http\Controllers\Admin\ProductContentController::class, 'update'])->name('productcontent.update')->middleware('AclCheck:product_content_edit');
Route::post('/productcontent/delete/{id}', [App\Http\Controllers\Admin\ProductContentController::class, 'destroy'])->name('productcontent.destroy')->middleware('AclCheck:product_content_delete');

//------------------Admin/Products/Supplier--
Route::get('/admin/supplier', [App\Http\Controllers\Admin\ProductSupplierController::class, 'index'])->name('admin.supplier')->middleware('AclCheck:supplier_list');
Route::get('/supplier/create', [App\Http\Controllers\Admin\ProductSupplierController::class, 'create'])->name('supplier.create')->middleware('AclCheck:supplier_create');
Route::post('/supplier/create', [App\Http\Controllers\Admin\ProductSupplierController::class, 'store'])->name('supplier.store')->middleware('AclCheck:supplier_create');
Route::get('/supplier/edit/{id}', [App\Http\Controllers\Admin\ProductSupplierController::class, 'edit'])->name('supplier.edit')->middleware('AclCheck:supplier_edit');
Route::post('/supplier/edit/{id}', [App\Http\Controllers\Admin\ProductSupplierController::class, 'update'])->name('supplier.update')->middleware('AclCheck:supplier_edit');
Route::post('/supplier/delete/{id}', [App\Http\Controllers\Admin\ProductSupplierController::class, 'destroy'])->name('supplier.destroy')->middleware('AclCheck:supplier_delete');

//------------------Admin/Products/manufacturers--
Route::get('/admin/manufacturers', [App\Http\Controllers\Admin\ProductManufacturerController::class, 'index'])->name('admin.manufacturers')->middleware('AclCheck:manufacturer_list');
Route::get('/manufacturers/create', [App\Http\Controllers\Admin\ProductManufacturerController::class, 'create'])->name('manufacturers.create')->middleware('AclCheck:manufacturer_create');
Route::post('/manufacturers/create', [App\Http\Controllers\Admin\ProductManufacturerController::class, 'store'])->name('manufacturers.store')->middleware('AclCheck:manufacturer_create');
Route::get('/manufacturers/edit/{id}', [App\Http\Controllers\Admin\ProductManufacturerController::class, 'edit'])->name('manufacturers.edit')->middleware('AclCheck:manufacturer_edit');
Route::post('/manufacturers/edit/{id}', [App\Http\Controllers\Admin\ProductManufacturerController::class, 'update'])->name('manufacturers.update')->middleware('AclCheck:manufacturer_edit');
Route::post('/manufacturers/delete/{id}', [App\Http\Controllers\Admin\ProductManufacturerController::class, 'destroy'])->name('manufacturers.destroy')->middleware('AclCheck:manufacturer_delete');
Route::post('/manufacturers/removeimage', [App\Http\Controllers\Admin\ProductManufacturerController::class, 'remove_manufacturersimage'])->name('manufacturers.removeImage')->middleware('AclCheck:manufacturer_edit');


//------------------Admin/Products/MedicineUse--
Route::get('/admin/medicineUse', [App\Http\Controllers\Admin\ProductMedicineUseController::class, 'index'])->name('admin.medicineUse')->middleware('AclCheck:medicine_use_list');
Route::get('/medicineUse/create', [App\Http\Controllers\Admin\ProductMedicineUseController::class, 'create'])->name('medicineUse.create')->middleware('AclCheck:medicine_use_create');
Route::post('/medicineUse/create', [App\Http\Controllers\Admin\ProductMedicineUseController::class, 'store'])->name('medicineUse.store')->middleware('AclCheck:medicine_use_create');
Route::get('/medicineUse/edit/{id}', [App\Http\Controllers\Admin\ProductMedicineUseController::class, 'edit'])->name('medicineUse.edit')->middleware('AclCheck:medicine_use_edit');
Route::post('/medicineUse/edit/{id}', [App\Http\Controllers\Admin\ProductMedicineUseController::class, 'update'])->name('medicineUse.update')->middleware('AclCheck:medicine_use_edit');
Route::post('/medicineUse/delete/{id}', [App\Http\Controllers\Admin\ProductMedicineUseController::class, 'destroy'])->name('medicineUse.destroy')->middleware('AclCheck:medicine_use_delete');

//--Admin/Stores--
Route::get('/admin/stores', [App\Http\Controllers\Admin\StoreController::class, 'index'])->name('admin.stores');
Route::get('/stores/create', [App\Http\Controllers\Admin\StoreController::class, 'create'])->name('store.create')->middleware('AclCheck:store_create');
Route::post('/stores/create', [App\Http\Controllers\Admin\StoreController::class, 'store'])->name('store.store')->middleware('AclCheck:store_update');
Route::get('/stores/show/{id}', [App\Http\Controllers\Admin\StoreController::class, 'show'])->name('store.show')->middleware('AclCheck:store_show');
Route::get('/stores/edit/{id}', [App\Http\Controllers\Admin\StoreController::class, 'edit'])->name('store.edit')->middleware('AclCheck:store_edit');
Route::post('/stores/edit/{id}', [App\Http\Controllers\Admin\StoreController::class, 'update'])->name('store.update')->middleware('AclCheck:store_update');
Route::post('/stores/delete/{id}', [App\Http\Controllers\Admin\StoreController::class, 'destroy'])->name('store.destroy')->middleware('AclCheck:store_destroy');


//------------------Admin/Products/Orders--
Route::get('/admin/orders', [App\Http\Controllers\Admin\OrderController::class, 'orderlist'])->name('admin.orders')->middleware('AclCheck:order_list');
Route::get('/admin/ordersdetails/{id}', [App\Http\Controllers\Admin\OrderController::class, 'orderDetails'])->name('admin.order.details')->middleware('AclCheck:order_details');
Route::get('/admin/order/invoice_print/{id}', [App\Http\Controllers\Admin\OrderController::class, 'print_invoiceOrder'])->name('admin.order.print_invoice')->middleware('AclCheck:print_invoice');
Route::post('/admin/order/changestatus/{id}', [App\Http\Controllers\Admin\OrderController::class, 'changeOrderStatus'])->name('admin.order.changestatus')->middleware('AclCheck:order_update');
Route::post('/admin/orderdetails/changeStatus', [App\Http\Controllers\Admin\OrderController::class, 'changeOrderdetailStatus'])->name('admin.orderdetails.changestatus')->middleware('AclCheck:order_update');
// Admin/teams
Route::get('admin/teams',                   [App\Http\Controllers\Admin\TeamsController::class,'index'])->name('teams.index')->middleware('AclCheck:team_list');
Route::get('teams/create',                  [App\Http\Controllers\Admin\TeamsController::class,'create'])->name('teams.create')->middleware('AclCheck:team_create');
Route::post('teams/create',                 [App\Http\Controllers\Admin\TeamsController::class,'store'])->name('teams.create')->middleware('AclCheck:team_create');
Route::get('teams/edit/{id}',               [App\Http\Controllers\Admin\TeamsController::class,'edit'])->name('teams.edit')->middleware('AclCheck:team_edit');
Route::post('teams/edit/{id}',              [App\Http\Controllers\Admin\TeamsController::class,'update'])->name('teams.edit')->middleware('AclCheck:team_edit');
Route::post('teams/delete/{id}',            [App\Http\Controllers\Admin\TeamsController::class,'destroy'])->name('teams.destroy')->middleware('AclCheck:team_delete');
Route::post('teams/change-order',            [App\Http\Controllers\Admin\TeamsController::class,'orderchange'])->name('teams.orderchange')->middleware('AclCheck:team_edit');

//Admin/Doctors
Route::get('admin/doctor',                  [App\Http\Controllers\Admin\DoctorsController::class,'index'])->name('doctor.index');
Route::get('doctor/create',                 [App\Http\Controllers\Admin\DoctorsController::class,'create'])->name('doctor.create');
Route::post('doctor/create',                [App\Http\Controllers\Admin\DoctorsController::class,'store'])->name('doctor.create');
Route::get('doctor/edit/{id}',              [App\Http\Controllers\Admin\DoctorsController::class,'edit'])->name('doctor.edit');
Route::post('doctor/edit/{id}',             [App\Http\Controllers\Admin\DoctorsController::class,'update'])->name('doctor.edit');
Route::post('doctor/delete/{id}',           [App\Http\Controllers\Admin\DoctorsController::class,'destroy'])->name('doctor.delete');
Route::post('doctor/change-order',            [App\Http\Controllers\Admin\DoctorsController::class,'orderchange'])->name('doctor.orderchange') ;
//------------------Admin/PromotionBanners--
Route::get('/admin/promotionbanner', [App\Http\Controllers\Admin\PromotionBannerController::class, 'index'])->name('admin.promotionbanner')->middleware('AclCheck:promotion_banner_list');
Route::get('/promotionbanner/create', [App\Http\Controllers\Admin\PromotionBannerController::class, 'create'])->name('promotionbanner.create')->middleware('AclCheck:promotion_banner_create');
Route::post('/promotionbanner/create', [App\Http\Controllers\Admin\PromotionBannerController::class, 'store'])->name('promotionbanner.create')->middleware('AclCheck:promotion_banner_create');
Route::get('/promotionbanner/view/{id}', [App\Http\Controllers\Admin\PromotionBannerController::class, 'show'])->name('promotionbanner.show')->middleware('AclCheck:promotion_banner_view');
Route::get('/promotionbanner/edit/{id}', [App\Http\Controllers\Admin\PromotionBannerController::class, 'edit'])->name('promotionbanner.edit')->middleware('AclCheck:promotion_banner_edit');
Route::post('/promotionbanner/edit/{id}', [App\Http\Controllers\Admin\PromotionBannerController::class, 'update'])->name('promotionbanner.edit')->middleware('AclCheck:promotion_banner_edit');
Route::post('/promotionbanner/update/bannerurl', [App\Http\Controllers\Admin\PromotionBannerController::class, 'update_bannerurl'])->name('promotionbanner.updateurl');
Route::post('/promotionbanner/removeimage', [App\Http\Controllers\Admin\PromotionBannerController::class, 'remove_bannerimage'])->name('promotionbanner.removeimage');
Route::post('/promotionbanner/changestatus', [App\Http\Controllers\Admin\PromotionBannerController::class, 'changestatus'])->name('promotionbanner.changestatus');
Route::post('/promotionbanner/delete/{id}', [App\Http\Controllers\Admin\PromotionBannerController::class, 'destroy'])->name('promotionbanner.destroy')->middleware('AclCheck:promotion_banner_delete');

//------------------Admin/ContentPages/Slider--
Route::get('/sliders', [App\Http\Controllers\Admin\SlidersController::class, 'index'])->name('admin.sliders')->middleware('AclCheck:slider_list');
Route::get('/sliders/create', [App\Http\Controllers\Admin\SlidersController::class, 'create'])->name('slider.create')->middleware('AclCheck:slider_create');
Route::post('/sliders/create', [App\Http\Controllers\Admin\SlidersController::class, 'store'])->name('slider.create')->middleware('AclCheck:slider_create');
Route::get('/slider/edit/{id}', [App\Http\Controllers\Admin\SlidersController::class, 'edit'])->name('slider.edit')->middleware('AclCheck:slider_edit');
Route::post('/slider/edit/{id}', [App\Http\Controllers\Admin\SlidersController::class, 'update'])->name('slider.update')->middleware('AclCheck:slider_edit');
Route::get('/slider/view/{id}', [App\Http\Controllers\Admin\SlidersController::class, 'show'])->name('slider.show')->middleware('AclCheck:slider_view');
Route::post('/slider/removeMedia', [App\Http\Controllers\Admin\SlidersController::class, 'removeMedia'])->name('slider.removeMedia');
Route::post('/slider/delete/{id}', [App\Http\Controllers\Admin\SlidersController::class, 'destroy'])->name('slider.destroy')->middleware('AclCheck:slider_delete');

//------------------Admin/ContentPages/Contentpages--
Route::get('/contentpages', [App\Http\Controllers\Admin\ContentpagesController::class, 'index'])->name('admin.contentpages')->middleware('AclCheck:content_page_list');
Route::get('/contentpages/create', [App\Http\Controllers\Admin\ContentpagesController::class, 'create'])->name('contentpages.create')->middleware('AclCheck:content_page_create');
Route::post('/contentpages/create', [App\Http\Controllers\Admin\ContentpagesController::class, 'store'])->name('contentpages.create')->middleware('AclCheck:content_page_create');
Route::get('/contentpages/view/{id}', [App\Http\Controllers\Admin\ContentpagesController::class, 'show'])->name('contentpages.show')->middleware('AclCheck:content_page_view');
Route::get('/contentpages/edit/{id}', [App\Http\Controllers\Admin\ContentpagesController::class, 'edit'])->name('contentpages.edit')->middleware('AclCheck:content_page_edit');
Route::post('/contentpages/edit/{id}', [App\Http\Controllers\Admin\ContentpagesController::class, 'update'])->name('contentpages.edit')->middleware('AclCheck:content_page_edit');
Route::post('/contentpages/delete/{id}', [App\Http\Controllers\Admin\ContentpagesController::class, 'destroy'])->name('contentpages.destroy')->middleware('AclCheck:content_page_delete');
Route::post('/contentpages/removeimage', [App\Http\Controllers\Admin\ContentpagesController::class, 'remove_bannerimage'])->name('contentpages.removeImage');

//------------------Admin/HomeCategories--
Route::get('/admin/home_categories', [App\Http\Controllers\Admin\ContentpagesController::class, 'homeCategory'])->name('admin.home.categories')->middleware('AclCheck:home_category');
Route::post('/admin/home_categories', [App\Http\Controllers\Admin\ContentpagesController::class, 'store_homeCategory'])->name('admin.home.categories')->middleware('AclCheck:home_category');
Route::get('/admin/home_offerlinksection', [App\Http\Controllers\Admin\ContentpagesController::class, 'offerlinksection'])->name('admin.home.offersection');
Route::POST('/admin/home_offerlinksection', [App\Http\Controllers\Admin\ContentpagesController::class, 'offerlinkupdate'])->name('admin.home.offersection');

//------------------Admin/GeneralSettings--
Route::get('/admin/settings', [App\Http\Controllers\Admin\AdminController::class, 'settings'])->name('admin.settings')->middleware('AclCheck:general_settings');
Route::post('/admin/settings', [App\Http\Controllers\Admin\AdminController::class, 'storesettings'])->name('admin.settings')->middleware('AclCheck:general_settings');
Route::post('/admin/settings/removeimage', [App\Http\Controllers\Admin\AdminController::class, 'remove_image'])->name('admin.settings.removeImage')->middleware('AclCheck:general_settings');

//------------------Admin/SocialMedia--
Route::get('/admin/socialmedia', [App\Http\Controllers\Admin\AdminController::class, 'socialmediaSetting'])->name('admin.socialmedia')->middleware('AclCheck:social_list');
Route::post('/socialmedia/create', [App\Http\Controllers\Admin\AdminController::class, 'socialmediaSettingCreate'])->name('socialmedia.create')->middleware('AclCheck:social_create');
Route::post('/socialmedia/update', [App\Http\Controllers\Admin\AdminController::class, 'socialmediaSettingUpdate'])->name('socialmedia.update')->middleware('AclCheck:social_edit');
Route::post('/socialmedia/delete/{id}', [App\Http\Controllers\Admin\AdminController::class, 'socialmediadestroy'])->name('socialmedia.destroy')->middleware('AclCheck:social_delete');

//---Admin/NewsLetter
Route::get('admin/newsletter', [App\Http\Controllers\Admin\NewsLetterController::class, 'index'])->name('newsletter.index');
Route::post('newsletter/mail', [App\Http\Controllers\Admin\NewsLetterController::class, 'sentNewsletterMail'])->name('sent.newsletter');
Route::post('newsletter/mailtoall', [App\Http\Controllers\Admin\NewsLetterController::class, 'sentNewsletterMailtoAll'])->name('sent.newslettertoAll');



//------------------Admin/Customersupport--
Route::get('admin/customersupport', [App\Http\Controllers\Admin\AdmincustomersupportController::class, 'index'])->name('customersupport.index');
Route::get('admin/customersupport/create', [App\Http\Controllers\Admin\AdmincustomersupportController::class, 'create'])->name('customersupport.create');
Route::post('admin/customersupport/create', [App\Http\Controllers\Admin\AdmincustomersupportController::class, 'store'])->name('customersupport.create');
Route::get('admin/customersupport/edit/{id}', [App\Http\Controllers\Admin\AdmincustomersupportController::class, 'edit'])->name('customersupport.edit');
Route::post('admin/customersupport/edit/{id}', [App\Http\Controllers\Admin\AdmincustomersupportController::class, 'update'])->name('customersupport.update');
Route::post('admin/customersupport/delete/{id}', [App\Http\Controllers\Admin\AdmincustomersupportController::class, 'destroy'])->name('customersupport.destroy');
Route::post('admin/customersupport/status', [App\Http\Controllers\Admin\AdmincustomersupportController::class, 'update_status'])->name('customersupport.status');

//--------------------Admin/reports
Route::get('admin/reports/productreports',[App\Http\Controllers\Admin\ReportsController::class, 'productreports'])->name('admin.reports.product');
Route::get('admin/reports/orderreports',[App\Http\Controllers\Admin\ReportsController::class, 'order_reports'])->name('admin.reports.order');
Route::get('admin/reports/salesreports',[App\Http\Controllers\Admin\ReportsController::class, 'sales_reports'])->name('admin.reports.sales');

//================================================================================================================

//------------------CustomerSupport/Login--
Route::get('/login/customersupport',[App\Http\Controllers\Auth\LoginController::class, 'showcustomersupportLoginForm'])->name('login.customersupport');
Route::post('/login/customersupport', [App\Http\Controllers\Auth\LoginController::class, 'customersupportLogin'])->name('login.customersupport');
Route::get('/customersupport/logout', [App\Http\Controllers\Auth\LoginController::class, 'customersupportlogout'])->name('customersupport.logout');

//------------------CustomerSupport/Profile--CustomerSupport
Route::get('/customersupport/profile',[App\Http\Controllers\CustomerSupport\CustomersupportController::class, 'profile'])->name('customersupport.profile');
Route::post('/customersupport/updateprofile',[App\Http\Controllers\CustomerSupport\CustomersupportController::class, 'updateprofile'])->name('customersupport.updateprofile');
Route::post('/customersupport/change-password',[App\Http\Controllers\CustomerSupport\CustomersupportController::class, 'changePassword'])->name('customersupport.changePassword');

//------------------CustomerSupport/Chat--
Route::get('/customersupport/manage/chat', [App\Http\Controllers\CustomerSupport\CustomersupportController::class, 'manage_chat'])->name('customersupport.manage.chat');
Route::post('/customersupport/chat/attend', [App\Http\Controllers\CustomerSupport\CustomersupportController::class, 'chat_attend'])->name('customersupport.chat.attend');
Route::post('/adminbotman/loadmessage', [App\Http\Controllers\CustomerSupport\CustomersupportController::class, 'load_message'])->name('admin.chat.load');
Route::post('/adminbotman/chat/disconnect', [App\Http\Controllers\CustomerSupport\CustomersupportController::class, 'chat_disconnect'])->name('admin.chat.disconnect');


//------------------AdminBotman/Chat--
Route::match(['get', 'post'], '/adminbotman', [App\Http\Controllers\CustomerSupport\AdminBotManController::class, 'handle']);
Route::match(['get', 'post'], '/adminbotman/send',[App\Http\Controllers\CustomerSupport\AdminBotManController::class, 'sendmessage'] )->name('admin.chat.send');

//================================================================================================================
//================================================================================================================
//================================================================================================================

//------------------FrontEnd--
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::post('/home/banners', [App\Http\Controllers\HomeController::class, 'get_homebanners'])->name('home.getBanners');
Route::post('/home/Pagebanners', [App\Http\Controllers\HomeController::class, 'get_pagebanners'])->name('page.getBanners');
Route::get('/user/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('user.profile');
Route::get('/home/categories', [App\Http\Controllers\HomeController::class, 'Homecategories'])->name('home.categories');

Route::get('/category/all-medicines', [App\Http\Controllers\HomeController::class, 'all_medicine_categories'])->name('list.medicine.categories');
Route::post('/category/subcategory', [App\Http\Controllers\HomeController::class, 'Subcategories'])->name('find.subcategory');
Route::get('/list/all-medicines', [App\Http\Controllers\ShoppingController::class, 'all_medicines'])->name('list.all-medicines');
Route::get('/list/all-brands', [App\Http\Controllers\ShoppingController::class, 'all_brands'])->name('list.all-brands');
Route::get('/list/offer-products', [App\Http\Controllers\ShoppingController::class, 'offerproductslist'])->name('list.offerproducts');
Route::get('/track/order', [App\Http\Controllers\ShoppingController::class, 'trackorder'])->name('track.order');

//------------------FrontEnd/User/Register--
Route::get('/register/createuser', [App\Http\Controllers\Auth\RegisterController::class, 'view_registerUser'])->name('register.view');
Route::post('/register/user', [App\Http\Controllers\Auth\RegisterController::class, 'registerUser'])->name('register.user');

//------------------FrontEnd/User/Login--
Route::post('/login/user', [App\Http\Controllers\Auth\LoginController::class, 'userLogin'])->name('user.login');
Route::post('/otplogin/user', [App\Http\Controllers\Auth\LoginController::class, 'userOtpLogin'])->name('user.otp_login');
Route::post('/login/requestotp', [App\Http\Controllers\Auth\LoginController::class, 'request_otp'])->name('user.otp_request');

Route::get('/user/logout', [App\Http\Controllers\Auth\LoginController::class, 'userLogout'])->name('user.logout');

//------------------FrontEnd/User/ForgotPassword--
Route::get('user/reset/password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showuser_resetpassword'])->name('user.reset.password');
Route::post('user/reset/password', [App\Http\Controllers\Auth\ResetPasswordController::class, 'sentuser_reset'])->name('user.reset.password');
Route::get('user/reset/verify/{email}/{key}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'verifyuser_PasswordReset'])->name('user.reset.verifyCustomer');
Route::post('user/reset/verify/{email}/{key}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'UserPasswordReset'])->name('user.reset.verifyCustomer');

//------------------FrontEnd/Shopping/Products--
Route::get('/item/{product_url}', [App\Http\Controllers\ShoppingController::class, 'index'])->name('shopping.productdetail');
Route::get('/productlisting', [App\Http\Controllers\ShoppingController::class, 'productlisting'])->name('list.allproductlisting');
Route::get('/productlisting/category/{categoryname}', [App\Http\Controllers\ShoppingController::class, 'category_productlisting'])->name('shopping.productlisting');
Route::get('autocomplete/itemsearch', [App\Http\Controllers\ShoppingController::class, 'search_item'])->name('product.searchkeyword');
Route::post('prescription/uploadfile', [App\Http\Controllers\ShoppingController::class, 'upload_prescription'])->name('product.prescription.upload');
Route::post('delete/prescription/uploadfile', [App\Http\Controllers\ShoppingController::class, 'delete_prescription'])->name('prescription.upload.delete');
Route::get('/generalprescription', [App\Http\Controllers\ShoppingController::class, 'create_prescription'])->name('generalprescription.create');
Route::post('/generalprescription',[App\Http\Controllers\ShoppingController::class, 'store_prescription'])->name('generalprescription.store');

Route::get('generalprescription/addmedicine', [App\Http\Controllers\ShoppingController::class, 'add_prescription_medicine'])->name('prescription.addmedicine');


//------------------FrontEnd/Shopping/Products/cart--
Route::get('/product/cart', [App\Http\Controllers\ShoppingController::class, 'cart'])->name('product.cart');
Route::post('product/addTocart', [App\Http\Controllers\ShoppingController::class, 'productaddcart'])->name('product.addTocart');
Route::post('product/deletecart', [App\Http\Controllers\ShoppingController::class, 'productdeletecart'])->name('product.deleteFromcart');

//------------------FrontEnd/Shopping/Products/checkout--
Route::get('/product/checkout', [App\Http\Controllers\ShoppingController::class, 'checkout'])->name('product.checkout');
Route::get('/product/ordercheckout/{order_id}', [App\Http\Controllers\ShoppingController::class, 'order_checkout'])->name('order.checkout');
Route::post('/checkout/updateaddress', [App\Http\Controllers\ShoppingController::class, 'checkout_UpdateAddress'])->name('checkout.updateAddress');
Route::post('/product/placeorder', [App\Http\Controllers\ShoppingController::class, 'placeOrder'])->name('product.placeorder');
Route::post('/product/placeorderbuynow', [App\Http\Controllers\ShoppingController::class, 'placeOrder_buynow'])->name('product.placeorder.buynow');
Route::get('/order/invoice/{id}', [App\Http\Controllers\ShoppingController::class, 'invoiceOrder'])->name('order.invoice');
Route::get('/order/invoice/print/{id}', [App\Http\Controllers\ShoppingController::class, 'print_invoiceOrder'])->name('order.invoice.print');
Route::post('getChecksum',[App\Http\Controllers\ShoppingController::class, 'getChecksum'])->name('order.sum');
Route::post('payment/response', [App\Http\Controllers\ShoppingController::class, 'payment_response'])->name('payment.response');
Route::get('razorpay-payment/response', [App\Http\Controllers\ShoppingController::class, 'RazorPaymentResponse'])->name('razorpay.payment.store');
Route::post('/stores/list', [App\Http\Controllers\ShoppingController::class, 'stores_list'])->name('stores.list');

Route::post('payment/return/{checkout_type}', [App\Http\Controllers\ShoppingController::class, 'payment_return'])->name('payment.return');
Route::post('payment/response-webhook', [App\Http\Controllers\ShoppingController::class, 'payment_response_webhook'])->name('payment.response.webhook');
Route::post('/order/payment/', [App\Http\Controllers\ShoppingController::class, 'order_payment'])->name('order.payment');


//------------------FrontEnd/Shopping/Wishlist--
Route::post('/add/wishlist', [App\Http\Controllers\ShoppingController::class, 'manage_wishlist'])->name('add.wishlist');

//------------------FrontEnd/ContentPages--
Route::get('page/{seo_url}', [App\Http\Controllers\HomeController::class, 'viewcontentpage'])->name('view.contentpage');
Route::get('/contact_us', [App\Http\Controllers\HomeController::class, 'view_contact_page'])->name('view.contact_us');
Route::post('contactus/team', [App\Http\Controllers\HomeController::class, 'sentContact'])->name('sent.Contactus');
Route::get('page-ap/{seo_url}', [App\Http\Controllers\HomeController::class, 'viewcontentpage_api']);
Route::get('team', [App\Http\Controllers\HomeController::class, 'OurTeam'])->name('page.teams');
//------------------FrontEnd/Careers/Jobs--
Route::get('/careers/viewjobs', [App\Http\Controllers\HomeController::class, 'view_careerjobs'])->name('view.career.jobs');
Route::get('/careers/job/apply/{id}', [App\Http\Controllers\HomeController::class, 'apply_careerjobs'])->name('apply.career.jobs');
Route::post('/careers/job/apply/{id}', [App\Http\Controllers\HomeController::class, 'store_careerjobs'])->name('apply.career.jobs');

//--------------FrontEnd/News&Events
Route::get('news-evets',                      [App\Http\Controllers\HomeController::class, 'news_evets'])->name('news_evets');
Route::get('news-evets-details/{id}',         [App\Http\Controllers\HomeController::class, 'news_evets_details'])->name('news_evets_details');

//------------------FrontEnd/Newsletter--
Route::post('/newsletter/subscribe', [App\Http\Controllers\HomeController::class, 'Subscribe_newsletter'])->name('newsletter.subscribe');
Route::post('/newsletter/unsubscribe', [App\Http\Controllers\HomeController::class, 'Unsubscribe_newsletter'])->name('newsletter.unsubscribe');


//------------------FrontEnd/LoadState--
Route::post('/ajax/stateLoader', [App\Http\Controllers\HomeController::class, 'stateLoader'])->name('ajax.stateLoader');

//------------------FrontEnd/UserProfile--
Route::get('/myaccount', [App\Http\Controllers\MyaccountController::class, 'myaccount'])->name('user.myaccount');
Route::get('/myaccount/wishlist', [App\Http\Controllers\MyaccountController::class, 'wishlist'])->name('myaccount.wishlist');
Route::get('/myaccount/orderhistory', [App\Http\Controllers\MyaccountController::class, 'orderhistory'])->name('myaccount.orderhistory');
Route::get('/myaccount/changepassword', [App\Http\Controllers\MyaccountController::class, 'changeUserPassword'])->name('myaccount.changepassword');
Route::post('/myaccount/changepassword', [App\Http\Controllers\MyaccountController::class, 'updateUserPassword'])->name('myaccount.updatepassword');
Route::get('/myaccount/delete', [App\Http\Controllers\MyaccountController::class, 'delete_account'])->name('myaccount.delete');


Route::post('/myaccount/orderstatus_manage', [App\Http\Controllers\MyaccountController::class, 'orderstatus_manage'])->name('change.orderstatus');
Route::post('/myaccount/review/delete', [App\Http\Controllers\MyaccountController::class, 'delete_productreview'])->name('customer.review.delete');

//------------------FrontEnd/UserProfile/Address--
Route::post('/update/profile', [App\Http\Controllers\MyaccountController::class, 'updateProfile'])->name('update.profile');
Route::post('/update/profilepic', [App\Http\Controllers\MyaccountController::class, 'profilepic'])->name('update.profilepic');
Route::post('/add/profileaddress', [App\Http\Controllers\MyaccountController::class, 'add_profileaddress'])->name('profile.add.address');
Route::post('/get/address', [App\Http\Controllers\MyaccountController::class, 'get_profileaddress'])->name('profile.getaddress');
Route::post('/update/address', [App\Http\Controllers\MyaccountController::class, 'update_profileaddress'])->name('profile.updateaddress');

//------------------FrontEnd/UserProfile/ProductReview--
Route::post('/myaccount/add/productreview', [App\Http\Controllers\MyaccountController::class, 'add_productreview'])->name('add.product.review');

//------------------Botman/Chat--
Route::match(['get', 'post'], '/botman', [App\Http\Controllers\CustomerSupport\BotManController::class, 'handle']);
Route::match(['get', 'post'], '/botman/send/chat_message_send',[App\Http\Controllers\CustomerSupport\BotManController::class, 'sendmessage'])->name('chat_message.send');
Route::match(['get', 'post'], '/botman/load/chat_message_load',[App\Http\Controllers\CustomerSupport\BotManController::class, 'load_message'])->name('chat_message.load');
Route::get('/add_watermark', [App\Http\Controllers\ShoppingController::class, 'add_watermark'])->name('admin.watermark');

