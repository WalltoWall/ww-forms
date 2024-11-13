<?php

namespace WWForms\API;

use WWForms\Tables\Submissions;
use DateTime;
use DateTimeZone;

class SubmissionsAPI {
  public static function load_submissions() {
    // variables from javascript
    $form_id = $_POST['formId'];
    $offset = $_POST['offset'] ? (int) $_POST['offset'] : 0;
    $limit = $_POST['limit'] ? (int) $_POST['limit'] : 10;
    $sort = $_POST['sort'] ? strtoupper($_POST['sort']) : 'DESC';

    // when init is true, fields list and total submissions count are returned
    $init = $_POST['init'] ? $_POST['init'] : 'false';

    $results = Submissions::get_submissions($form_id, $offset, $limit, $sort);

    // format submission results
    $results = array_map(function ($result) {
      $submission_data['data'] = json_decode($result->submission_data, true);
      $submission_data['id'] = $result->id;
      $submission_data['submission_date'] = $result->submission_date;

      return $submission_data;
    }, $results);

    if ($init == 'true'):
      $fields = FieldsAPI::get_fields($form_id);

      $total = Submissions::get_submissions_count($form_id);

      $data = array(
          'results' => $results,
          'fields' => $fields,
          'total' => $total
      );
    else:
        $data = array(
            'results' => $results
        );
    endif;

    echo json_encode($data);

    exit();
  }

  public static function remove_submissions() {
    // format array from javascript
    $ids = $_POST['submissionIds'];
    $cleaned_ids = str_replace("\\", "", $ids);
    $submission_ids = implode(', ', json_decode($cleaned_ids));

    Submissions::delete_submissions($submission_ids);

    echo json_encode(array('success' => true));

    exit();
  }

  public static function get_submissions_csv() {
    $form_id = $_GET['formId'];
    $slug = get_post_field('post_name', $form_id);

    $fields = FieldsAPI::get_fields($form_id);
    $headers = FieldsAPI::get_field_labels($form_id, true);

    $results = Submissions::get_all_submissions($form_id);

    $output = fopen('php://output', 'w');

    $timezone = get_option('timezone_string') ? get_option('timezone_string') : 'UTC';
    $report_date = new DateTime("now", new DateTimeZone($timezone));
    $formatted_report_date = $report_date->format('Y-m-d');

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$slug.'-report-'.$formatted_report_date.'.csv";' );
    header('Content-Transfer-Encoding: binary');

    fputcsv($output, $headers);

    foreach ($results as $result):
      $submission_data = json_decode($result->submission_data, true);

      $result_date = new DateTime($result->submission_date);
      $result_date->setTimezone(new DateTimeZone($timezone));
      $submission = [$result_date->format('n/j/Y, g:ia')];

      foreach ($fields as $field):
        if ($field['type'] == 'multiple_choice'):
          $values = [];
          foreach (array_keys($submission_data) as $key):
            if (str_contains($key, $field['name'])):
              $values[] = $submission_data[$key];
            endif;
          endforeach;
          $submission[] = implode(', ', $values);
        elseif ($field['type'] == 'checkbox'):
          $submission[] = $submission_data[$field['name']] ? 'true' : 'false';
        else:
          $submission[] = $submission_data[$field['name']];
        endif;
      endforeach;

      fputcsv($output, $submission);
    endforeach;

    fclose($output);

    exit();
  }
}