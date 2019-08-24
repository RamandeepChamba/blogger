<?php

namespace Blog\Controllers;
use \Raman\Authentication;
use \Raman\DatabaseTable;
use \Raman\Helpers;

class User
{
  function __construct(DatabaseTable $usersTable, DatabaseTable $blogsTable,
    Authentication $authentication)
  {
    $this->usersTable = $usersTable;
    $this->blogsTable = $blogsTable;
    $this->authentication = $authentication;
    $this->helpers = new Helpers();
  }

  private function fetchBlogs($user_id)
  {
    $blogs = $this->blogsTable->fetchByCol('user_id', $user_id);
    return $blogs;
  }

  public function profile()
  {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
      // Fetch user's profile
      $user = $this->usersTable->fetch($_GET['id']);

      if (!$user) {
        header("HTTP/1.1 404 Profile not found.");
      }

      // Fetch user's blogs
      $blogs = $this->fetchBlogs($_GET['id']);

      return [
        'title' => $user['name'],
        'template' => 'user.html.php',
        'variables' => [
          'user' => $user,
          'blogs' => $blogs,
          'user_id' => $this->authentication->getUser()['id'],
          'helpers' => $this->helpers
        ]
      ];
    }
    else {
      header("HTTP/1.1 400 Invalid request.");
    }
  }
}
