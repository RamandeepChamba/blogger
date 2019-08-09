<?php include __DIR__ . '/errors.html.php'; ?>

<?php if (isset($blog[0]['blog'])) { ?>
  <div id ="blog-container">
    <?=$blog[0]['blog']?>
  </div>
  <hr>
  <?php include __DIR__ . '/comments.html.php'; ?>
<?php }; ?>
