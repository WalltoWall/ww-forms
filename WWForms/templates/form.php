<?php

$form = get_post($args['id']);
$site_key = get_field('site_key', 'option');

if ($form):
  $submit_label = get_field('submit_button_text', $form->ID);
?>

<form class="ww-form" data-id="<?= $form->ID ?>" data-sitekey="<?= $site_key ?>">
  <?php if (have_rows('fields', $form->ID)):
    while (have_rows('fields', $form->ID)): the_row();
      $label = get_sub_field('label');
      $name = get_sub_field('field_name');

      if (empty($name)):
        $name = uniqid('field-');
        update_sub_field('field_name', $name);
      endif;

      $placeholder = get_sub_field('placeholder');
      $required = get_sub_field('required');

      if (get_row_layout() == 'text_field'): ?>
        <label class="ww-form-control">
          <span class="ww-form-label">
            <?= $label ?>
            <?php if ($required): ?>
              <span>*</span>
            <?php endif; ?>
          </span>
          <input type="text" name="<?= $name ?>" class="ww-input ww-text-field" placeholder="<?= $placeholder ?>" <?= $required ? 'required' : '' ?> />
        </label>

      <?php elseif (get_row_layout() == 'text_area'): ?>
        <label class="ww-form-control">
          <span class="ww-form-label">
            <?= $label ?>
            <?php if ($required): ?>
              <span>*</span>
            <?php endif; ?>
          </span>
          <textarea name="<?= $name ?>" class="ww-input ww-textarea" placeholder="<?= $placeholder ?>" <?= $required ? 'required' : '' ?>></textarea>
        </label>

        <?php elseif (get_row_layout() == 'dropdown'): ?>
          <label class="ww-form-control">
            <span class="ww-form-label">
              <?= $label ?>
              <?php if ($required): ?>
                <span>*</span>
              <?php endif; ?>
            </span>
            <div class="ww-dropdown-container">
              <select name="<?= $name ?>" class="ww-input ww-dropdown" <?= $required ? 'required' : '' ?>>
                <?php if ($placeholder): ?>
                  <option value=""><?= $placeholder ?></option>
                <?php endif; ?>

                <?php if (have_rows('options')):
                  while (have_rows('options')): the_row();
                    $option = get_sub_field('option');
                  ?>
                    <option value="<?= $option ?>"><?= $option ?></option>
                  <?php
                  endwhile;
                endif;
                ?>
              </select>
              <span class="ww-dropdown-indicator"></span>
            </div>
          </label>

        <?php elseif (get_row_layout() == 'heading'): ?>
          <h2 class="ww-form-heading"><?= get_sub_field('value') ?></h2>

        <?php elseif (get_row_layout() == 'checkbox'): ?>
          <label class="ww-form-control ww-checkbox-control">
            <input type="checkbox" name="<?= $name ?>" class="ww-checkbox" placeholder="<?= $placeholder ?>" <?= $required ? 'required' : '' ?> />
            <span class="ww-checkbox-label">
              <?= $label ?>
              <?php if ($required): ?>
                <span>*</span>
              <?php endif; ?>
            </span>
          </label>

        <?php elseif (get_row_layout() == 'radio_buttons'): ?>
          <fieldset class="ww-form-control ww-form-fieldset">
            <legend class="ww-form-label ww-form-legend">
              <?= $label ?>
              <?php if ($required): ?>
                <span>*</span>
              <?php endif; ?>
            </legend>
            <?php if (have_rows('options')):
              while (have_rows('options')): the_row();
                $option = get_sub_field('option');
              ?>
                <label class="ww-radio-control">
                  <input type="radio" name="<?= $name ?>" value="<?= $option ?>" class="ww-radio" <?= $required ? 'required' : '' ?> />
                  <span class="ww-radio-label"><?= $option ?></span>
                </label>
              <?php
              endwhile;
            endif;
            ?>
          </fieldset>

        <?php elseif (get_row_layout() == 'multiple_choice'): ?>
          <fieldset class="ww-form-control ww-form-fieldset">
            <legend class="ww-form-label ww-form-legend">
              <?= $label ?>
            </legend>
            <?php if (have_rows('options')):
              $index = 0;
              while (have_rows('options')): the_row();
                $option = get_sub_field('option');
                $optionName = $name.'-'.$index;
              ?>
                <label class="ww-checkbox-control">
                  <input type="checkbox" name="<?= $optionName ?>" value="<?= $option ?>" class="ww-checkbox" />
                  <span class="ww-checkbox-label"><?= $option ?></span>
                </label>
              <?php
                $index++;
              endwhile;
            endif;
            ?>
          </fieldset>

        <?php elseif (get_row_layout() == 'email'): ?>
          <label class="ww-form-control">
            <span class="ww-form-label">
              <?= $label ?>
              <?php if ($required): ?>
                <span>*</span>
              <?php endif; ?>
            </span>
            <input type="email" name="<?= $name ?>" class="ww-input ww-email" placeholder="<?= $placeholder ?>" <?= $required ? 'required' : '' ?> />
          </label>

        <?php elseif (get_row_layout() == 'file_upload'):
          $allowedFileTypes = get_sub_field('allowed_file_types');
        ?>
          <label class="ww-form-control">
            <span class="ww-form-label">
              <?= $label ?>
              <?php if ($required): ?>
                <span>*</span>
              <?php endif; ?>
            </span>
            <input type="file" name="<?= $name ?>" class="ww-input ww-file-upload" accept="<?= $allowedFileTypes ?>" <?= $required ? 'required' : '' ?> />
          </label>

  <?php endif;
    endwhile;
  endif; ?>

  <div class="ww-form-submit-container">
    <button type="submit" class="ww-form-submit"><?= $submit_label ? $submit_label : 'Submit' ?></button>

    <?php if ($site_key): ?>
      <p class="ww-recaptcha-text">This site is protected by reCAPTCHA and the Google
        <a href="https://policies.google.com/privacy">Privacy Policy</a> and
        <a href="https://policies.google.com/terms">Terms of Service</a> apply.
      </p>
    <?php endif; ?>
  </div>
</form>

<?php endif; ?>