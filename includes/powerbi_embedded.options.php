<?php
/**
 * @file
 * Create a settings form for powerbi_embedded plugin.
 */

// Register the create menu action.
add_action('admin_menu', 'powerbi_embedded_create_menu');

/**
 * Create the powerbi embedded settings menu
 *
 * @see add_action('admin_menu', 'powerbi_embedded_create_menu')
 */
function powerbi_embedded_create_menu() {

  //create new top-level menu
  add_menu_page('Powerbi Embedded Settings', 'Powerbi Embedded Settings', 'administrator', __FILE__, 'powerbi_embedded_settings_page');

  //call register settings function
  add_action( 'admin_init', 'register_powerbi_embedded_settings' );
}

/**
 * Register powerbi embedded settings.
 *
 * @see powerbi_embedded_create_menu() | add_menu_page()
 */
function register_powerbi_embedded_settings() {
  // register settings
  register_setting( 'powerbi-embedded-settings-group', 'powerbi_embedded_workspace_collection_name' );
  register_setting( 'powerbi-embedded-settings-group', 'powerbi_embedded_workspace_id' );
  register_setting( 'powerbi-embedded-settings-group', 'powerbi_embedded_app_key_1' );
  register_setting( 'powerbi-embedded-settings-group', 'powerbi_embedded_app_key_2' );
}

/**
 * Create output for the powerbi settings page.
 *
 * @see powerbi_embedded_create_menu() | add_action()
 */
function powerbi_embedded_settings_page() {
  print powerbi_embedded_get_template('options-form', array());
}
