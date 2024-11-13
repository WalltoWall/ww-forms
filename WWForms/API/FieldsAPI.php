<?php

namespace WWForms\API;

use WWForms\Helpers;

class FieldsAPI {
  public static function install_fields() {
    $type_json = Helpers::get_json('form_post_type.json');
    $type = acf_get_post_type($type_json['key']); // post type key

    if (empty($type)) {
      acf_import_post_type($type_json);
    }

    $fields_json = Helpers::get_json('form_field_group.json');
    $fields = acf_get_field_group($fields_json['key']); // field group key

    if (empty($fields)) {
      acf_import_field_group($fields_json);
    }

    $recaptcha_page_json = Helpers::get_json('recaptcha_options_page.json');
    $recaptcha_page = acf_get_ui_options_page($recaptcha_page_json['key']); // options page key

    if (empty($recaptcha_page)) {
      acf_import_ui_options_page($recaptcha_page_json);
    }

    $recaptcha_json = Helpers::get_json('recaptcha_field_group.json');
    $recaptcha = acf_get_field_group($recaptcha_json['key']); // field group key

    if (empty($recaptcha)) {
      acf_import_field_group($recaptcha_json);
    }
  }

  public static function get_fields($form_id) {
    $fields = [];
    if (have_rows('fields', $form_id)):
      while (have_rows('fields', $form_id)): the_row();
          if (get_row_layout() != "heading"):
              $label = get_sub_field('label');
              $name = get_sub_field('field_name');

              $fields[] = array(
              'label' => $label,
              'name' => $name,
              'type' => get_row_layout()
              );
          endif;
      endwhile;
    endif;

    return $fields;
  }

  public static function get_field_labels($form_id, $include_date = true) {
    $headers = [];

    if ($include_date):
      $headers[] = 'Submitted';
    endif;

    if (have_rows('fields', $form_id)):
      while (have_rows('fields', $form_id)): the_row();
          if (get_row_layout() != "heading"):
              $label = get_sub_field('label');
              $headers[] = $label;
          endif;
      endwhile;
    endif;

    return $headers;
  }
}