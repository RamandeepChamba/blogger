<?php
namespace Blog\Controllers;
use \Raman\DatabaseTable;
use \Raman\Authentication;
use \Raman\Helpers;

class Comment
{
  private $commentsTable;
  private $authentication;
  private $helpers;

  public function __construct(DatabaseTable $commentsTable,
    Authentication $authentication)
  {
    $this->commentsTable = $commentsTable;
    $this->authentication = $authentication;
    $this->helpers = new Helpers();
  }

  // Add/update comment
  public function save()
  {
    // Sanitize data
    $data = array_map(array($this->helpers, 'sanitize'), $_POST['comment']);
    // Error checking
      if (!isset($data['comment']) || empty($data['comment'])) {
        $errors[] = "Comment can't be empty";
      }

      if (!isset($data['user_id'])
        || (isset($data['user_id']) && empty($data['user_id'])))
      {
        $errors[] = 'Login to comment';
      }

      if (!isset($data['blog_id'])
        || (isset($data['blog_id']) && empty($data['blog_id'])))
      {
        $errors[] = 'Blog not found';
      }

    if (!isset($errors)) {
      // Add / Edit comment
      /*
      $fields = [
        'comment' => $data['comment'],
        'user_id' => $data['user_id'],
        'blog_id' => $data['blog_id'],
      ];
      */
      if ($this->commentsTable->save($data) == 0) {
        $errors[] = 'Unable to add comment';
      }
      else {
        header("location: /blog/view?id=$data[blog_id]");
      }
    }

    if (isset($errors)) {
      $errors = serialize($errors);
      header("location: /blog/view?id=$data[blog_id]&errors=$errors");
    }
  }

  public function showReplies()
  {
    $parent_id = $this->helpers->sanitize($_GET['parent_id']);
    $replies = $this->commentsTable->fetchByCol('parent_id', $parent_id);

    $fields = implode(',',
      [
        'blog_id', 'comment', 'comments.id as comment_id', 'name'
      ]);
    $sql = "SELECT $fields FROM comments
      JOIN users ON users.id = comments.user_id
      WHERE comments.parent_id = :parent_id";
    $params = ['parent_id' => $parent_id];
    $blog = $this->commentsTable->query($sql, $params)->fetchAll();
    return [
      'html' => true,
      'template' => 'commentsList.html.php',
      'variables' => [
        'blog' => $blog ?? null
      ]
    ];
  }

  public function replyForm()
  {
    // Fetch current user Id
    $user_id = $this->authentication->getUser()['id'] ?? NULL;

    return [
      'html' => true,
      'template' => 'commentForm.html.php',
      'variables' => [
        'parent_id' => $_GET['parent_id'],
        'blog_id' => $_GET['blog_id'],
        'user_id' => $user_id,
        'type' => 'reply'
      ]
    ];
  }

  public function delete()
  {
    // Delete comment
      // TODO
  }
}
