<?php

add_action( 'woocommerce_before_single_variation', 'action_wc_before_single_variation' );
function action_wc_before_single_variation() 
{
    ?>
    <script type="text/javascript">
    (function($){
        $('form.variations_form').on('show_variation', function(event, data){

           var variation_id =  data.variation_id;
          jQuery.ajax({
		    type:"POST",
		    url:"<?php echo admin_url('admin-ajax.php'); ?>",
		    data: {action:'my_special_ajax_call_for_variation',variation_id:variation_id},
		    success:function(res)
		    {
		    	jQuery('#more_selling_product').html(res);

		    }
		});

        });
    })(jQuery);
    </script>
    <?php
}




add_action('wp_ajax_my_special_ajax_call_for_variation', 'scross_sell');
add_action('wp_ajax_nopriv_my_special_ajax_call_for_variation', 'scross_sell');
function scross_sell()
{ 
	$_cross_sells_products_ids = get_post_meta( $_POST['variation_id'], 'cross_sells_products', true );


	$html = '';
	$variation_select = '';
	if (!empty($_cross_sells_products_ids)) 
	{


		$html .= '<div class="cross-selling-wrapper">';
		// $html .= '<h4>'.esc_html__('Buy at once', 'cross-selling').'</h3>';
		/*$html .= '<h4>'.esc_html__($title, 'cross-selling').'</h4>';*/

		$html .= '<ul class="site-list product-form__bundles site-list--normalize" aria-orientation="vertical" aria-label="Bundle and save">';

		foreach ($_cross_sells_products_ids as $key => $product_id) 
		{
			$product = wc_get_product( $product_id );
			$price = $product->get_price_html();

			$thePrice = $product->get_price();

			if ( $product->is_type( 'variable' ) ) 
			{

				/* Get Variation */
				$args = array(
				    'post_type'     => 'product_variation',
				    'post_status'   => array( 'private', 'publish' ),
				    'numberposts'   => -1,
				    'orderby'       => 'menu_order',
				    'order'         => 'asc',
				    'post_parent'   => $product_id // get parent post-ID
				);
				$variations = get_posts( $args );

				$variation_select .= '<select id="color_c" name="variation_select_name_'.$product_id.'" class="" onchange="cross_sell_vari(this)">';
				$variation_select .= '<option value="">Wähle eine Option</option>';

				foreach ( $variations as $variation ) 
				{

				    // get variation ID
				    $variation_ID = $variation->ID;

				    // get variations meta
				    $product_variation = new WC_Product_Variation( $variation_ID );

				    // get variation featured image
				    $variation_image = $product_variation->get_image();

				    // get variation price
				    $variation_price = $product_variation->get_price_html();
				        	
				    foreach (wc_get_product($variation_ID)->get_variation_attributes() as $attr) 
			        {
						$variation_select .= '<option value="'.$variation_ID.'">'.wc_attribute_label( $attr ).'</option>';
			        }


				}
				$variation_select .= '</select>';
			}


			$html .= '<li class="site-list__item" onclick="list__item(this)">';
			$html .= '<input type="hidden" class="put-product-id" name="products_id[]">';
				$html .= '<div class="site-line-item site-line-item--bundle site-line-item--border-around" title="'.$product->get_title().'" product-id="'.$product_id.'">';
					$html .= '<div class="site-line-item__container d-flex">';

						$html .= '<div class="site-line-item__image col-3">';
							$html .= '<div class="site-image__wrapper site-image__wrapper--fixed-width">';
							$html .= '<img class="site-image lazyautosizes lazyloaded" src="'.wp_get_attachment_url( $product->get_image_id(), '' ).'">';
							$html .= '</div>';
						$html .= '</div>';

						$html .= '<div class="site-line-item__information d-flex flex-column justify-content-between col-9 d-flex justify-content-between">';
							$html .= '<div class="site-line-item__header">';
								$html .= '<div class="site-line-item__title-container d-flex align-items-center justify-content-between">';
									$html .= '<label for="bundle-product" class="site-line-item__title site-font site-font__size--small site-font--weight-medium">'.$product->get_title().'</label>';
									$html .= '<div class="bundle-field__container" id="bundle-field-checkbox">';
										$html .= '<input type="checkbox" class="bundle-field" id="bundle-product" name="bundle-product" value="" data-price="'.$thePrice.'" aria-label="">';
									$html .= '</div>';
								$html .= '</div>';
								$html .= '<div class="site-line-item__discount site-font site-font__size--small">'.$product->get_short_description().'</div>';
								$html .= '<div>';
								if ( $product->is_type( 'variable' ) ) 
								{

									$html .= $variation_select;
								}

								            
								$html .= '</div>';
							$html .= '</div>';
							$html .= '<div class="site-line-item__footer">';
								$html .= '<div class="site-line-item__footer-container d-flex align-items-center justify-content-between">';
									$html .= '<a href="'.$product->get_permalink().'" class="site-link site-font site-font__size--small" role="link" title="View Product">'.__('View Product', 'cross-selling').'</a>';
									$html .= '<div class="product-price__container d-flex align-items-center">';
										$html .= '<span class="product-price d-flex align-items-center">';
											$html .= '<span class="product-price__text site-font site-font__size--small">'.$price;
											$html .= '</span>';
										$html .= '</span>';
									$html .= '</div>';
								$html .= '</div>';
							$html .= '</div>';
						$html .= '</div>';

					$html .= '</div>';
				$html .= '</div>';
			$html .= '</li>';
		

		}	

		$html .= '</ul>';
		$html .= '</div>';



	}
	
    wp_die($html);

}



