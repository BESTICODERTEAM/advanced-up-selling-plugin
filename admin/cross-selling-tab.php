<?php

// First Register the Tab by hooking into the 'woocommerce_product_data_tabs' filter
add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab' );
function add_my_custom_product_data_tab( $product_data_tabs ) {
    $product_data_tabs['cross-selling-tab'] = array(
        'label' => __( 'Cross Selling Product', 'woocommerce' ),
        'target' => 'cross_selling_product_data',
        // 'class'     => array( 'show_if_simple' ),
    );
    return $product_data_tabs;
}

/** CSS To Add Custom tab Icon */
function wcpp_custom_style() {?>
<style>
#woocommerce-product-data ul.wc-tabs li.my-custom-tab_options a:before { font-family: WooCommerce; content: '\e006'; }
</style>
<?php 
}
add_action( 'admin_head', 'wcpp_custom_style' );

// functions you can call to output text boxes, select boxes, etc.
add_action('woocommerce_product_data_panels', 'woocom_custom_product_data_fields');

function woocom_custom_product_data_fields() 
{
    global $woocommerce, $post;

    /* Note the 'id' attribute needs to match the 'target' parameter set above*/
    ?> 

    <div id='cross_selling_product_data' class='panel woocommerce_options_panel'> 
    	<div class='options_group'> 
    		<p class="form-field">
		    <label for="cross_sells_products"><?php _e( 'Cross-sells', 'woocommerce' ); ?></label>
		    <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="cross_sells_products" name="cross_sells_products[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $post->ID ); ?>">
		     <?php
            $product_ids = get_post_meta( $post->ID, '_cross_sells_products_ids', true );

            foreach ( $product_ids as $product_id ) {
                $product = wc_get_product( $product_id );
                if ( is_object( $product ) ) {
                    echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                }
            }
        ?>   
		    </select> <?php echo wc_help_tip( __( 'Select Products Here.', 'woocommerce' ) ); ?>
		</p>

    	</div>
    </div>
	<?php
}

/** Hook callback function to save custom fields information */
add_action( 'woocommerce_process_product_meta_simple', 'woocom_save_proddata_custom_fields'  );
add_action( 'woocommerce_process_product_meta_variable', 'woocom_save_proddata_custom_fields');
function woocom_save_proddata_custom_fields($post_id) {


    /* Save Select */
	$product_field_type =  $_POST['cross_sells_products'];
    update_post_meta( $post_id, '_cross_sells_products_ids', $product_field_type );

}


