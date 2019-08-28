<?php

namespace Blog\Controllers;
use \Raman\Authentication;
use \Raman\DatabaseTable;
use \Raman\Helpers;

class User
{
  function __construct(
    DatabaseTable $usersTable, DatabaseTable $blogsTable,
    DatabaseTable $followersTable,
    Authentication $authentication)
  {
    $this->usersTable = $usersTable;
    $this->blogsTable = $blogsTable;
    $this->followersTable = $followersTable;
    $this->authentication = $authentication;
    $this->helpers = new Helpers();
  }

  private function fetchBlogs($user_id)
  {
    $blogs = $this->blogsTable->fetchByCol('user_id', $user_id);
    return $blogs;
  }

  // Fetch followers
  public function fetchFollowers($user_id = NULL)
  {
    if (isset($user_id)) {
      $followers = $this->followersTable->fetchByCol('user_id', $user_id);
      return $followers;
    }

    $user_id = $_GET['id'];

    $fields = implode(',', [
      'U.id', 'U.name'
    ]);

    $sql = "SELECT $fields FROM followers as F
      JOIN users as U ON F.follower_id = U.id
      WHERE F.user_id = :user_id";

    $params = [
      'user_id' => $user_id
    ];

    $followers = $this->followersTable->query($sql, $params)->fetchAll();

    // Return HTML for ajax
    return [
      'html' => true,
      'template' => 'followList.html.php',
      'variables' => [
        'followers' => $followers
      ]
    ];
  }

  // Fetch following
  public function fetchFollowing($user_id = NULL)
  {
    if (isset($user_id)) {
      $following = $this->followersTable->fetchByCol('follower_id', $user_id);
      return $following;
    }

    $user_id = $_GET['id'];

    $fields = implode(',', [
      'U.id', 'U.name'
    ]);

    $sql = "SELECT $fields FROM followers as F
      JOIN users as U ON F.user_id = U.id
      WHERE F.follower_id = :user_id";

    $params = [
      'user_id' => $user_id
    ];

    $following = $this->followersTable->query($sql, $params)->fetchAll();

    // Return HTML for ajax
    return [
      'html' => true,
      'template' => 'followList.html.php',
      'variables' => [
        'following' => $following
      ]
    ];
  }

  public function profile($user_id = NULL)
  {
    $user_id = $user_id ?? $_GET['id'];

    if (isset($user_id) && !empty($user_id)) {
      // Fetch user's profile
      $user = $this->usersTable->fetch($user_id);

      // Fetch current user
      $cur_user = $this->authentication->getUser();

      if (!$user) {
        header("HTTP/1.1 404 Profile not found.");
      }

      // Check if current user is following this user
      if ($cur_user['id'] == $user['id']) {
        $following = false;
      } else {
        $sql = "SELECT * FROM followers
          WHERE user_id = :user_id AND follower_id = :follower_id";
        $params = [
          'user_id' => $user['id'],
          'follower_id' => $cur_user['id'],
        ];

        $following = $this->followersTable->query($sql, $params)->fetch()
          ? true : false;
      }

      // Fetch user's blogs
      $blogs = $this->fetchBlogs($user_id);

      // Fetch followers
      $user['followers'] = count($this->fetchFollowers($user_id));
      $user['following'] = count($this->fetchFollowing($user_id));
      // Fetch following

      return [
        'title' => $user['name'],
        'template' => 'user.html.php',
        'variables' => [
          'user' => $user,
          'blogs' => $blogs,
          'user_id' => $cur_user['id'],
          'helpers' => $this->helpers,
          'following' => $following
        ]
      ];
    }
    else {
      header("HTTP/1.1 400 Invalid request.");
    }
  }

  public function follow()
  {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
      // Fetch user to follow
      $user = $this->usersTable->fetch($_GET['id']);

      // Check if user to follow exist
      if (!$user) {
        header("HTTP/1.1 404 Profile not found.");
      }

      // Get current user id
      $cur_user = $this->authentication->getUser();

      if (!$cur_user) {
        header('HTTP/1.1 403 Forbidden');
      }

      $fields = [
        'user_id' => $user['id'],
        'follower_id' => $cur_user['id']
      ];

      // Add current user as a follower to user
      if ($this->followersTable->save($fields)) {
        return $this->profile($_GET['id']);
      }

      // Following failed
      header('HTTP/1.1 403 Forbidden');
    }
  }
}
