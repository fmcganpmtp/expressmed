<!-- My wishlist view -->
<div class="pb-90" role="tabpanel" aria-labelledby="nav-wishlist-tab">
    <div class="row">
        <div class="col-md-12">
            <div id="msg_alert" class="alert alert-success" style="display: none"></div>
            <p class="f-18 text-uppercase font-weight-bold cyan pb-10 pt-50 mb-0">WishList</p>
        </div>
        @php
            $currency_key = array_search('site_currency_icon', array_column($common_settings, 'item'));
            $Discount = 0;
            $bannerCnt = 1;
        @endphp
        @forelse ($wishlist_products as $prd_wishlist)
            @php
                if ($prd_wishlist->minoffer_price != 0) {
                    $Discount = (($prd_wishlist->minprice - $prd_wishlist->minoffer_price) * 100) / $prd_wishlist->minprice;
                }
            @endphp

            <div class="col-lg-3 col-sm-4 col-6 products-content">
                <div class="product-listing">
                    <a href="{{ route('shopping.productdetail', $prd_wishlist->product_url) }}">
                        @if ($prd_wishlist->product_image != '')
                            <img src="{{ asset('assets/uploads/products/') . '/' . $prd_wishlist->product_image }}" class="img-fluid" alt="">
                        @else
                            <img src="{{ asset('img/no-image.jpg') }}" class="img-fluid">
                        @endif
                    </a>
                </div>
                <div class="star-icon">
                    <a href="javascript:void(0)" class="profile_wishlist" data_item="{{ $prd_wishlist->id }}">
                        @if (!empty($wishlist) && in_array($prd_wishlist->id, array_column($wishlist, 'product_id')))
                            <img src="{{ asset('front_view/images/star-icon.png') }}">
                        @else
                            <img src="{{ asset('front_view/images/wishlist.png') }}">
                        @endif
                    </a>
                </div>
                <div class="item-head"><a href="{{ route('shopping.productdetail', $prd_wishlist->product_url) }}">{{ $prd_wishlist->product_name }}</a></div>
                <div class="item-price">{!! $common_settings[$currency_key]['value'] !!}{{ $prd_wishlist->offer_price == 0 ? number_format($prd_wishlist->price, 2) : number_format($prd_wishlist->offer_price, 2) }}
                    @if ($prd_wishlist->offer_price != 0)<div class="old-price">{!! $common_settings[$currency_key]['value'] !!}{{ number_format($prd_wishlist->price, 2) }}</div>@endif
                </div>
            </div>
        @empty

            <h4>No items added in the Wishlist to be displayed.</h4>

        @endforelse
        {{ $wishlist_products->links('pagination::bootstrap-4') }}

    </div>
</div>
