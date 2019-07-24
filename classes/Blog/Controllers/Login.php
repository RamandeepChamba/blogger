<?php

namespace Blog\Controllers;
use \Raman\DatabaseTable;
use \Raman\Helpers;

class Login
{

  private $usersTable;
  private $helpers;

  function __construct(DatabaseTable $usersTable)
  {
    $this->usersTable = $usersTable;
    $this->helpers = new Helpers();
  }

  // Registeration form
  public function loginForm()
  {
    return [
      'title' => 'Login',
      'template' => 'login.html.php'
    ];
  }

  public function login()
  {
    if (isset($_POST['login'])) {
      // Sanitize user data
      $user = array_map(array($this->helpers, 'sanitize'), $_POST['user']);

      // Check if values null / empty
      foreach ($user as $key => $val) {
        if (!$val) {
          $errors[] = ucwords($key) . ' required';
        }
      }

      // Login
      if (!isset($errors))
      {
        // Fetch user (if any)
        $fetched_user = $this->usersTable->fetchByCol('name', $user['name']);

        if (count($fetched_user)
          && password_verify($user['password'], $fetched_user[0]['password']))
        {

        }
        else {
          $errors[] = 'Invalid Username / Password';
        }
      }

      if (isset($errors)) {
        return [
          'title' => 'Login | Error',
          'template' => 'login.html.php',
          'variables' => [
            'errors' => $errors,
            'username' => $user['name']
          ]
        ];
      }
    }
    else {
      header('HTTP/1.1 403 Forbidden');
    }
  }
}
