<?php
  global $post;
?>
  <div class="submissions-wrapper">
    <div class="submissions-buttons">
    <div class="actions">
			<label>
        Items per page:
        <select id="per-page-selector">
          <option value="10">10</option>
          <option value="25">25</option>
          <option value="50">50</option>
        </select>
      </label>
		</div>
      <?php
        $currentUrl = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
      ?>
      <a class="button button-primary" target="_blank" href="<?= $currentUrl.'/wp-admin/admin-ajax.php?action=get_submissions_csv&formId='.$post->ID ?>">
        Export CSV
      </a>
    </div>

    <div class="submissions">
      <table class="wp-list-table widefat striped table-view-list posts" id="submissions-table" data-id="<?= $post->ID ?>">
        <caption class="screen-reader-text"></caption>
        <thead>
          <tr>
          </tr>
        </thead>

        <tbody>
        </tbody>
      </table>
    </div>

    <div class="pagination" id="submission-pagination"></div>
  </div>
