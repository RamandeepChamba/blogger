<ul class="comments">  
<?php foreach ($blog as $b) { ?>
  <li>
    <?=$b['comment']?><br>
    <em>By: </em><a href="#"><?=$b['name']?></a><br>
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
