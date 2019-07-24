<?php if (isset($errors)) { ?>
  <?php foreach ($errors as $e) { ?>
    <div class="alert alert-danger"><?=$e?></div>
  <?php }; ?>
<?php }; ?>
<form action="/user/register" method="post">
  <input type="text" name="user[name]" placeholder="Username"
    value="<?=$username ?? ''?>" autocomplete="off">
  <input type="password" name="user[password]" placeholder="Password">
  <input type="password" name="user[cpassword]" placeholder="Confirm Password">
  <button type="submit" name="register">Register</button>
</form>
