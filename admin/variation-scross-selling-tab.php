<?php

add_action( 'woocommerce_product_after_variable_attributes', 'variation_settings_fields', 10, 3 );
add_action( 'woocommerce_save_product_variation', 'save_variation_settings_fields', 10, 2 );
add_filter( 'woocommerce_available_variation', 'load_variation_settings_fields' );

function variation_settings_fields( $loop, $variation_data, $variation ) 
{
    ?>
        <p class="form-field">
            <label for="cross_sells_products"><?php _e( 'Cross-sells', 'woocommerce' ); ?></label>
            <select class="wc-product-search" multiple="multiple" style="width: 50%;" id="cross_sells_products" name="cross_sells_products[]" data-placeholder="<?php esc_attr_e( 'Search for a product&hellip;', 'woocommerce' ); ?>" data-action="woocommerce_json_search_products_and_variations" data-exclude="<?php echo intval( $variation->ID ); ?>">
             <?php
            $product_ids = get_post_meta( $variation->ID, 'cross_sells_products', true );

            foreach ( $product_ids as $product_id ) {
                $product = wc_get_product( $product_id );
                if ( is_object( $product ) ) {
                    echo '<option value="' . esc_attr( $product_id ) . '"' . selected( true, true, false ) . '>' . wp_kses_post( $product->get_formatted_name() ) . '</option>';
                }
            }
        ?>   
            </select> <?php echo wc_help_tip( __( 'Select Products Here.', 'woocommerce' ) ); ?>
        </p>

    <?php
}

function save_variation_settings_fields( $variation_id, $loop ) 
{
    $cross_sells_products_ids = $_POST['cross_sells_products'];
    update_post_meta( $variation_id, 'cross_sells_products',  $cross_sells_products_ids );
}

function load_variation_settings_fields( $variation ) 
{     
    $variation['cross_sells_products'] = get_post_meta( $variation[ 'variation_id' ], 'cross_sells_products', true );

    return $variation;
}



