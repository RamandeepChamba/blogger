<?php if (count($blogs)) { ?>
  <h1>Blogs</h1>
  <hr>
  <ul>
  <?php foreach ($blogs as $blog) { ?>
    <li><?=$blog['blog']?></li>
    <a href="/blog/add?id=<?=$blog['id']?>">Edit</a>
    <a href="/blog/delete?id=<?=$blog['id']?>">Delete</a>
    <hr>
  <?php }; ?>
  </ul>
<?php } else { ?>
  <h2>No Blogs <a href="/blog/add">create now</a></h2>
<?php }; ?>
