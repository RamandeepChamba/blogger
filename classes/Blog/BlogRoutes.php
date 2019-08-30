<?php
namespace Blog;
use \Raman\DatabaseTable;
use \Raman\Routes;
use \Raman\Authentication;
use \Blog\Controllers\Blog;
use \Blog\Controllers\Comment;
use \Blog\Controllers\User;
use \Blog\Controllers\Register;
use \Blog\Controllers\Login;

class BlogRoutes implements Routes
{

  public function __construct()
  {
    include __DIR__ . '/../../includes/connection.php';

    $this->blogsTable = new DatabaseTable($pdo, 'blogs', 'id');
    $this->usersTable = new DatabaseTable($pdo, 'users', 'id');
    $this->commentsTable = new DatabaseTable($pdo, 'comments', 'id');
    $this->followersTable = new DatabaseTable($pdo, 'followers', 'user_id');
    $this->blogsLikesTable = new DatabaseTable($pdo, 'blogs_likes', 'blog_id');
    $this->commentsLikesTable = new DatabaseTable($pdo, 'comments_likes', 'comment_id');
    $this->authentication = new Authentication($this->usersTable, 'name', 'password');
  }

  public function getRoutes(): array
  {
    $blogController = new Blog($this->blogsTable, $this->commentsTable,
      $this->blogsLikesTable, $this->commentsLikesTable,
      $this->authentication);
    $userController = new User($this->usersTable, $this->blogsTable,
      $this->followersTable, $this->authentication);
    $commentController = new Comment($this->commentsTable,
      $this->commentsLikesTable, $this->authentication);
    $registerController = new Register($this->usersTable, $this->authentication);
    $loginController = new Login($this->authentication);

    // Routing
    $routes = [
      // Profile
      'user' => [
        'GET' => [
          'controller' => $userController,
          'action' => 'profile'
        ]
      ],
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
      // Follow
      'user/follow' => [
        'GET' => [
          'controller' => $userController,
          'action' => 'follow'
        ],
        'login' => true
      ],
      // Followers
      'user/followers' => [
        'GET' => [
          'controller' => $userController,
          'action' => 'fetchFollowers'
        ]
      ],
      // Following
      'user/following' => [
        'GET' => [
          'controller' => $userController,
          'action' => 'fetchFollowing'
        ]
      ],
      // Logout
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
      'blog/like' => [
        'POST' => [
          'controller' => $blogController,
          'action' => 'like'
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
      'blog/comment/add' => [
        'POST' => [
          'controller' => $commentController,
          'action' => 'save'
        ],
        'login' => true
      ],
      'blog/comment/delete' => [
        'POST' => [
          'controller' => $commentController,
          'action' => 'delete'
        ],
        'login' => true
      ],
      'blog/comment/edit' => [
        'GET' => [
          'controller' => $commentController,
          'action' => 'edit'
        ],
        'login' => true
      ],
      'blog/comment/like' => [
        'POST' => [
          'controller' => $commentController,
          'action' => 'like'
        ],
        'login' => true
      ],
      'blog/comment/showReplies' => [
        'GET' => [
          'controller' => $commentController,
          'action' => 'showReplies'
        ]
      ],
      'blog/comment/reply' => [
        'GET' => [
          'controller' => $commentController,
          'action' => 'replyForm'
        ],
        'POST' => [
          'controller' => $commentController,
          'action' => 'reply'
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

  public function getAuthentication(): Authentication
  {
    return $this->authentication;
  }
}
