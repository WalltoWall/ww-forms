<?php

namespace WWForms;

use WWForms\API\SubmissionsAPI;
use WWForms\Helpers;

class Actions {
  private static function submissions_markup() {
    Helpers::get_ww_template("submissions");
  }

  private static function add_submissions_meta_box() {
     add_meta_box("submissions", "Form Submissions", function () {
         Actions::submissions_markup();
     }, "form", "advanced", "high", null);
  }

  private static function add_form_shortcode($attrs) {
    $attributes = shortcode_atts( array(
      'id' => ''
    ), $attrs );

    ob_start();
    Helpers::get_ww_template("form", null, $attributes);

    return ob_get_clean();
  }

  public static function setup() {
    add_action("add_meta_boxes", function () {
      Actions::add_submissions_meta_box();
    });

    // adds google recaptcha js to the head
    add_action('wp_head', function () {
      $recaptcha = get_field('google_recaptcha', 'option');
      if ($recaptcha):
          $siteKey = $recaptcha['site_key'];
          echo $siteKey ? '<script src="https://www.google.com/recaptcha/api.js?render='.$siteKey.'"></script>' : '';
      endif;
    });

    // add form shortcode
    add_shortcode('ww_form', function ($attrs) {
      return Actions::add_form_shortcode($attrs);
    });

    // load scripts and styles
    add_action('wp_enqueue_scripts', function () {
      $manifest = Helpers::get_manifest();
      wp_enqueue_script_module(
          id: 'ww-forms',
          src: plugin_dir_url(__FILE__) . 'assets/' . $manifest['resources/js/index.ts']['file']
      );
      wp_enqueue_style(
          handle: 'ww-forms',
          src: plugin_dir_url(__FILE__) . 'assets/' . $manifest['resources/css/index.css']['file']
      );
    });

    add_action('admin_enqueue_scripts', function () {
      $manifest = Helpers::get_manifest();
      wp_enqueue_script_module(
        id: 'ww-forms',
        src: plugin_dir_url(__FILE__) . 'assets/' . $manifest['resources/js/admin/index.ts']['file']
      );
      wp_enqueue_style(
        handle: 'ww-forms',
        src: plugin_dir_url(__FILE__) . 'assets/' . $manifest['resources/css/admin/index.css']['file']
      );
    });

    // setup ajax actions
    add_action('wp_ajax_load_submissions', function () {
      SubmissionsAPI::load_submissions();
    });
    add_action('wp_ajax_get_submissions_csv', function () {
      SubmissionsAPI::get_submissions_csv();
    });
  }
}