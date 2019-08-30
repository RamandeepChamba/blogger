<?php
namespace Blog\Controllers;
use \Raman\DatabaseTable;
use \Raman\Authentication;
use \Raman\Helpers;

class Comment
{
  private $commentsTable;
  private $commentsLikesTable;
  private $authentication;
  private $helpers;

  public function __construct(DatabaseTable $commentsTable,
    DatabaseTable $commentsLikesTable,
    Authentication $authentication)
  {
    $this->commentsTable = $commentsTable;
    $this->commentsLikesTable = $commentsLikesTable;
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

    $subfields = implode(',',
      [
        'A.blog_id', 'A.comment', 'A.id as comment_id',
        'COUNT(B.id) as replies',
        'U.name as author', 'U.id as user_id'
      ]);
    $fields = implode(',',
      [
        'comment', 'T.comment_id', 'blog_id',
        'replies', 'COUNT(C.user_id) as likes',
        'author', 'T.user_id'
      ]);
    $sql = "SELECT $fields FROM comments_likes as C
      RIGHT JOIN (
        SELECT $subfields FROM comments AS A
          LEFT JOIN comments AS B ON A.id = B.parent_id
          JOIN users as U ON U.id = A.user_id
        	WHERE A.parent_id = :parent_id
          GROUP BY(A.id)
      ) as T
      ON C.comment_id = T.comment_id
      GROUP BY (T.comment_id)";

    $params = ['parent_id' => $parent_id];
    $comments = $this->commentsTable->query($sql, $params)->fetchAll();

    // Fetch all comments that current user has liked
    $sql = "SELECT comment_id FROM comments_likes
      WHERE user_id = :user_id";

    $params = [
      'user_id' => $userId
    ];
    $user_liked_comments = $this->commentsLikesTable->
      query($sql, $params)->
      fetchAll();

    $liked_comments = array_map(function ($record)
    {
      return $record['comment_id'];
    }, $user_liked_comments);

    return [
      'html' => true,
      'template' => 'commentsList.html.php',
      'variables' => [
        'comments' => $comments ?? null,
        'user_id' => $userId,
        'liked_comments' => $liked_comments,
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

  private function fetchLikes($comment_id) {
    // Fetch Number of likes
    $sql = 'SELECT COUNT(user_id) as likes FROM comments_likes
      WHERE comment_id = :comment_id';
    $params = [
      'comment_id' => $comment_id
    ];
    return $this->commentsLikesTable->query($sql, $params)->fetch()['likes'];
  }

  public function like()
  {
    // Fetch comment id
    $comment_id = $_POST['comment_id'];
    // Check if liking or unliking
    $unlike = $_POST['unlike'] == 'true' ? true : false;
    // Check if comment present
    $comment = $this->commentsTable->fetch($comment_id);
    if ($comment) {
      // Fetch current user
      $user_id = $this->authentication->getUser()['id'];
      // User can't like his own comment
      if ($user_id == $comment['user_id']) {
        $error = [
          'msg' => 'Cannot ' . ($unlike ? 'unlike' : 'like') . ' your own comment',
          'code' => 403
        ];
      }
      // Like / Unlike comment
      else {
        $fields = [
          'user_id' => $user_id,
          'comment_id' => $comment['id']
        ];

        $action = $unlike ? 'deleteByCols' : 'save';
        if ($this->commentsLikesTable->$action($fields)) {
          // Fetch number of likes
          $likes = $this->fetchLikes($comment_id);

          return [
            'json' => [
              'comment_id' => $comment_id,
              'comment_likes' => $likes
            ]
          ];
        }
        else {
          $error = [
            'msg' => 'Unable to ' . ($unlike ? 'unlike' : 'like') . ' comment',
            'code' => 403
          ];
        }
      }
    }

    // Errors
    if (!isset($error)) {
      $error = [
        'msg' => 'Comment not found / Invalid request',
        'code' => 404
      ];
    }

    return [
      'json' => [
        'error' => $error
      ]
    ];
  }
}
