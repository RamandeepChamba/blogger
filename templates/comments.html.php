<div id="comments-container">
  <h2>Comments</h2>
  <hr>
  <?php include __DIR__ . '/commentForm.html.php'; ?>
  <br>
  <br>
  <?php if (!$blog[0]['comment']) { ?>
    <p>Be the first to comment</p>
  <?php } else {
    include __DIR__ . '/commentsList.html.php';
  }; ?>
</div>

<script type="text/javascript">
  let comments = $('.comments')

  comments.click(function (e) {
    let btn = e.target

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
    else if($(btn).hasClass('btn-cancel'))
    {
      // Remove clicked class from reply button
      let replyForm = $(btn).parent()
      $(replyForm).parent().children('.btn-reply')
        .removeClass('clicked')
      // Remove reply form
      $(replyForm).remove()
    }
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
            $(html).insertAfter(btn)
          }
        )
      } else {
        // Hide replies
        $(btn).html('Show replies')

        $(btn).parent().children('.comments').remove()
      }
      // Toggle show class
      $(btn).toggleClass('hide')
    }
  })
</script>

<!-- TODO -->
<!--

  ==>
  If comment has replies
    - Show 'show replies' button
      - On 'hide replies' click
        - Hide replies

  ==>
  On username click
    - Show user's profile page

  ==>
  On reply button click
    - render reply form
      - On cancel click
        - Delete reply form
-->
