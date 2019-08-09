<form action="/blog/comment/add" method="post" class="form-reply">
  <?php if (isset($parent_id)) { ?>
    <input type="text" name="comment[parent_id]" value="<?=$parent_id?>" hidden>
  <?php }; ?>
  <input type="text" name="comment[blog_id]"
    value="<?php echo (isset($type) && $type == 'reply')
      ? $blog_id : $blog[0]['blog_id'] ?>"
    hidden>
  <input type="text" name="comment[user_id]" value="<?=$user_id?>" hidden>
  <input type="text" name="comment[comment]"
    placeholder="<?php echo (isset($type) && $type == 'reply')
      ? 'Add reply' : 'Comment on this blog' ?>"
    autocomplete="off">
  <button type="submit" name="addComment">
    <?php echo (isset($type) && $type == 'reply')
      ? 'Reply' : 'Comment' ?>
  </button>
  <?php if (isset($type) && $type == 'reply') { ?>
    <button type="button" name="cancel" class="btn-cancel">Cancel</button>
  <?php }; ?>
</form>
