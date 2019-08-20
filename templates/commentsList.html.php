<ul class="<?=isset($replies) ? 'replies' : 'comments' ?>">
<?php foreach ($comments as $c) { ?>
  <br>
  <li>
    <p class="comment">
      <?=$c['comment']?>
      <br>
      <em>By: </em>
      <a href="/user/<?=$c['user_id']?>">
        <?php if ($c['user_id'] == $user_id) { ?>
          you
        <?php } else { ?>
          <?=$c['author']?>
        <?php }; ?>
      </a>
    </p>
    <!-- If current user is the author of comment -->
    <?php if ($c['user_id'] == $user_id) { ?>
      <!-- Edit comment -->
      <!-- TODO -->
      <button type="button" name="btn-edit_comment"
        data-comment_id="<?=$c['comment_id']?>"
      >
        Edit
      </button>
      <!--  -->
      <form action="/blog/comment/delete" method="post">
        <input type="text" name="blog_id" value=<?=$c['blog_id']?> hidden>
        <input type="text" name="comment_id" value=<?=$c['comment_id']?> hidden>
        <button type="submit" name="btn-delete_comment">Delete</button>
      </form>
    <?php }; ?>
    <button type="button" name="showReplies"
      class="btn-show_replies"
      data-comment_id=<?=$c['comment_id']?>
    >
      <!-- [Show no. of replies] -->
      Show replies
    </button>
    <button type="button" name="reply" class="btn-reply"
      data-comment_id=<?=$c['comment_id']?>
      data-blog_id=<?=$c['blog_id']?>
    >
      Reply
    </button>
  </li>
<?php }; ?>
</ul>
