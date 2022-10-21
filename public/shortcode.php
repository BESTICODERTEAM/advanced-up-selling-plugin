<?php

/*

=========================== Old Code ====================

add_action( 'woocommerce_after_add_to_cart_button', 'woocommerce_before_add_to_cart_button_data_show' );
function woocommerce_before_add_to_cart_button_data_show()
{
	$productID = get_the_ID();

	$_cross_sells_products_ids = get_post_meta( $productID, '_cross_sells_products_ids', true );

	if (!empty($_cross_sells_products_ids)) 
	{


	?>
		<div class="cross-selling-wrapper">
			<h4><?php echo  _e( 'Buy at once', 'cross-selling' ); ?></h3>
			<ul class="site-list product-form__bundles site-list--normalize" aria-orientation="vertical" aria-label="Bundle and save">
			<?php

				foreach ($_cross_sells_products_ids as $id_key => $products_id) 
				{
					$id_key++;
					$product = wc_get_product( $products_id );
					$price = $product->get_price_html();

					$thePrice = $product->get_price();
					
				?>

				<li class="site-list__item">
					<input type="hidden" class="put-product-id" name="products_id[]">
					<div class="site-line-item site-line-item--bundle site-line-item--border-around" title="<?php echo $product->get_title(); ?>" product-id="<?php echo $products_id; ?>">
						<div class="site-line-item__container d-flex">
							<div class="site-line-item__image col-3">
								<div class="site-image__wrapper site-image__wrapper--fixed-width"><img class="site-image lazyautosizes lazyloaded" src="<?php echo wp_get_attachment_url( $product->get_image_id(), '' ); ?>">
								</div>
							</div>
							<div class="site-line-item__information d-flex flex-column justify-content-between col-9 d-flex justify-content-between">
								<div class="site-line-item__header">
									<div class="site-line-item__title-container d-flex align-items-center justify-content-between">
										<label for="bundle-product-<?php echo $id_key; ?>" class="site-line-item__title site-font site-font__size--small site-font--weight-medium"><?php echo $product->get_title(); ?></label>
										<div class="bundle-field__container">
											<input type="checkbox" class="bundle-field" id="bundle-product-<?php echo $id_key; ?>" name="bundle-product-<?php echo $id_key; ?>" value="39591188103299" data-price="<?php echo $thePrice; ?>" aria-label="<?php echo $product->get_title(); ?>">
										</div>
									</div>
									<div class="site-line-item__discount site-font site-font__size--small">Save 20% or more.</div>
								</div>
								<div class="site-line-item__footer">
									<div class="site-line-item__footer-container d-flex align-items-center justify-content-between">
										<a href="<?php echo $product->get_permalink(); ?>" class="site-link site-font site-font__size--small" role="link" title="View Product"><?php echo  _e( 'View Product', 'cross-selling' ); ?></a>
										<div class="product-price__container d-flex align-items-center">
											<span class="product-price d-flex align-items-center">
												<span class="product-price__text site-font site-font__size--small">
													<?php echo $price; ?>
												</span>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</li>
					
				<?php
				}

			?>
			</ul>
		</div>
	<?php	
	}

}
*/


add_action('wp_footer', 'custom_call_ajax_onload'); 
function custom_call_ajax_onload() 
{ 
	if ( is_product() ){
	global $product;
	$id = $product->get_id();
?>

	<script type="text/javascript">
		var product_id =  '<?php echo $id; ?>';
	          jQuery.ajax({
			    type:"POST",
			    url:"<?php echo admin_url('admin-ajax.php'); ?>",
			    data: {action:'my_special_ajax_call_for_variation',product_id:product_id},
			    success:function(res)
			    {
			    	jQuery('#more_selling_product').html(res);

			    }
			});

	</script>

<?php
	}
}


/*add_action( 'woocommerce_before_single_variation', 'action_wc_before_single_variation' );
function action_wc_before_single_variation() 
{
    ?>
    <script type="text/javascript">
    (function($){

    	console.log('hi');

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
}*/




