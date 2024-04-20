
var elm;
var cart_wish_btn;
$(document).on('click', '.plusButton', function (e) {
    var product_id = $(this).attr('data-id');
    var quantity = 1;
    var _token = $('#csrftoken').val();
    elm = false;
    ajaxAddtoCart(_token, product_id, quantity);
});

$(document).on('click', '.minusButton', function (e) {
    var product_id = $(this).attr('data-id');
    var _token = $('#csrftoken').val();
    elm = false;

    ajaxDeleteFromCart(_token, product_id, 'single');
});

$(document).on('click', '.add-cart', function (e) {
    var product_id = $(this).val();
    var quantity = $(this).closest('.sec_productadd').find('.input-number').val();
    var _token = $('#csrftoken').val();

    elm = $(this).parent();
    cart_wish_btn = elm.html();


    ajaxAddtoCart(_token, product_id, quantity);
});

$(document).on('click', '.add-cart-list', function (e) {
    var product_id = $(this).attr('value');
    var quantity = $(this).closest('.sec_productadd').find('.input-number').val();
    var _token = $('#csrftoken').val();

    elm = $(this).parent();
    cart_wish_btn = elm.html();
    $(this).html('<i class="fas fa-shopping-cart"></i>Adding..');

    result = ajaxAddtoCartfromlist(_token, product_id, quantity);


});

$(document).on('click', '#empty_cart', function (e) {
    var _token = $('#csrftoken').val();
    elm = false;

    ajaxDeleteFromCart(_token, 0, 'all');
});

function deleteCart(productId = null) {
    if (productId != null) {
        var _token = $('#csrftoken').val();
        elm = false;
        ajaxDeleteFromCart(_token, productId, 'product');
    }
}

function ajaxAddtoCart(_token, product_id, quantity) {
    $.ajax({
        type: "post",
        url: url_addToCart,
        data: {
            "_token": _token,
            'product_id': product_id,
            'quantity': quantity,
        },
        // beforeSend: function(){
        //   },

        success: function (response) {
            if (response.success) {
                displayMessage("Product Added to cart", 'success');
            } else {
                displayMessage(response.errorMsg, 'error');
            }
            cart = [];
            $.each(response.data, function (i, element) {

                cart.push({
                    product_id: element.product_id,
                    product_url: element.product_url,
                    product_image: element.product_image,
                    product_name: element.product_name,
                    quantity: element.quantity,
                    price: element.ProductPrice,
                    offer_percent: element.offer_percent,
                    tax_details: (element.tax_details != '' ? element.tax_details : 0),
                    total_amount: element.ProductPrice * element.quantity,
                });
            });
            countcart = cart.length;
            DisplayCart();

            if (elm != false) {
                // elm.html(cart_wish_btn);
            }
        }
    });
}

function ajaxAddtoCartfromlist(_token, product_id, quantity) {
    // alert(product_id);
    $.ajax({
        type: "post",
        url: url_addToCart,
        data: {
            "_token": _token,
            'product_id': product_id,
            'quantity': quantity,
        },
        success: function (response) {

            if (response.success) {
                $('.add-cart-list_' + product_id).html('<i class="fas fa-check"></i>Added');
                setTimeout(() => {
                    $('.add-cart-list_' + product_id).html('<i class="fas fa-shopping-cart"></i>Add');
                }, 3000);
            } else {

                displayMessage(response.errorMsg, 'error');
            }
            cart = [];
            $.each(response.data, function (i, element) {
                cart.push({
                    product_id: element.product_id,
                    product_image: element.product_image,
                    product_url:element.product_url,
                    product_name: element.product_name,
                    quantity: element.quantity,
                    price: element.ProductPrice,
                    offer_percent: element.offer_percent,
                    tax_details: (element.tax_details != '' ? element.tax_details : 0),
                    total_amount: element.ProductPrice * element.quantity,
                });
            });
            countcart = cart.length;
            DisplayCart();

            if (elm != false) {
                // elm.html(cart_wish_btn);
            }
        }
    });


}

