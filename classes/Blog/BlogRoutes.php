<?php
namespace Blog;
use \Raman\DatabaseTable;
use \Raman\Routes;
use \Blog\Controllers\Blog;

class BlogRoutes implements Routes
{

  private $blogsTable;

  public function __construct()
  {
    include __DIR__ . '/../../includes/connection.php';

    $this->blogsTable = new DatabaseTable($pdo, 'blogs', 'id');
  }

  public function getRoutes(): array
  {
    $blogController = new Blog($this->blogsTable);

    // Routing
    $routes = [
      // Blog
      'blog/add' => [
        'GET' => [
          'controller' => $blogController,
          'action' => 'edit'
        ],
        'POST' => [
          'controller' => $blogController,
          'action' => 'save'
        ]
      ],
      'blog/delete' => [
        'GET' => [
          'controller' => $blogController,
          'action' => 'delete'
        ]
      ],
      'blog/upload' => [
        'POST' => [
          'controller' => $blogController,
          'action' => 'upload'
        ]
      ],
      '' => [
        'GET' => [
          'controller' => $blogController,
          'action' => 'home'
        ]
      ]
    ];

    return $routes;
  }
}
