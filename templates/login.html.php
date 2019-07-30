<?php include __DIR__ . '/errors.html.php'; ?>

<form action="/user/login" method="post">
  <input type="text" name="user[name]" autocomplete="off"
    autofocus placeholder="Username" value="<?=$username ?? ''?>">
  <input type="password" name="user[password]" placeholder="Password">
  <button type="submit" name="login">Login</button>
</form>
