<?php

namespace Blog\Controllers;
use \Raman\DatabaseTable;
use \Raman\Helpers;

class Register
{

  private $usersTable;
  private $helpers;

  function __construct(DatabaseTable $usersTable)
  {
    $this->usersTable = $usersTable;
    $this->helpers = new Helpers();
  }

  // Registeration form
  public function registerForm()
  {
    return [
      'title' => 'Registration',
      'template' => 'register.html.php'
    ];
  }

  public function register()
  {
    if (isset($_POST['register'])) {
      // Sanitize user data
      $user = array_map(array($this->helpers, 'sanitize'), $_POST['user']);

      // Check if values null / empty
      foreach ($user as $key => $val) {
        if (!$val) {
          if ($key == 'cpassword') {
            continue;
          }
          $errors[] = ucwords($key) . ' required';
        }
      }

      // Check if username available
      if (!isset($errors)) {
        count($this->usersTable->fetchByCol('name', $user['name']))
          ? $errors[] = 'Username not available'
          : null;
      }

      // Check password confirmed
      if (!isset($errors) && $user['password'] != $user['cpassword']) {
        $errors[] = 'Password confirmation failed';
      }

      // Register
      if (!isset($errors))
      {
        // Add user to db
        if(count($this->usersTable->save([
          'name' => $user['name'],
          'password' => password_hash($user['password'], PASSWORD_DEFAULT)
        ])))
        {
          header('location: /');
        }
        else {
          $errors[] = 'Registration Failed';
        }
      }

      if (isset($errors)) {
        return [
          'title' => 'Registration | Error',
          'template' => 'register.html.php',
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
