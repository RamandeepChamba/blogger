<h1><?=$user['name']?></h1>

<section class="section-follow" data-user_id="<?=$user['id']?>">
  <?php if ($user['id'] == $user_id) { ?>
  <?php } elseif ($following) { ?>
  <a href="/user/unfollow?id=<?=$user['id']?>">Unfollow</a>
  <?php } else { ?>
  <a href="/user/follow?id=<?=$user['id']?>">Follow</a>
  <?php }; ?>
  <hr>

  <button type="button" name="btn-followers">
    Followers <strong><?=$user['followers']?></strong>
  </button>
  <br>
  <button type="button" name="btn-following">
    Following <strong><?=$user['following']?></strong>
  </button>
</section>

<hr>

<?php include __DIR__ . '/showBlogs.html.php'; ?>

<script type="text/javascript">
  $(document).ready(load)

  function load() {

    let user_id = $('.section-follow').data('user_id')
    let btn_followers = $("[name = 'btn-followers']")
    let btn_following = $("[name = 'btn-following']")

    $(btn_followers).click(function(e) {
      let btn = btn_followers

      if ($(btn).hasClass('clicked')) {
        // Change clicked status
        $(btn).removeClass('clicked')
        // Remove follow(ing / ers) list
        $(btn).parent().children('.followers').remove()
      }
      else {
        // Change clicked status
        $(btn).addClass('clicked')
        // Fetch follow(ing / ers) list
        let url = '/user/followers'

        $.get(url,
          {id: user_id},
          function (html) {
            if (html != '') {
              // Append followers list after button
              $(btn).after(html)
            }
          }
        )
      }
    })

    $(btn_following).click(function(e) {
      let btn = btn_following

      if ($(btn).hasClass('clicked')) {
        // Change clicked status
        $(btn).removeClass('clicked')
        // Remove follow(ing / ers) list
        $(btn).parent().children('.following').remove()
      }
      else {
        // Change clicked status
        $(btn).addClass('clicked')
        // Fetch follow(ing / ers) list
        let url = '/user/following'

        $.get(url,
          {id: user_id},
          function (html) {
            if (html != '') {
              // Append followers list after button
              $(btn).after(html)
            }
          }
        )
      }
    })

  }
</script>
