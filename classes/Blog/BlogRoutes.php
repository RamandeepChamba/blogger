<?php
namespace Blog;
use \Raman\DatabaseTable;
use \Raman\Routes;
use \Blog\Controllers\Blog;
use \Blog\Controllers\Register;
use \Blog\Controllers\Login;

class BlogRoutes implements Routes
{

  private $blogsTable;
  private $usersTable;
  private $registerController;
  private $loginController;

  public function __construct()
  {
    include __DIR__ . '/../../includes/connection.php';

    $this->blogsTable = new DatabaseTable($pdo, 'blogs', 'id');
    $this->usersTable = new DatabaseTable($pdo, 'users', 'id');
    $this->blogController = new Blog($this->blogsTable);
    $this->registerController = new Register($this->usersTable);
    $this->loginController = new Login($this->usersTable);
  }

  public function getRoutes(): array
  {
    // Routing
    $routes = [
      // Register
      'user/register' => [
        'GET' => [
          'controller' => $this->registerController,
          'action' => 'registerForm'
        ],
        'POST' => [
          'controller' => $this->registerController,
          'action' => 'register'
        ]
      ],
      // Login
      'user/login' => [
        'GET' => [
          'controller' => $this->loginController,
          'action' => 'loginForm'
        ],
        'POST' => [
          'controller' => $this->loginController,
          'action' => 'login'
        ]
      ],
      // Blog
      'blog/add' => [
        'GET' => [
          'controller' => $this->blogController,
          'action' => 'edit'
        ],
        'POST' => [
          'controller' => $this->blogController,
          'action' => 'save'
        ]
      ],
      'blog/delete' => [
        'GET' => [
          'controller' => $this->blogController,
          'action' => 'delete'
        ]
      ],
      'blog/upload' => [
        'POST' => [
          'controller' => $this->blogController,
          'action' => 'upload'
        ]
      ],
      '' => [
        'GET' => [
          'controller' => $this->blogController,
          'action' => 'home'
        ]
      ]
    ];

    return $routes;
  }
}
