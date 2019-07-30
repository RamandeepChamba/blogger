<?php include __DIR__ . '/errors.html.php'; ?>

<?php if (isset($blog)) { ?>
  <div id ="blog-container">
    <?=$blog['blog']?>
  </div>
<?php }; ?>
