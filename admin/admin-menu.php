<?php
/* Override Admin menu icon */
function replace_admin_menu_icons_css() {
    ?>
    <style>
       .toplevel_page_cross-selling .dashicons-admin-generic:before {
    		content: "\f103" !important;
		}
    </style>
    <?php
}
add_action( 'admin_head', 'replace_admin_menu_icons_css' );


/* Admin menu Add */
add_action('admin_menu', 'cross_selling_plugin_setup_menu');
 
function cross_selling_plugin_setup_menu(){
    add_menu_page( 'Cross Selling', 'Cross Selling', 'manage_options', 'cross-selling', 'display_shortcode' );
}
 
function display_shortcode()
{
   echo "<h1>Cross Selling Product ShortCode</h1>";
   echo "<br>";
   echo "<h2>Cross Selling Shortcode (HTML OR PHP):  <code>['cross_selling_shortcode'] OR  do_shortcode('[cross_selling_shortcode]'); </code></h2>";
}
 
