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
    <script src="/tinymce/tinymce/tinymce.min.js"></script>
    <script>
      tinymce.init({
        selector: '#blog',
        plugins: 'link, image',
        toolbar: 'bold italic link image',
        default_link_target: "_blank",
        link_assume_external_targets: 'https',
        image_uploadtab: true,
        images_upload_url: '/blog/upload',

        images_upload_handler : function(blobInfo, success, failure) {
    			var xhr, formData;

    			xhr = new XMLHttpRequest();
    			xhr.withCredentials = false;
    			xhr.open('POST', '/blog/upload');

    			xhr.onload = function() {
    				var json;

    				if (xhr.status != 200) {
    					failure('HTTP Error: ' + xhr.status);
    					return;
    				}

    				json = JSON.parse(xhr.response);

            if (!json || !json.location) {
    					failure('Invalid JSON: ' + xhr.responseText);
    					return;
    				}

    				success(json.location);
    			};

    			formData = new FormData();
    			formData.append('file', blobInfo.blob(), blobInfo.filename());

    			xhr.send(formData);
    		},

      });
    </script>
  </body>
</html>
