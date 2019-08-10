<ul class="comments">
<?php foreach ($blog as $b) { ?>
  <br>
  <li>
    <p class="comment">
      <?=$b['comment']?>
      <br>
      <em>By: </em><a href="#"><?=$b['name']?></a>
    </p>
    <!-- If current user is the author of comment -->
    <?php if ($b['user_id'] == $user_id) { ?>
      <button type="button" name="edit_comment">Edit</button>
      <form action="/blog/comment/delete" method="post">
        <input type="text" name="blog_id" value=<?=$b['blog_id']?> hidden>
        <input type="text" name="comment_id" value=<?=$b['comment_id']?> hidden>
        <button type="submit" name="delete_comment">Delete</button>
      </form>
    <?php }; ?>
    <button type="button" name="showReplies"
      class="btn-show_replies"
      data-comment_id=<?=$b['comment_id']?>
    >
      <!-- [Show no. of replies] -->
      Show replies
    </button>
    <button type="button" name="reply" class="btn-reply"
      data-comment_id=<?=$b['comment_id']?>
      data-blog_id=<?=$b['blog_id']?>
    >
      Reply
    </button>
  </li>
<?php }; ?>
</ul>