function ajaxDeleteFromCart(_token, product_id, quantity) {
    var html = '';
    $.ajax({

        type: "post",
        url: url_deleteCart,
        data: {
            "_token": _token,
            'product_id': product_id,
            'quantity': quantity
        },
        success: function (response) {
            if (response.success) {
                displayMessage("Product removed from cart", 'success');
            } else {
                displayMessage(response.errorMsg, 'error');
            }
            cart = [];
            $.each(response.data, function (i, element) {
                cart.push({
                    product_id: element.product_id,
                    product_url: element.product_url,
                    product_image: element.product_image,
                    product_name: element.product_name,
                    quantity: element.quantity,
                    price: element.ProductPrice,
                    offer_percent: element.offer_percent,
                    tax_details: (element.tax_details != '' ? element.tax_details : 0),
                    total_amount: element.ProductPrice * element.quantity,
                });
            });
            countcart = cart.length;
            DisplayCart();
            if (quantity == 'all') {
                $(".cart-count").text(0);

                html += '<div class="main-cart-full">';
                html += '<div class="col-md-12 row full-cart-outer">';
                html += '<h6>Your cart is empty</h6>';
                html += '</div>';
                html += '</div>';
                $("#cart_content").html(html);
            }
        }
    });
}

function displayMessage(message, type) {
    if (type == 'success' && elm != false) {
        // elm.closest('.row').next('.style_alert').show().text(message).delay(1000).fadeOut();
        swal({
            title: 'Added',
            text: message,
            type: 'success',
            timer: 1500,
            showCancelButton: false,
            showConfirmButton: false
        });
    }
}

