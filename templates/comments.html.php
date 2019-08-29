<div id="comments-container">
  <h2>
    Comments
    <strong style="color: #777; font-size: 80%">
      <?=count($comments)?>
    </strong>
  </h2>
  <hr>
  <?php include __DIR__ . '/commentForm.html.php'; ?>
  <br>
  <br>
  <?php if (isset($comments) && !count($comments)) { ?>
    <p>Be the first to comment</p>
  <?php } else {
    include __DIR__ . '/commentsList.html.php';
  }; ?>
</div>

<script type="text/javascript">
  let comments = $('.comments')

  comments.click(function (e) {
    let btn = e.target

    // Reply form
    if ($(btn).hasClass('btn-reply')
      && !$(btn).hasClass('clicked'))
    {
      let blog_id = btn.dataset.blog_id
      let parent_id = btn.dataset.comment_id

      // Add clicked class so form can be rendered only once
      $(btn).addClass('clicked')

      $.get('/blog/comment/reply',
        {parent_id, blog_id},
        function (html) {
          $(html).insertAfter(btn)
        }
      )
    }
    // Remove reply form
    else if($(btn).hasClass('btn-cancel_reply'))
    {
      // Remove clicked class from reply button
      let replyForm = $(btn).parent()
      $(replyForm).parent().children('.btn-reply')
        .removeClass('clicked')
      // Remove reply form
      $(replyForm).remove()
    }
    // Show / Hide replies
    else if ($(btn).hasClass('btn-show_replies'))
    {
      if (!$(btn).hasClass('hide'))
      {
        let parent_id = btn.dataset.comment_id

        // Show replies
        $(btn).html('Hide replies')

        $.get('/blog/comment/showReplies',
          {parent_id},
          function (html) {
            $(btn).parent().append(html)
          }
        )
      } else {
        // Hide replies
        let replies = btn.dataset.replies
        $(btn).html(`Show <strong>${replies}</strong> replies`)

        $(btn).parent().children('.replies').remove()
      }
      // Toggle show class
      $(btn).toggleClass('hide')
    }
    // Edit comment
    else if ($(btn).attr('name') == 'btn-edit_comment') {
      let comment_id = btn.dataset.comment_id
      // Replace comment with edit form
      $.get('/blog/comment/edit',
        {comment_id},
        function (edit_form) {
          $(btn).parent().html(edit_form)
        }
      )
    }
    // Remove edit form and render comment
    else if($(btn).hasClass('btn-cancel_edit'))
    {
      // Grab parent comment id
      let parent_id = btn.dataset.parent_id
      let blog_id = btn.dataset.blog_id

      if (!parent_id) {
        location.href='/blog/view?id=' + blog_id;
      }
      else {
        // Grab parent comment
        let parent_comment = $(btn).parent().parent().parent().parent()
        // Re-render replies
        $(parent_comment).children('.replies').remove()
        $.get('/blog/comment/showReplies',
          {parent_id},
          function (html) {
            $(parent_comment).append(html)
          }
        )
      }
    }
    else if ($(btn).attr('name') == 'like_comment') {
      console.log('like comment')
    }
  })
</script>
