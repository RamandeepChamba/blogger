<form action="/blog/add" method="post">
  <input type="text" name="blog[id]" value="<?=$blog['id']?>" hidden>
  <textarea id="blog" name="blog[blog]" rows="20"
    placeholder="Start writing blog"
    autofocus><?=$blog['blog'] ?? ''?></textarea>
  <button type="submit" name="add">Add</button>
</form>