function DisplayCart() {

    var carthtml = '';
    var grandTotal = 0.00;
    var itemPrice = 0;
    var cart_content = '';
    if (cart.length > 0) {
        $.each(cart, function (i, element) {
            // alert(element.product_url);

            carthtml += '<div class="main-cart-full">';
            // carthtml += '<div class="col-md-12 row full-cart-outer">';
            carthtml += '<div class="cart-content-first">';
            carthtml += '<div class="delete-cart"><a href="javascript:void(0)" onclick="deleteCart(' + element.product_id + ')"><i class="fa fa-trash trash" aria-hidden="true"></i></a></div>';

            carthtml += '<div class="cartdesc-img-outer">';
            carthtml += '<a href="' + url_productdetails + '/' + element.product_url + '">';
            if (element.product_image != null && element.product_image != '') {
                carthtml += '<img src="' + product_image_path + '/' + element.product_image + '" class="img-fluid">';
            } else {
                carthtml += '<img src="' + assets_path + '/no-image.jpg" class="img-fluid no-image">';
            }
            carthtml += '</a>';
            carthtml += '</div>';
            carthtml += (element.offer_percent > 0 ? '<div class="cart-off"><span>' + element.offer_percent + '% Off</span></div>' : '');



            carthtml += '<div class="cart-content-first-desc"><a href="' + url_productdetails + '/' + element.product_url + '">' + element.product_name + '</a>';





            itemPrice = (element.price != 0 ? element.price : 0);


            // carthtml += '<div class="quantity-price">';
            // carthtml += '<div class="item-price cart-price">'+ currencyIcon + (parseFloat(itemPrice)).toFixed(2) + '</div>';

            //Tax section starts--
            // var total_with_tax = 0;
            // $.each(element.tax_details, function (i, tax) {
            //     var tax_amt = (element.total_amount * tax.percentage) / 100;
            //     carthtml += '<small><span class="f-16 mr-1">' + tax.percentage + '% ' + tax.tax_name + '</span></small>';
            //     carthtml += '<small>' + currencyIcon + '<span class="f-16 mr-1">' + tax_amt.toFixed(2) + '</span><br></small>';
            //     total_with_tax += tax_amt;
            // });

            var total_with_tax = 0;
            var total_tax_percent = 0;
            $.each(element.tax_details, function (i, tax) {
                total_tax_percent = total_tax_percent+tax.percentage;
                // var tax_amt = (element.total_amount * tax.percentage) / 100;
                // carthtml += '<div class="vat_tax"><small><span class="f-16 mr-1">' + tax.percentage + '% ' + tax.tax_name + '</span></small>';
                // carthtml += '<small>' + currencyIcon + '<span class="f-16 mr-1">' + tax_amt.toFixed(2) + '</span><br></small></div>';
                // total_with_tax += tax_amt;
            });
            total_tax_percent_value = element.total_amount*100 / (total_tax_percent+100);
            $.each(element.tax_details, function (i, tax) {
              var taxt_pers =  tax.percentage*100/total_tax_percent;
              var cgst = (element.total_amount-total_tax_percent_value)*taxt_pers/100;
              carthtml += '<div class="vat_tax"><small><span class="f-16 mr-1">' + tax.percentage + '% ' + tax.tax_name + '</span></small>';
                carthtml += '<small>' + currencyIcon + '<span class="f-16 mr-1">' + cgst.toFixed(2) + '</span><br></small></div>';

            });
            carthtml += '</div></div>';
            //Tax section starts--

            // carthtml += '</div>';

            carthtml += '<div class="full-carts-total d-flex flex-grow-1">';
            carthtml += '<div class="quantity-price">';
            carthtml += '<div class="item-price cart-price">' + currencyIcon + (parseFloat(itemPrice)).toFixed(2) + '</div>';
            carthtml += '</div>';

            carthtml += '<div class="cart-quantity">';
            carthtml += '<div class="input-group quantity-count">';
            carthtml += '<span class="input-group-btn">';
            carthtml += '<button type="button" class="btn cart-no minusButton" data-type="minus" data-id="' + element.product_id + '" ' + (element.quantity <= 1 ? 'disabled' : '') + '>';
            carthtml += '<i class="fas fa-minus" aria-hidden="true"></i>';
            carthtml += '</button>';
            carthtml += '</span>';
            carthtml += '<input type="text" class="form-control input-number" value="' + element.quantity + '" min="1">';
            carthtml += '<span class="input-group-btn">';
            carthtml += '<button type="button" class="btn cart-no plusButton" data-type="plus" data-id="' + element.product_id + '">';
            carthtml += '<i class="fas fa-plus" aria-hidden="true"></i>';
            carthtml += '</button>';
            carthtml += '</span>';
            carthtml += '</div>';
            carthtml += '</div>';

            carthtml += '<div class="cart-sub-total">';
            carthtml += '<div class="item-price cart-price">' + currencyIcon + ((element.quantity * itemPrice) ).toFixed(2) + '</div>';
            carthtml += '</div>';

            // carthtml += '</div>';



            //Tax section starts--

            //Tax section starts--

            carthtml += '</div>';

            carthtml += '</div>';

            // grandTotal += ((element.quantity * itemPrice) + total_with_tax);
            grandTotal += ((element.quantity * itemPrice));
        });
    } else {
        carthtml += '<div class="main-cart-full">';
        carthtml += '<div class="col-md-12 row full-cart-outer">';
        carthtml += '<h6>Your cart is empty</h6>';
        carthtml += '</div>';
        carthtml += '</div>';
    }

    cart_content = carthtml;
    if (cart.length > 0) {
    carthtml += '<div class="col-md-12 main-grand-total-outer">';
    carthtml += '<div class="row grand-total-outer">';
    // carthtml += '<div class="col-md-6 full-amnt-outer">';
    // carthtml += '</div>';

    carthtml += '<div class="col-md-6 full-btn-outr">';
    carthtml += '<div class="full-amnt-outer">';
    carthtml += '<div class="cart-total">Total</div>';
    carthtml += '<div class="cart-amount-total">' + currencyIcon + ' ' + grandTotal.toFixed(2) + '</div>';
    carthtml += '</div>';
    carthtml += '<div class="full-check-outer">';
    carthtml += '<div class="cart-icon"><a class="btn butn-blue" href="' + cart_path + '" style="text-decoration:none"><i class="fas fa-shopping-cart"></i> Cart</a></div>';
    carthtml += '<div class="checkout-icon"><a class="btn btn-green" href="' + checkoutUrl + '" style="text-decoration:none"><i class="fas fa-credit-card"></i> Checkout</a></div>';
    carthtml += '</div>';
    carthtml += '</div>';
    carthtml += '</div>';
    carthtml += '</div>';
    }

    // if (countcart > 0) {
    //     carthtml += '<div class="col-md-12 top-grand-total-outer">';
    //     carthtml += '<div class="row">';
    //     carthtml += '<div class="col-md-6 text-right"><div class="cart-icon"><a class="btn butn-blue" href="' + cart_path + '" style="text-decoration:none"><i class="fas fa-shopping-cart"></i> Cart</a></div></div>';
    //     carthtml += '<div class="col-md-6 text-right">';
    //     carthtml += '<div class="checkout-icon"><a class="btn btn-green" href="' + checkoutUrl + '" style="text-decoration:none"><i class="fas fa-credit-card"></i> Checkout</a></div>';
    //     carthtml += '</div>';
    //     carthtml += '</div>';
    //     carthtml += '</div>';
    // }

    $("#collapsecart").html(carthtml);
    $("#cart_content").html(cart_content);
    $('.cart-count').text(countcart);
    $('#checkout_subtot').html(currencyIcon + grandTotal.toFixed(2));
    $('#checkout_total').html(currencyIcon + grandTotal.toFixed(2));
}
