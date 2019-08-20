<form action="/blog/comment/add" method="post" class="form-reply">
  <input type="text" name="comment[id]"
    value="<?=$comment['id'] ?? ''?>" hidden>
  <input type="text" name="comment[parent_id]"
    value="<?=$comment['parent_id'] ?? ($parent_id ?? '') ?>" hidden>
  <input type="text" name="comment[blog_id]"
    value="<?php echo (isset($type) && $type == 'reply')
      ? $blog_id : (isset($type) && $type == 'edit_comment'
        ? $comment['blog_id'] : $blog['blog_id']) ?>"
    hidden>
  <input type="text" name="comment[user_id]" value="<?=$user_id?>" hidden>
  <input type="text" name="comment[comment]"
    placeholder="<?php echo (isset($type) && $type == 'reply')
      ? 'Add reply' : 'Comment on this blog' ?>"
    value="<?=$comment['comment'] ?? ''?>"
    autocomplete="off">
  <button type="submit" name="addComment">
    <?php echo (isset($type) && $type == 'reply')
      ? 'Reply' : (isset($type) && $type == 'edit_comment'
        ? 'Edit' : 'Comment') ?>
  </button>

  <?php if (isset($type) && $type == 'reply') { ?>
    <button type="button" name="cancel_reply" class="btn-cancel_reply">Cancel</button>
  <?php }; ?>

  <?php if (isset($type) && $type == 'edit_comment') { ?>
    <button type="button" name="cancel_edit" class="btn-cancel_edit">Cancel</button>
  <?php }; ?>
</form>
