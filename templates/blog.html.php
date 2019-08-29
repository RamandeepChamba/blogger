<?php include __DIR__ . '/errors.html.php'; ?>

<?php if (isset($blog['blog'])) { ?>
  <div id ="blog-container">
    <?=$blog['blog']?>
    <?php if ($blog['user_id'] != $user_id) { ?>
    <!-- User can't like his own blog -->

    <button type="button"
      name="<?=$liked ? 'unlike_blog' : 'like_blog'?>"
      data-blog_id="<?=$blog['blog_id']?>"
    >
      <?=$liked ? 'Unlike' : 'Like'?>
    </button>
  <?php } else { ?>
    <strong>Likes</strong>
  <?php }; ?>
    <strong id="blog_likes"
      style="color: #777; font-size: 90%">
      <?=$likes?>
    </strong>
    <br>
    <em>By: </em>
    <a href="/user?id=<?=$blog['user_id']?>">
      <?php if ($blog['user_id'] == $user_id) { ?>
        you
      <?php } else { ?>
        <?=$blog['author']?>
      <?php }; ?>
    </a>
  </div>
  <hr>
  <?php include __DIR__ . '/comments.html.php'; ?>
<?php }; ?>

<script type="text/javascript">
  $(document).ready(load)

  function load() {
    $('#blog-container').click(function (e) {
      let btn = e.target

      // Like / Unlike blog
      if ($(btn).attr('name') == 'like_blog'
        || $(btn).attr('name') == 'unlike_blog'
      )
      {

        let name = $(btn).attr('name')
        let action = name.split('_')[0]

        // Fetch blog id
        let blog_id = $(e.target).data('blog_id')
        let url = '/blog/like'
        let request =
          {
            blog_id,
            unlike: action == 'unlike'
          }

        // Post like / Unlike to blog
        $.post(url, request,
          function(data)
          {
            let json = JSON.parse(data)
            // On success
            if (!json.error) {
              alert('success')
              // Toggle button
              let newName = request.unlike ? 'like_blog' : 'unlike_blog'
              let html = request.unlike ? 'Like' : 'Unlike'
              $(`[name=${name}]`).html(html)
              $(`[name=${name}]`).attr('name', newName)
              // Update likes
              $('#blog_likes').html(json.likes)
            }
            // Errors
            else {
              let e = json.error
              alert(e.code + ': ' + e.msg)
            }
          })

            // Failure
            .fail(function(e) {
              if (e.status == 403) {
                alert('login to ' + action)
              }
              else {
                alert('unable to ' + action + ' blog')
              }
            })
      }
    });
  }
</script>
