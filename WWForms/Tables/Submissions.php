<?php

namespace WWForms\Tables;

class Submissions {
  private static function create_submissions_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'submissions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      form_id mediumint(9) NOT NULL,
      submission_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
      submission_data longtext NOT NULL,
      PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
  }

  public static function setup() {
    Submissions::create_submissions_table();
  }
}