<?php
/*
 Plugin Name: Powerbi Embedded
 Description: Integrate Powerbi Embedded with your wp site.
 Author: Jesse Day
 Version: 0.0.1
 Author URI: http://jesseday.github.io
*/

define( 'POWERBI_EMBEDDED__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'POWERBI_EMBEDDED__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'POWERBI_EMBEDDED__TEMPLATE_DIR', POWERBI_EMBEDDED__PLUGIN_DIR . 'templates/' );

// Vendor includes
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'vendor/jwt/src/BeforeValidException.php');
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'vendor/jwt/src/ExpiredException.php');
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'vendor/jwt/src/SignatureInvalidException.php');
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'vendor/jwt/src/JWT.php');

// Powerbi Embedded includes
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'includes/powerbi.inc');
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'includes/powerbi_embedded.helpers.php');
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'includes/powerbi_embedded.options.php');
include_once(POWERBI_EMBEDDED__PLUGIN_DIR . 'includes/powerbi_embedded.reports.php');

// Actions
add_action('init', 'powerbi_embedded_register_menus');
add_action('init', 'powerbi_embedded_create_post_type' );
add_action('admin_init', 'powerbi_embedded_reports_add_custom_fields');
add_action('save_post', 'powerbi_embedded_save_report_meta', 1, 2); // save the custom fields

/**
 * Create a custom menu for navigating reports.
 */
function powerbi_embedded_register_menus() {
  register_nav_menu('powerbi-dashboard-menu',__( 'Powerbi Dashboard Menu' ));
}


/**
 * Create a custom post type for powerbi embedded reports.
 */
function powerbi_embedded_create_post_type() {

  // UI labels
  $labels = array(
    'name'                => _x( 'Powerbi Reports', 'Post Type General Name' ),
    'singular_name'       => _x( 'Powerbi Report', 'Post Type Singular Name' ),
    'menu_name'           => __( 'Powerbi Reports' ),
    'parent_item_colon'   => __( 'Parent Report' ),
    'all_items'           => __( 'All Powerbi Reports' ),
    'view_item'           => __( 'View Powerbi Report' ),
    'add_new_item'        => __( 'Add New Powerbi Report' ),
    'add_new'             => __( 'Add New' ),
    'edit_item'           => __( 'Edit Powerbi Report' ),
    'update_item'         => __( 'Update Powerbi Report' ),
    'search_items'        => __( 'Search Powerbi Report' ),
    'not_found'           => __( 'Not Found' ),
    'not_found_in_trash'  => __( 'Not found in Trash' ),
  );

  // Other options

  $args = array(
    'label'               => __( 'Powerbi Reports'),
    'description'         => __( 'Embeddable reports from powerbi'),
    'labels'              => $labels,
    'supports'            => array( 'title', 'revisions' ),
    'hierarchical'        => false,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'can_export'          => false,
    'has_archive'         => false,
    'exclude_from_search' => true,
    'publicly_queryable'  => true,
    'capability_type'     => 'page',
    'rewrite' => array('slug' => 'report'),
  );

  register_post_type( 'powerbi_reports', $args );
}

/**
 * Add custom fields to powerbi embedded reports post type.
 */
function powerbi_embedded_reports_add_custom_fields() {
//  add_meta_box('powerbi_embedded_workspace', 'Workspace Id', 'powerbi_embedded_get_workspaces', 'powerbi_reports', 'normal', 'high');
  add_meta_box('powerbi_embedded_report', 'Report Information', 'powerbi_embedded_report_metabox', 'powerbi_reports', 'normal', 'high');
}

/**
 * Add custom metabox for report information to the powerbi_report post type.
 */
function powerbi_embedded_report_metabox() {
  global $post;

  $variables = array();
  $variables['metabox_noncename'] = 'powerbi_embedded_report_info';
  $powerbi = powerbi_embedded_new_powerbi_instance();
  $reports = $powerbi->getReports();
  $selected = get_post_meta($post->ID, 'powerbi_embedded_report_id', true);
  $options = array_map(function($item) use ($selected) {
    if ($selected == $item['id']) {
      return '<option value="' . $item['id'] . '" selected="selected">' . $item['name'] . '</option>';
    }
    else {
      return '<option value="' . $item['id'] . '">' . $item['name'] . '</option>';
    }
  }, $reports);
  $options = implode('', $options);
  $variables['fields'] = array();
  $variables['fields'][] = <<<EOD
<select id="powerbi_embedded_report_id" name="powerbi_embedded_report_id">
$options
</select>
EOD;
  echo powerbi_embedded_get_template('_metabox', $variables);
}

/**
 * Save report meta.
 *
 * @param $post_id
 * @param $post
 */
function powerbi_embedded_save_report_meta($post_id, $post) {

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times
  if ( !current_user_can( 'edit_post', $post->ID )) {
    return $post->ID;
  }
  // Run custom field save handlers.
  powerbi_embedded_save_report_information($post_id, $post);

}

/**
 * Custom save handler for the report_id custom field.
 *
 * @param $post_id
 * @param $post
 */
function powerbi_embedded_save_report_information($post_id, $post) {
  if ( !_powerbi_embedded_verify_nonce($_POST['powerbi_embedded_report_info_nonce'])) {
    return $post->ID;
  }

  powerbi_embedded_custom_field_actions($post_id, $post, $_POST['powerbi_embedded_report_id'], 'powerbi_embedded_report_id');
}
