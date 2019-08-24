<?php if (count($blogs)) { ?>
  <h1>Blogs <strong style="color: #777; font-size: 80%"><?=count($blogs)?></strong></h1>
  <hr>
  <ul>
  <?php foreach ($blogs as $blog) { ?>
    <li class="blog-overview">
      <h1><?=$blog['title']?></h1>
      <p><?=$blog['description']?></p>
      <a href="/blog/view?id=<?=$blog['id']?>">View</a>
      <br>
      <em>
        <strong>By:</strong>
        <a href="/user?id=<?=$blog['user_id'] ?? $user['id']?>">
          <?php if (($blog['user_id'] ?? $user['id']) == $user_id) { ?>
            you
          <?php } else { ?>
            <?=$blog['name'] ?? $user['name']?>
          <?php }; ?>
        </a>
      </em>
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
    <?php if (isset($blog['user_id']) &&
      $user_id == ($blog['user_id'] ?? $user['id'])) { ?>
    <!-- Show only if current user is the author -->
    <a href="/blog/add?id=<?=$blog['id']?>">Edit</a>
    <a href="/blog/delete?id=<?=$blog['id']?>">Delete</a>
    <?php }; ?>
    <hr>
  <?php }; ?>
  </ul>
<?php } else { ?>
  <h2>
    <?=isset($user) ? 'No Blogs' : 'No Blogs <a href="/blog/add">create now</a>'?>
  </h2>
<?php }; ?>
