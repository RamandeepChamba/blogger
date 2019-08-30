<ul class="<?=isset($replies) ? 'replies' : 'comments' ?>">
<?php foreach ($comments as $c) { ?>
  <br>
  <li>
    <p class="comment">
      <?=$c['comment']?>
      <br>
      <em>By: </em>
      <a href="/user?id=<?=$c['user_id']?>">
        <?php if ($c['user_id'] == $user_id) { ?>
          you
        <?php } else { ?>
          <?=$c['author']?>
        <?php }; ?>
      </a>
      <br>
      <?php if ($c['user_id'] == $user_id) { ?>
        <strong>Likes</strong>
      <?php } else { ?>
        <button type="button"
          name="<?=in_array($c['comment_id'], $liked_comments)
            ? 'unlike_comment'
            : 'like_comment'?>"
          id="comment_<?=$c['comment_id']?>"
          data-comment_id="<?=$c['comment_id']?>"
        >
          <?=in_array($c['comment_id'], $liked_comments)
            ? 'Unlike' : 'Like'?>
        </button>
      <?php }; ?>
      <strong id="comment_<?=$c['comment_id']?>_likes"
        style="color: #777; font-size: 90%">
        <?=$c['likes']?>
      </strong>
    </p>
    <!-- If current user is the author of comment -->
    <?php if ($c['user_id'] == $user_id) { ?>
      <!-- Edit comment -->
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
      data-replies=<?=$c['replies']?>
    >
      Show <strong><?=$c['replies']?></strong> replies
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
