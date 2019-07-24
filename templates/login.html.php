<?php if (isset($errors)) { ?>
  <?php foreach ($errors as $e) { ?>
    <div class="alert alert-danger"><?=$e?></div>
  <?php }; ?>
<?php }; ?>
<form action="/user/login" method="post">
  <input type="text" name="user[name]" autocomplete="off"
    placeholder="Username" value="<?=$username ?? ''?>">
  <input type="password" name="user[password]" placeholder="Password">
  <button type="submit" name="login">Login</button>
</form>
