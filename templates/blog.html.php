<?php include __DIR__ . '/errors.html.php'; ?>

<?php if (isset($blog['blog'])) { ?>
  <div id ="blog-container">
    <?=$blog['blog']?>
    <em>By: </em>
    <a href="/user?id=<?=$blog['user_id']?>">
      <?php if ($blog['user_id'] == $user_id) { ?>
        you
      <?php } else { ?>
        <?=$blog['author']?>
      <?php }; ?>
    </a>
  </div>
  <hr>
  <?php include __DIR__ . '/comments.html.php'; ?>
<?php }; ?>
