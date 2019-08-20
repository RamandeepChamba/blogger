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

      if (empty($data['parent_id'])) {
        $data['parent_id'] = NULL;
      }

      if (isset($data['comment_id'])) {
        $data['id'] = $data['comment_id'];
        unset($data['comment_id']);
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
    $userId = $this->authentication->getUser()['id'] ?? NULL;
    $parent_id = $this->helpers->sanitize($_GET['parent_id']);
    $replies = $this->commentsTable->fetchByCol('parent_id', $parent_id);

    $fields = implode(',',
      [
        'blog_id', 'comment', 'comments.id as comment_id',
        'users.name as author', 'users.id as user_id'
      ]);
    $sql = "SELECT $fields FROM comments
      JOIN users ON users.id = comments.user_id
      WHERE comments.parent_id = :parent_id";
    $params = ['parent_id' => $parent_id];
    $comments = $this->commentsTable->query($sql, $params)->fetchAll();
    return [
      'html' => true,
      'template' => 'commentsList.html.php',
      'variables' => [
        'comments' => $comments ?? null,
        'user_id' => $userId,
        'replies' => true 
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
    $id = $this->helpers->sanitize($_POST['comment_id']);
    $blogId = $this->helpers->sanitize($_POST['blog_id']);
    $userId = $this->authentication->getUser()['id'] ?? NULL;

    // Check if current user is the author of comment
    $comment = $this->commentsTable->fetchByCol('id', $id);

    if (!$comment) {
      $errors[] = 'Comment not found';
    }

    if (!isset($errors) && $comment[0]['user_id'] != $userId) {
      $errors[] = 'Permission denied';
    }

    // Delete comment
    if (!$this->commentsTable->delete($id)) {
      $errors[] = 'Unable to delete comment';
    }

    if (isset($errors)) {
      // Display Error
      $errors = serialize($errors);
      header("location: /blog/view?id=$blogId&errors=$errors");
    }
    else {
      header("location: /blog/view?id=$blogId");
    }
  }

  public function edit()
  {
    $user_id = $this->authentication->getUser()['id'] ?? NULL;
    $comment_id = $this->helpers->sanitize($_GET['comment_id']);
    // Fetch comment
    $comment = $this->commentsTable->fetch($comment_id);

    return [
      'html' => true,
      'template' => 'commentForm.html.php',
      'variables' => [
        'comment' => $comment,
        'user_id' => $user_id,
        'type' => 'edit_comment'
      ]
    ];
  }
}