add_action('wp_ajax_my_special_ajax_call_for_variation', 'scross_sell');
add_action('wp_ajax_nopriv_my_special_ajax_call_for_variation', 'scross_sell');
function scross_sell()
{ 
	$product_id = $_POST['product_id'];
	$_cross_sells_products_ids = get_post_meta( $product_id, '_cross_sells_products_ids', true );

	$disabled = '';
	$html = '';
	$variation_select = '';
	if (!empty($_cross_sells_products_ids)) 
	{


		$html .= '<div class="cross-selling-wrapper">';
		$html .= '<form action="" method="post">';
		$html .= '<h4>'.esc_html__('Buy at once', 'cross-selling').'</h3>';

		$html .= '<ul class="site-list product-form__bundles site-list--normalize" aria-orientation="vertical" aria-label="Bundle and save">';

		foreach ($_cross_sells_products_ids as $key => $product_id) 
		{
			$product = wc_get_product( $product_id );
			$price = $product->get_price_html();

			$thePrice = $product->get_price();

			if ( $product->is_type( 'variable' ) ) 
			{
				$disabled = 'disabled_pointer_events';

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
				$variation_select .= '<div class="error_msg">'.esc_html__('Please select variant.', 'cross-selling').'</div>';
			}


			$html .= '<li class="site-list__item">';
			/*$html .= '<input type="hidden" class="put-product-id" name="products_id[]">';*/
				$html .= '<div class="site-line-item site-line-item--bundle site-line-item--border-around" title="'.$product->get_title().'" product-id="'.$product_id.'" default-id="'.$product_id.'">';
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
									$html .= '<div class="bundle-field__container '.$disabled.'" id="bundle-field-checkbox" onclick="list__item(this)">';
										$html .= '<input type="checkbox" class="bundle-field" id="bundle-product" name="bundle-product" value="" data-price="'.$thePrice.'" aria-label=""  >';
									$html .= '</div>';
								$html .= '</div>';
								$html .= '<div class="site-line-item__discount site-font site-font__size--small">'.$product->get_short_description().'</div>';
								$html .= '<div class="variation_sele">';
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
		$html .= '</form>';
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



add_action('wp_footer', 'cross_sell_vari_func');
function cross_sell_vari_func()
{
?>
	<script type="text/javascript">
		function cross_sell_vari(elem)
		{

			var variation_id =  elem.value;

			var product_id = jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').parent('.site-list__item').children('div').attr('default-id');


	        jQuery.ajax({
			    type:"POST",
			    dataType: 'JSON',
			    url:"<?php echo admin_url('admin-ajax.php'); ?>",
			    data: {action:'my_special_ajax_call_for_more_variation',variation_id:variation_id,product_id:product_id},
			    success:function(res)
			    {
			    	if (res.get_price != null) 
			    	{
				    	jQuery(elem).parent('div').parent('div').parent('div').parent('div').find('.site-image__wrapper.site-image__wrapper--fixed-width').html(res.image);

				    	jQuery(elem).parent('div').parent('div').parent('div').parent('div').children('.site-line-item__information').children('.site-line-item__footer').find('.product-price__text').html(res.price);
				    	
				    	jQuery(elem).parent('div').parent('div').find('#bundle-field-checkbox').children('#bundle-product').attr('data-price',res.get_price);

				    	jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').parent('.site-list__item').children('div').attr('product-id',variation_id);

				    	jQuery(elem).parent('div').parent('div').find('.bundle-field__container').removeClass('disabled_pointer_events');

						jQuery('.error_msg').hide();


			    	}
			    	else
			    	{

			    		jQuery(elem).parent('div').parent('div').parent('div').parent('div').find('.site-image__wrapper.site-image__wrapper--fixed-width').html(res.image);

			    		jQuery(elem).parent('div').parent('div').parent('div').parent('div').children('.site-line-item__information').children('.site-line-item__footer').find('.product-price__text').html(res.price);

				    	jQuery(elem).parent('div').parent('div').find('.bundle-field__container').addClass('disabled_pointer_events');

				    	jQuery(elem).parent('div').parent('div').find('.bundle-field__container').find('.bundle-field').prop('checked',false);

				    	jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').removeClass('site-line-item--active');

				    	var item_id = jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').attr('product-id');

				    	jQuery('#put-product-id_'+item_id).remove();

						jQuery('.error_msg').show();


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
	$product_id = $_POST['product_id'];

	if (!empty($variation_ID)) 
	{
		// get variations meta
	    $product_variation = new WC_Product_Variation( $variation_ID );

		// get variation featured image
	    $image = $product_variation->get_image();
	    $variation_p_id = $product_variation->get_id();

	    // get variation price
	    $price = $product_variation->get_price_html();

		$get_price = $product_variation->get_price();
	}
	else
	{
		$product = wc_get_product( $product_id );
		$image = $product->get_image();
		$price = $product->get_price_html();
		$get_price = null;
	}

    echo json_encode(array('success' => true , 'image' => $image, 'price' => $price, 'get_price' => $get_price));

    wp_die();

}
