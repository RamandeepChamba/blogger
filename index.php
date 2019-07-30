<?php

use \Raman\EntryPoint;
use \Blog\BlogRoutes;

try {
  require __DIR__ . '/vendor/autoload.php';

  $route = ltrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
  $entryPoint = new EntryPoint(
    $route,
    $_SERVER['REQUEST_METHOD'],
    new BlogRoutes());
  $entryPoint->run();
}
catch (\PDOException $e) {
  $title = 'Blogger | Error';
  $output =
    $e->getMessage() . ' in ' .
    $e->getFile() . ':' . $e->getLine();

  include __DIR__ . '/templates/layout.html.php';
  // Upload branch
}
