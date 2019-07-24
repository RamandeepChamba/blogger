<?php

namespace Raman;
use \Raman\DatabaseTable;

class Authentication
{

  private $usersTable;

  function __construct(DatabaseTable $usersTable)
  {
    $this->usersTable = $usersTable;
  }
}
