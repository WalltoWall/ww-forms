<?php

namespace WWForms;

class Helpers {
  public static function get_ww_template($slug, $name = null, $args = array()) {
    $templates = array();
    $name = (string) $name;
    if ('' !== $name) {
      $templates[] = "{$slug}-{$name}.php";
    }

    $templates[] = "{$slug}.php";

    if ( !self::locate_template( $templates, true, false, $args ) ) {
      return false;
    }
  }

  private static function locate_template($template_names, $load = false, $load_once = true, $args = array()) {
    $located = '';
    foreach ( (array) $template_names as $template_name ):
      if (!$template_name):
        continue;
      endif;

      if (file_exists(plugin_dir_path(__FILE__) . 'templates/' . $template_name)):
        $located = plugin_dir_path(__FILE__) . 'templates/' . $template_name;
        break;
      endif;
    endforeach;

    if ($load && '' !== $located):
      load_template($located, $load_once, $args);
    endif;

    return $located;
  }

  public static function get_json($filename) {
    $json = plugin_dir_path( __FILE__ ) . 'json/' . $filename;
		$json_data = file_get_contents($json);
		$results = json_decode($json_data, true);

    return $results;
  }

  public static function get_manifest() {
    $manifest_path = plugin_dir_path(__FILE__) . 'assets/.vite/manifest.json';

    if (! file_exists($manifest_path)) {
        return;
    }

    $manifest = json_decode(file_get_contents($manifest_path), true);

    return $manifest;
  }
}