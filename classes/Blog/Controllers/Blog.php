<?php
namespace Blog\Controllers;
use \Raman\DatabaseTable;

class Blog
{
  private $blogsTable;

  public function __construct(DatabaseTable $blogsTable)
  {
    $this->blogsTable = $blogsTable;
  }

  public function home()
  {
    $blogs = $this->blogsTable->fetchAll();

    return [
      'title' => 'Blogger | Home',
      'template' => 'showBlogs.html.php',
      'variables' => [
        'blogs' => $blogs
      ]
    ];
  }

  // Render add/edit blog form
  public function edit()
  {
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
    $blog['id'] = htmlspecialchars($_POST['blog']['id']);
    $blog['blog'] = htmlspecialchars($_POST['blog']['blog']);

    // If id specified check if blog of that id is there
    if (strlen($blog['blog']) !== 0
      && ((strlen($blog['id']) == 0)
        || (strlen($blog['id']) !== 0
          && $this->blogsTable->fetch($blog['id'])))
      && $this->blogsTable->save($blog))
    {
      header('location: /');
    }
    else {
      return [
        'title' => 'Blogger | Error',
        'output' => 'Failed to add Blog'
      ];
    }
  }

  public function delete()
  {
    if ($this->blogsTable->delete($_GET['id'])) {
      header('location: /');
    }
    else {
      return [
        'title' => 'Blogger | Error',
        'output' => 'Failed to delete Blog'
      ];
    }
  }
}
