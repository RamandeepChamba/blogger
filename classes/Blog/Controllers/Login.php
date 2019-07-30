<?php

namespace Blog\Controllers;
use \Raman\DatabaseTable;
use \Raman\Authentication;
use \Raman\Helpers;

class Login
{

  private $authentication;
  private $helpers;

  function __construct(Authentication $authentication)
  {
    $this->authentication = $authentication;
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
        if ($this->authentication->login($user['name'], $user['password']))
        {
          header('location: /');
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

  public function logout()
  {
    session_destroy();
    header('location: /');
  }
}
