<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title><?=$title?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  </head>
  <body>
    <?php include __DIR__ . '/nav.html.php'; ?>
    <?=$output?>
    <script src="https://cdn.tiny.cloud/1/gt0xszlgk8n09tbq88x4nvchhz614bqp03fwt21bxnqsstm6/tinymce/5/tinymce.min.js"></script>
    <script>
      tinymce.init({
        selector: '#blog'
      });
    </script>
  </body>
</html>
