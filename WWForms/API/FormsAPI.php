<?php

namespace WWForms\API;

use WWForms\Tables\Submissions;
use Exception;

class FormsAPI {
  public static function submit_form() {
    $form_id = $_POST['formId'];

    $settings = get_field('display_settings', $form_id);

    // format object from javascript
    $form_data = $_POST['submission'];
    $cleaned_data = str_replace("\\", "", $form_data);
    $submission_data = json_decode($cleaned_data, true);

    foreach ($_FILES as $key => $value) {
      $value['name'] = preg_replace( '/[^0-9a-zA-Z.]/', '', basename( $value['name'] ) );

      $upload_overrides = array( 'test_form' => false );
      $upload_result = wp_handle_upload($value, $upload_overrides);

      if( $upload_result['url'] ):
        $submission_data[$key] = $upload_result['url'];
      endif;
    }

    $recaptcha_token = $_POST['recaptchaToken'];

    if ($recaptcha_token):
      $secret_key = get_field('secret_key', 'option');
      $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
          'body' => [
              'secret' => $secret_key,
              'response' => $recaptcha_token,
          ],
      ]);

      $response = json_decode(wp_remote_retrieve_body($response), true);

      if ($response['success']):
          try {
              Submissions::insert_submission($form_id, $submission_data);

              echo $settings['success_message'];
          } catch (Exception $e) {
              echo 'Something went wrong. Please try again later.';
          }
      else:
          echo $settings['success_message'];

          exit();
      endif;
    else:
        try {
            Submissions::insert_submission($form_id, $submission_data);

            echo $settings['success_message'];
        } catch (Exception $e) {
            echo 'Something went wrong. Please try again later.';
        }
    endif;

    exit();
  }
}