<?php
namespace Blog\Controllers;
use \Raman\DatabaseTable;
use \Raman\Authentication;
use \Raman\Helpers;

class Blog
{
  private $blogsTable;
  private $commentsTable;
  private $blogsLikesTable;
  private $commentsLikesTable;
  private $authentication;
  private $helpers;

  public function __construct(DatabaseTable $blogsTable,
    DatabaseTable $commentsTable, DatabaseTable $blogsLikesTable,
    DatabaseTable $commentsLikesTable,
    Authentication $authentication)
  {
    $this->blogsTable = $blogsTable;
    $this->commentsTable = $commentsTable;
    $this->blogsLikesTable = $blogsLikesTable;
    $this->commentsLikesTable = $commentsLikesTable;
    $this->authentication = $authentication;
    $this->helpers = new Helpers();
  }

  public function home()
  {
    // Join table
    $reference = [
      'table' => [
        'type' => 'referenced',
        'name' => 'users'
      ],
      'primary' => 'id',
      'foreign' => 'user_id'
    ];

    // Fields to fetch
    $fields = ['blogs.id as id, title, description, user_id,
      created_at, last_updated, name'];

    $blogs = $this->blogsTable->join($reference, $fields);

    return [
      'title' => 'Blogger | Home',
      'template' => 'showBlogs.html.php',
      'variables' => [
        'blogs' => $blogs,
        'user_id' => $this->authentication->getUser()['id'],
        'helpers' => $this->helpers
      ]
    ];
  }

  // Render add/edit blog form
  public function edit()
  {
    // Check if user has permission
      // TODO

    // If it's an edit request
    if (isset($_GET['id'])) {
      // Check if there's a blog in db with that id
      $blog = $this->blogsTable->fetch($_GET['id']);
    }
    return [
      'title' => 'Add Blog',
      'template' => 'addBlog.html.php',
      'variables' => [
        'blog' => isset($blog) ? $blog : NULL
      ]
    ];
  }

  // Add/update blog to database
  public function save()
  {
    // Check if user has permission
      // TODO

    $blog['id'] = htmlspecialchars($_POST['blog']['id']);
    $blog['title'] = htmlspecialchars($_POST['blog']['title']);
    $blog['description'] = htmlspecialchars($_POST['blog']['description']);
    $blog['blog'] = $_POST['blog']['blog'];
    // Fetch current user id
    $blog['user_id'] = $this->authentication->getUser()['id'];

    foreach ($blog as $key => $value) {
      if (in_array($key, ['title', 'description', 'blog'])
        && empty($value))
      {
        $errors[] = ucwords($key) . ' required';
      }
    }

    // If id specified check if blog of that id is there
    if (!isset($errors)
      && (!empty($blog['id'])
        && !$this->blogsTable->fetch($blog['id'])))
    {
      $errors[] = 'No blog found for editing';
    }

    // Save blog to db
    if (!isset($errors) && $this->blogsTable->save($blog) == 0) {
      $errors[] = 'Failed to add blog';
    }

    if (!isset($errors))
    {
      header('location: /');
    }
    else {
      unset($blog['user_id']);
      return [
        'title' => 'Add Blog | errors',
        'template' => 'addBlog.html.php',
        'variables' => [
          'blog' => $blog,
          'errors' => $errors
        ]
      ];
    }
  }

  public function delete()
  {
    // Check if user has permission
      // TODO
    if ($this->blogsTable->delete($_GET['id'])) {
      header('location: /');
    }
    else {
      return [
        'title' => 'Blogger | errors',
        'output' => 'Failed to delete Blog'
      ];
    }
  }

  public function upload()
  {
    return [
      'file' => 'upload.php'
    ];
  }

  private function fetchLikes($blog_id) {
    // Fetch Number of likes
    $sql = 'SELECT COUNT(user_id) as likes FROM blogs_likes
      WHERE blog_id = :blog_id';
    $params = [
      'blog_id' => $blog_id
    ];
    return $this->blogsLikesTable->query($sql, $params)->fetch()['likes'];
  }

  public function view()
  {
    $id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : NULL;

    if (isset($id)) {
      // Fetch current user Id
      $userId = $this->authentication->getUser()['id'] ?? NULL;

      // Fetch blog and user info
      $fields = implode(',',
        [
          'blogs.id as blog_id', 'blog', 'title', 'description',
          'users.name as author', 'user_id'
        ]);
      $sql = "SELECT $fields FROM blogs JOIN users
        ON blogs.user_id = users.id
        WHERE blogs.id = :blog_id";
      $params = ['blog_id' => $id];
      $blog = $this->blogsTable->query($sql, $params)->fetch();

      if (!$blog) {
        $errors[] = 'Blog not found';
      }
      else {
        // Fetch comments
        $subfields = implode(',',
          [
            'A.comment', 'A.id as comment_id', 'A.blog_id',
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
            SELECT $subfields FROM comments as A
              LEFT JOIN comments as B
              ON A.id = B.parent_id
              JOIN users as U ON A.user_id = U.id
                WHERE A.blog_id = :blog_id
                AND A.parent_id IS NULL
                GROUP BY(A.id)
          ) as T
          ON C.comment_id = T.comment_id
          GROUP BY (T.comment_id)";

        $params = ['blog_id' => $id];
        $comments = $this->commentsTable->query($sql, $params)->fetchAll();

        // Check if current user has liked this blog
        $sql = "SELECT * FROM blogs_likes
          WHERE user_id = :user_id
          AND blog_id = :blog_id";
        $params = [
          'user_id' => $userId,
          'blog_id' => $id
        ];
        $liked =
          isset($this->blogsLikesTable->
            query($sql, $params)->
              fetch()['user_id'])
          ? true : false;

        // Fetch Number of likes
        $likes = $this->fetchLikes($id);

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
      }
    } else {
      $errors[] = 'Invalid request';
    }

    if (!isset($errors) && isset($_GET['errors'])) {
      $errors = unserialize($_GET['errors']);
    }

    return [
      'title' => $blog['title'] ?? null,
      'template' => 'blog.html.php',
      'variables' => [
        'blog' => $blog ?? null,
        'comments' => $comments ?? null,
        'errors' => $errors ?? null,
        'user_id' => $userId,
        'liked' => $liked ?? false,
        'liked_comments' => $liked_comments,
        'blog_likes' => $likes
      ]
    ];
  }

  public function like()
  {
    // Fetch blog id
    $blog_id = $_POST['blog_id'];
    // Check if liking or unliking
    $unlike = $_POST['unlike'] == 'true' ? true : false;
    // Check if blog present
    $blog = $this->blogsTable->fetch($blog_id);
    if ($blog) {
      // Fetch current user
      $user_id = $this->authentication->getUser()['id'];
      // User can't like his own blog
      if ($user_id == $blog['user_id']) {
        $error = [
          'msg' => 'Cannot ' . ($unlike ? 'unlike' : 'like') . ' your own blog',
          'code' => 403
        ];
      }
      // Like / Unlike blog
      else {
        $fields = [
          'user_id' => $user_id,
          'blog_id' => $blog_id
        ];

        $action = $unlike ? 'deleteByCols' : 'save';
        if ($this->blogsLikesTable->$action($fields)) {
          // Fetch number of likes
          $likes = $this->fetchLikes($blog_id);

          return [
            'json' => [
              'user_id' => $user_id,
              'blog_id' => $blog_id,
              'blog_likes' => $likes
            ]
          ];
        }
        else {
          $error = [
            'msg' => 'Unable to ' . ($unlike ? 'unlike' : 'like') . ' blog',
            'code' => 403
          ];
        }
      }
    }

    // Errors
    if (!isset($error)) {
      $error = [
        'msg' => 'Blog not found / Invalid request',
        'code' => '404'
      ];
    }

    return [
      'json' => [
        'error' => $error
      ]
    ];
  }
}
