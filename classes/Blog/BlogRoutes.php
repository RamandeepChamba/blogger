<?php
namespace Blog;
use \Raman\DatabaseTable;
use \Raman\Routes;
use \Raman\Authentication;
use \Blog\Controllers\Blog;
use \Blog\Controllers\Register;
use \Blog\Controllers\Login;

class BlogRoutes implements Routes
{

  private $blogsTable;
  private $usersTable;
  private $authentication;

  public function __construct()
  {
    include __DIR__ . '/../../includes/connection.php';

    $this->blogsTable = new DatabaseTable($pdo, 'blogs', 'id');
    $this->usersTable = new DatabaseTable($pdo, 'users', 'id');
    $this->authentication = new Authentication($this->usersTable, 'name', 'password');
  }

  public function getRoutes(): array
  {
    $blogController = new Blog($this->blogsTable, $this->authentication);
    $registerController = new Register($this->usersTable, $this->authentication);
    $loginController = new Login($this->authentication);

    // Routing
    $routes = [
      // Register
      'user/register' => [
        'GET' => [
          'controller' => $registerController,
          'action' => 'registerForm'
        ],
        'POST' => [
          'controller' => $registerController,
          'action' => 'register'
        ]
      ],
      // Login
      'user/login' => [
        'GET' => [
          'controller' => $loginController,
          'action' => 'loginForm'
        ],
        'POST' => [
          'controller' => $loginController,
          'action' => 'login'
        ]
      ],
      'user/logout' => [
        'GET' => [
          'controller' => $loginController,
          'action' => 'logout'
        ],
        'login' => true
      ],
      // Blog
      'blog/add' => [
        'GET' => [
          'controller' => $blogController,
          'action' => 'edit'
        ],
        'POST' => [
          'controller' => $blogController,
          'action' => 'save'
        ],
        'login' => true
      ],
      'blog/view' => [
        'GET' => [
          'controller' => $blogController,
          'action' => 'view'
        ]
      ],
      'blog/delete' => [
        'GET' => [
          'controller' => $blogController,
          'action' => 'delete'
        ],
        'login' => true
      ],
      'blog/upload' => [
        'POST' => [
          'controller' => $blogController,
          'action' => 'upload'
        ],
        'login' => true
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

  public function getAuthentication(): Authentication
  {
    return $this->authentication;
  }
}
