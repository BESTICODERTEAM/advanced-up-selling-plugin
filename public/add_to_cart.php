<?php

add_filter( 'woocommerce_add_to_cart_validation', 'filter_add_to_cart_validation', 10, 3 );
function filter_add_to_cart_validation( $passed, $product_id, $quantity ) 
{ 
	
    do_action('more_item_add_to_cart_action');

    if ( WC()->cart->is_empty() ) return $passed;

    foreach( WC()->cart->get_cart() as $cart_item ) {
       
        if( $current_vendor->id != $cart_vendor->id ) {
            $passed = false;
            break; 
        }
    }
    
    return $passed;

}


add_action( 'more_item_add_to_cart_action',  'more_item_add_to_cart_action_fun'  );
function more_item_add_to_cart_action_fun()
{

	/*print_r($_POST);
	exit();*/
	if (isset($_POST['add-to-cart'])) 
	{
		$more_item = $_POST['products_id'];
		if (isset($more_item)) 
		{
			foreach ($more_item as $key => $id) 
			{
				if (!empty($id)) 
				{

					WC()->cart->add_to_cart( $id );
				}
			}
		}
	}

}



/**
 * Custom Add To Cart Messages
 * Add this to your theme functions.php file
 **/
add_filter( 'wc_add_to_cart_message', 'custom_add_to_cart_message' );
function custom_add_to_cart_message() {

	$ProNameArr = array();	



	if (isset($_POST['add-to-cart'])) 
	{

		$productname = get_the_title($_POST['add-to-cart']);
		array_push($ProNameArr, $productname);

		$more_item = $_POST['products_id'];
		if (!empty($more_item)) 
		{
			foreach ($more_item as $key => $id) 
			{
				$productname = get_the_title($id);

				array_push($ProNameArr, $productname);

			}
		}
	}
	
    global $woocommerce;
    /*Output success messages “test proudct AS” has been added to your cart.*/
    if (get_option('woocommerce_cart_redirect_after_add')=='yes') :
        $return_to  = get_permalink(woocommerce_get_page_id('shop'));
        $message    = sprintf('<a href="%s" class="button">%s</a> %s', $return_to, __('Continue Shopping &rarr;', 'woocommerce'), __('“'.implode(",",$ProNameArr).'” has been added to your cart.', 'woocommerce') );
    else :
        $message    = sprintf('<a href="%s" class="button">%s</a> %s', get_permalink(woocommerce_get_page_id('cart')), __('View Cart &rarr;', 'woocommerce'), __('“'.implode(",",$ProNameArr).'” has been added to your cart.', 'woocommerce') );
    endif;
        return $message;
}
/* Custom Add To Cart Messages */