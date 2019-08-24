<?php include __DIR__ . '/errors.html.php'; ?>

<form action="/blog/add" method="post">
  <input type="text" name="blog[id]" value="<?=$blog['id']?>" hidden>
  <label for="blog-title">Title</label>
  <input type="text" name="blog[title]" id="blog-title"
    value="<?=$blog['title']?>" placeholder="Title" required autofocus>
  <label for="blog-description">Description</label>
  <input type="text" name="blog[description]" id="blog-description"
    value="<?=$blog['description']?>"
    placeholder="Description" required>
  <textarea id="blog" name="blog[blog]" rows="20"
    placeholder="Start writing blog"
    autofocus><?=$blog['blog'] ?? ''?></textarea>
  <button type="submit" name="add">Add</button>
</form>
