<?php

namespace WWForms\Tables;

use WWForms\API\EmailAPI;

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

  public static function insert_submission($form_id, $submission_data) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'submissions';

    $wpdb->insert(
      $table_name,
      array(
        'form_id' => $form_id,
        'submission_date' => current_time('mysql'),
        'submission_data' => json_encode($submission_data)
      )
    );

    EmailAPI::send_email($form_id, $submission_data);
  }

  public static function get_submissions($form_id, $offset = 0, $limit = 10, $sort = 'DESC') {
    global $wpdb;

    $table_name = $wpdb->prefix . 'submissions';

    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE form_id = $form_id ORDER BY submission_date $sort LIMIT $limit OFFSET $offset");

    return $results;
  }

  public static function get_all_submissions($form_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'submissions';

    $results = $wpdb->get_results("SELECT * FROM $table_name WHERE form_id = $form_id ORDER BY submission_date DESC");

    return $results;
  }

  public static function get_submissions_count($form_id) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'submissions';

    $total = $wpdb->get_var("SELECT COUNT(*) FROM $table_name WHERE form_id = $form_id");

    return $total;
  }

  public static function delete_submissions($submission_ids) {
    global $wpdb;

    $table_name = $wpdb->prefix . 'submissions';

    $wpdb->query("DELETE FROM $table_name WHERE id IN ($submission_ids)");
  }

  public static function setup() {
    Submissions::create_submissions_table();
  }
}