<?php include __DIR__ . '/errors.html.php'; ?>

<form action="/user/register" method="post">
  <input type="text" name="user[name]" placeholder="Username"
    value="<?=$username ?? ''?>" autocomplete="off" autofocus>
  <input type="password" name="user[password]" placeholder="Password">
  <input type="password" name="user[cpassword]" placeholder="Confirm Password">
  <button type="submit" name="register">Register</button>
</form>
