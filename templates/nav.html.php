<nav>
  <ul>
    <li><a href="/">Home</a></li>
    <li><a href="/blog/add">Add Blog</a></li>
    <?php if (isset($loggedIn) && $loggedIn) { ?>
      <li><a href="/user/logout">Logout</a></li>
    <?php } else { ?>
      <li><a href="/user/login">Login</a></li>
      <li><a href="/user/register">Register</a></li>
    <?php }; ?>
  </ul>
</nav>
