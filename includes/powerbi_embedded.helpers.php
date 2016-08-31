<?php
/**
 * @file
 * Helper functions for the powerbi embedded plugin.
 */

/**
 * Render a template with given variables.
 *
 * @param $template
 * @param array $variables
 * @return string
 */
function powerbi_embedded_get_template($template, $variables = array()) {
  extract($variables, EXTR_SKIP);
  ob_start();

  include POWERBI_EMBEDDED__TEMPLATE_DIR . $template . '.tpl.php';

  return ob_get_clean();
}

/**
 * Check that this is as report.
 */
function powerbi_is_report() {
  global $post;
  return $post->post_type === 'powerbi_reports';
}

function _powerbi_embedded_create_nonce() {
  return wp_create_nonce( plugin_basename(__FILE__) );
}

function _powerbi_embedded_verify_nonce($value) {
  return wp_verify_nonce( $value, plugin_basename(__FILE__));
}

/**
 * Custom field actions.
 *
 * @param $post_id
 * @param $post
 * @param $value
 * @param $key
 */
function powerbi_embedded_custom_field_actions($post_id, $post, $value, $key) {
  if( $post->post_type == 'revision' ) return; // Don't store custom data twice
  $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
  if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
    update_post_meta($post->ID, $key, $value);
  } else { // If the custom field doesn't have a value
    add_post_meta($post->ID, $key, $value);
  }
  if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
}

/**
 * Custom callback to create embeddable content in a report.
 *
 * @return string
 */
function _powerbi_embedded_report_content() {
  global $post;

  $powerbi = powerbi_embedded_new_powerbi_instance();
  $report_id = get_post_meta($post->ID, 'powerbi_embedded_report_id', TRUE);
  $report = $powerbi->getReport($report_id);
  $embed_token = $powerbi->getEmbedToken($report_id);

  $variables = array();
  $variables['report_id'] = $report['id'];
  $variables['report_url'] = $report['embedUrl'];
  $variables['access_token'] = $embed_token;
  return powerbi_embedded_get_template('_report', $variables);
}

/**
 * Create a new powerbi instance
 */
function powerbi_embedded_new_powerbi_instance() {
  $workspace = get_option('powerbi_embedded_workspace_id');
  $workspaceCollection = get_option('powerbi_embedded_workspace_collection_name');
  $appTokens[] = get_option('powerbi_embedded_app_key_1');
  $appTokens[] = get_option('powerbi_embedded_app_key_2');
  return new Powerbi($appTokens, $workspaceCollection, $workspace);
}