<?php

namespace WWForms\API;

use WWForms\API\FieldsAPI;
use DateTime;
use DateTimeZone;

class EmailAPI {
  public static function send_email($form_id, $submission_data) {
    $emailSettings = get_field('email_notifications', $form_id);
    $fromEmail = $emailSettings['from_email'];
    $toEmails = $emailSettings['to_emails'];

    if ($fromEmail && $toEmails):
      $headers[] = 'From: ' . $fromEmail;
      $headers[] = 'Content-type: text/html';

      $fields = FieldsAPI::get_fields($form_id);

      $date = new DateTime(current_time('mysql'));
      $timezone = get_option('timezone_string') ? get_option('timezone_string') : 'UTC';
      $date->setTimezone(new DateTimeZone($timezone));
      $formattedDate = $date->format('n/j/Y, g:ia');

      $subject = get_the_title($form_id) . ' Submission - ' . $formattedDate;

      $message = '<p><strong>Submitted</strong>: ' . $formattedDate . '</p>';

      foreach ($fields as $field):
        $message .= '<p><strong>' . $field['label'] . '</strong>: ';

        if ($field['type'] == 'multiple_choice'):
          $values = [];
          foreach (array_keys($submission_data) as $key):
            if (str_contains($key, $field['name'])):
              $values[] = $submission_data[$key];
            endif;
          endforeach;
          $message .= implode(', ', $values);

        elseif ($field['type'] == 'checkbox'):
          $message .= $submission_data[$field['name']] ? 'true' : 'false';

        else:
          $message .= $submission_data[$field['name']];
        endif;

        $message .= '</p>';
      endforeach;

      wp_mail($toEmails, $subject, $message, $headers);
    endif;
  }
}