<?php if (isset($errors)) { ?>
  <?php foreach ($errors as $e) { ?>
    <div class="alert alert-danger"><?=$e?></div>
  <?php }; ?>
<?php }; ?>