/* Create Shortcode */
function cross_selling_shortcode_fun() 
{
	echo '<div id="more_selling_product"></div>';
}

add_shortcode( 'cross_selling_shortcode', 'cross_selling_shortcode_fun' );


add_action( 'woocommerce_save_product_variation', 'bbloomer_save_custom_field_variations', 10, 2 );
function bbloomer_save_custom_field_variations( $variation_id, $i ) {
$custom_field = $_POST['custom_field'][$i];
if ( isset( $custom_field ) ) update_post_meta( $variation_id, 'custom_field', esc_attr( $custom_field ) );
}


add_action('wp_footer', 'cross_sell_vari_func');
function cross_sell_vari_func()
{
?>
	<script type="text/javascript">
		function cross_sell_vari(elem)
		{

			var variation_id =  elem.value;
	        jQuery.ajax({
			    type:"POST",
			    dataType: 'JSON',
			    url:"<?php echo admin_url('admin-ajax.php'); ?>",
			    data: {action:'my_special_ajax_call_for_more_variation',variation_id:variation_id},
			    success:function(res)
			    {
			    	if (res.price != '') 
			    	{
				    	jQuery(elem).parent('div').parent('div').parent('div').parent('div').find('.site-image__wrapper.site-image__wrapper--fixed-width').html(res.image);

				    	jQuery(elem).parent('div').parent('div').parent('div').parent('div').children('.site-line-item__information').children('.site-line-item__footer').find('.product-price__text').html(res.price);
				    	
				    	jQuery(elem).parent('div').parent('div').find('#bundle-field-checkbox').children('#bundle-product').attr('data-price',res.get_price);

				    	// jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').parent('.site-list__item').children('.put-product-id').val(variation_id);
			    	}
			    	else
			    	{
			    		jQuery('.variations_form').trigger('check_variations');

			    	}

			    }
			});
		}
	</script>

<?php	
}


add_action('wp_ajax_my_special_ajax_call_for_more_variation', 'child_scross_sell');
add_action('wp_ajax_nopriv_my_special_ajax_call_for_more_variation', 'child_scross_sell');
function child_scross_sell()
{

	$variation_ID = $_POST['variation_id'];
	// get variations meta
    $product_variation = new WC_Product_Variation( $variation_ID );

	// get variation featured image
    $variation_image = $product_variation->get_image();
    $variation_p_id = $product_variation->get_id();

    // get variation price
    $variation_price = $product_variation->get_price_html();

	$get_price = $product_variation->get_price();



    echo json_encode(array('success' => true , 'image' => $variation_image, 'price' => $variation_price, 'get_price' => $get_price));

    wp_die();

}
