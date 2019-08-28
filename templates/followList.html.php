<?php if ($following ?? $followers) { ?>
<ul class="<?=isset($followers) ? 'followers' : 'following'?>">
<?php foreach (($followers ?? $following) as $f) { ?>
  <li>
    <a href="/user?id=<?=$f['id']?>"><?=$f['name']?></a>
  </li>
<?php }; ?>
</ul>
<?php }; ?>
