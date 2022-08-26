<?php
/**
 * Plugin Name:       theopenbook
 * Plugin URI:        https://github.com/stoosepp/theopenbook
 * Description:       Open Textbook Plugin - provides back-end functionality - requires theopenbook theme to display everything.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Stoo Sepp
 * Author URI:        https://www.stoosepp.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       my-basics-plugin
 * Domain Path:       /languages
 */

define( 'MY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );

include_once(MY_PLUGIN_PATH.'bookfunctions/customposttaxonomy.php');
include_once(MY_PLUGIN_PATH.'bookfunctions/adminfilter.php');

// function pluginprefix_setup_book_post_type() {
//     //CAll functions required to set up post type
//     create_book_type();
//     add_custom_taxonomies();
//     set_theopenbook_settings();
// }
// add_action( 'init', 'pluginprefix_setup_book_post_type' );

// function pluginprefix_setup_book_admin() {
//     //CAll functions required to set up post type
//     // addBookMetaBox();
//     // licenseSelectMetaBoxCreator();
//     // saveMeta();
// }
// add_action( 'init', 'pluginprefix_setup_book_admin' );




/**
 * Activate the plugin.
 */
function run_at_activation(){
    //Functions to run at deactivation
    create_book_type();
    add_custom_taxonomies();
    set_theopenbook_settings();
  }
  register_activation_hook( __FILE__, 'run_at_activation' );

/**
 * Deactivation hook.
 */

function run_at_deactivation(){
    //Functions to run at deactivation
  }
  register_deactivation_hook( __FILE__, 'run_at_deactivation' );
?>