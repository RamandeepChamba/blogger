<?php
namespace Blog\Controllers;
use \Raman\DatabaseTable;
use \Raman\Authentication;
use \Raman\Helpers;

class Blog
{
  private $blogsTable;
  private $commentsTable;
  private $authentication;
  private $helpers;

  public function __construct(DatabaseTable $blogsTable,
    DatabaseTable $commentsTable, Authentication $authentication)
  {
    $this->blogsTable = $blogsTable;
    $this->commentsTable = $commentsTable;
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

      // Fetch comments
      $fields = implode(',',
        [
          'comment', 'comments.id as comment_id', 'blog_id',
          'users.name as author', 'users.id as user_id'
        ]);
      $sql = "SELECT $fields FROM comments JOIN users
        ON comments.user_id = users.id
        WHERE comments.blog_id = :blog_id
        AND comments.parent_id IS NULL";
      $params = ['blog_id' => $id];
      $comments = $this->commentsTable->query($sql, $params)->fetchAll();

      if (!$blog) {
        $errors[] = 'Blog not found';
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
        'user_id' => $userId
      ]
    ];
  }
}
