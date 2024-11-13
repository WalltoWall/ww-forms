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

    $recaptcha_token = $_POST['recaptchaToken'];

    if ($recaptcha_token):
      $recaptcha = get_field('google_recaptcha', 'option');
      $secretKey = $recaptcha['secret_key'];
      $response = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
          'body' => [
              'secret' => $secretKey,
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