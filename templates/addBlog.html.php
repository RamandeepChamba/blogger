<?php include __DIR__ . '/errors.html.php'; ?>

<form action="/blog/add" method="post">
  <input type="text" name="blog[id]" value="<?=$blog['id']?>" hidden>
  <input type="text" name="blog[title]" value="<?=$blog['title']?>"
    placeholder="Title" required autofocus>
  <input type="text" name="blog[description]" value="<?=$blog['description']?>" 
    placeholder="Description" required>
  <textarea id="blog" name="blog[blog]" rows="20"
    placeholder="Start writing blog"
    autofocus><?=$blog['blog'] ?? ''?></textarea>
  <button type="submit" name="add">Add</button>
</form>
