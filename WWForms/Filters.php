<?php

namespace WWForms;

class Filters {
  private static function add_shortcode_column($columns) {
    $columns['shortcode'] = 'Shortcode';

    return $columns;
  }

  public static function setup() {
    add_filter( 'manage_form_posts_columns', function ($columns) {
      return self::add_shortcode_column($columns);
    } );
  }
}