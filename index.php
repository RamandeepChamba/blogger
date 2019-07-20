<?php

try {
  require __DIR__ . '/vendor/autoload.php';

  $route = ltrim(strtok($_SERVER['REQUEST_URI'], '?'), '/');
  $entryPoint = new \Raman\EntryPoint(
    $route,
    $_SERVER['REQUEST_METHOD'],
    new \Blog\BlogRoutes());
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
