<?php if (count($blogs)) { ?>
  <h1>Blogs</h1>
  <hr>
  <ul>
  <?php foreach ($blogs as $blog) { ?>
    <li class="blog-overview">
      <h1><?=$blog['title']?></h1>
      <p><?=$blog['description']?></p>
      <a href="/blog/view?id=<?=$blog['id']?>">View</a>
      <br>
      <em><strong>By:</strong> <?=ucwords($blog['name'])?></em>
      <br>
      <em><strong>On:</strong> <?=date('d/m/Y', strtotime($blog['created_at']))?></em>
      <br>
      <em><strong>Last Updated:</strong>
        <?php
        $seconds = time() - strtotime($blog['last_updated']);
        echo $helpers->timeElapsed($seconds) . ' ago';
        ?>
      </em>
    </li>
    <?php if ($user_id == $blog['user_id']) { ?>
    <!-- Show only if current user is the author -->
    <a href="/blog/add?id=<?=$blog['id']?>">Edit</a>
    <a href="/blog/delete?id=<?=$blog['id']?>">Delete</a>
    <?php }; ?>
    <hr>
  <?php }; ?>
  </ul>
<?php } else { ?>
  <h2>No Blogs <a href="/blog/add">create now</a></h2>
<?php }; ?>
