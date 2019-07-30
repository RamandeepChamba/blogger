<?php
namespace Raman;

class Authentication {
  private $users;
  private $usernameColumn;
  private $passwordColumn;

  public function __construct(DatabaseTable $users,
    $usernameColumn, $passwordColumn)
  {
    session_start();
    $this->users = $users;
    $this->usernameColumn = $usernameColumn;
    $this->passwordColumn = $passwordColumn;
  }

  public function login($username, $password)
  {
    $user = $this->users->fetchByCol(
      $this->usernameColumn, strtolower($username)
    );

    if (!empty($user) && password_verify(
      $password, $user[0][$this->passwordColumn]))
    {
      // Login successful
      session_regenerate_id();
      $_SESSION['username'] = $user[0][$this->usernameColumn];
      $_SESSION['password'] = $user[0][$this->passwordColumn];
      return true;

    } else {
      // Error / Wrong credentials
      return false;
    }
  }

  public function isLoggedIn()
  {
    if (empty($_SESSION['username'])) {
      return false;
    }

    $user = $this->users->fetchByCol(
      $this->usernameColumn,
      strtolower($_SESSION['username'])
    );

    if (!empty($user) &&
      $_SESSION['password'] ===
      $user[0][$this->passwordColumn])
    {
      return true;

    } else {
      return false;
    }
  }

  public function getUser()
  {
    if ($this->isLoggedIn()) {
      return $this->users->fetchByCol($this->usernameColumn,
        strtolower($_SESSION['username']))[0];

    } else {
      return false;
    }
  }
}
