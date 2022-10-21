/* Simple Product Js */
/*jQuery('.site-list__item').on('click',function()
{

	if (jQuery(this).find('.bundle-field').is(':checked')) 
	{
		jQuery(this).find('.bundle-field').prop('checked',false);
		jQuery(this).children('div').removeClass('site-line-item--active');
		jQuery(this).find('.put-product-id').val('');

	}
	else
	{

		jQuery(this).find('.bundle-field').prop('checked',true);
		jQuery(this).children('div').addClass('site-line-item--active');
		
		var item_id = jQuery(this).find('.site-line-item').attr('product-id');
		jQuery(this).find('.put-product-id').val(item_id);


	}
				
});*/

/* Variation Product Js */
function list__item(elem)
{
	var check_variant = jQuery(elem).parent('div').parent('div').find('.variation_sele').children('select').val();

	if (check_variant == '') 
	{
		jQuery('.error_msg').show();
		return false;
	}
	

	if (jQuery(elem).find('.bundle-field').is(':checked')) 
	{


		jQuery(elem).find('.bundle-field').prop('checked',false);

		jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').removeClass('site-line-item--active');

		var item_id = jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').attr('product-id');

		jQuery('.woocommerce-variation-add-to-cart #put-product-id_'+item_id).remove();

	}
	else
	{

		jQuery(elem).find('.bundle-field').prop('checked',true);
		jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').addClass('site-line-item--active');
		
		var item_id = jQuery(elem).parent('div').parent('div').parent('div').parent('div').parent('div').attr('product-id');
		jQuery(elem).find('.put-product-id').val(item_id);

		jQuery('.woocommerce-variation-add-to-cart').append('<input type="hidden" class="put-product-id" id="put-product-id_'+item_id+'" value="'+item_id+'" name="products_id[]">');


	}
			
}


/* clear variations empty cross selling item */
/*jQuery('.reset_variations').on('click',function(){

	jQuery('#more_selling_product').html('');

});
*/

