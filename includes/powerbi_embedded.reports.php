<?php

use Firebase\JWT\JWT;

/**
 * @file
 * Define the powerbi embedded dashboard report page.
 */

// Filters
add_filter('single_template', 'powerbi_embedded_report_template');

/**
 * Define the powerbi embedded dashboard page template
 */
function powerbi_embedded_report_template( $page_template ) {
  if (powerbi_is_report()) {
    $page_template = POWERBI_EMBEDDED__TEMPLATE_DIR . 'single-report.tpl.php';
  }
  return $page_template;
}

// Actions
add_action('template_redirect', 'powerbi_embedded_check_permissions');
add_action('the_content', 'powerbi_embedded_report_content');
add_action('wp_enqueue_scripts', 'powerbi_embedded_enqueue_resources');

/**
 * Callback for hook wp.
 *
 * Checks permissions for users on powerbi report pages and redirects to
 * login if !available.
 */
function powerbi_embedded_check_permissions() {
  if (powerbi_is_report()) {
    if (!current_user_can('view_powerbi_embedded_reports')) {
      wp_redirect(home_url('wp-login.php'), 303);
      exit();
    }
  }
}

/**
 * Add embedded report content if this is a report page.
 */
function powerbi_embedded_report_content($content) {
  if (powerbi_is_report()) {
    $content .= _powerbi_embedded_report_content();
  }
  return $content;
}

/**
 * Callback for hook wp_enqueue_scripts.
 *
 * Enqueues custom powerbi vendor scripts, and styling/js for report pages.
 */
function powerbi_embedded_enqueue_resources() {
  if (powerbi_is_report()) {
    // Enqueue scripts.
    wp_enqueue_script('powerbi', POWERBI_EMBEDDED__PLUGIN_URL . 'vendor/powerbi/powerbi.js', array(), FALSE, TRUE);
    wp_enqueue_script('powerbi_embedded', POWERBI_EMBEDDED__PLUGIN_URL . 'js/powerbi_embedded.js', array(), FALSE, TRUE);
    // Enqueue styles
    wp_enqueue_style('powerbi_embedded', POWERBI_EMBEDDED__PLUGIN_URL . 'styles/powerbi_embedded.css');
  }
}
