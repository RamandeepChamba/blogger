<?php
namespace Raman;

class EntryPoint
{

  public function __construct(string $route, string $method, Routes $routes)
  {
    $this->route = $route;
    $this->method = $method;
    $this->routes = $routes;
  }

  private function loadTemplate($template, $variables = [])
  {
    extract($variables);
    ob_start();
    include __DIR__ . '/../../templates/' . $template;
    return ob_get_clean();
  }

  public function run()
  {
    $routes = $this->routes->getRoutes();
    // Default to home if route not in routes
    if (!isset($routes[$this->route])) {
      $this->route = '';
      $this->method = 'GET';
    }

    // Error if page requires login but not logged in
    if (isset($routes[$this->route]['login'])
      && !$this->routes->getAuthentication()->isLoggedIn())
    {
      header('HTTP/1.1 403 Forbidden');
      return;
    }

    // Run action from particular controller according to route & method
    $controller = $routes[$this->route][$this->method]['controller'];
    $action = $routes[$this->route][$this->method]['action'];
    $page = $controller->$action();
    $title = $page['title'] ?? 'Untitled';

    if (isset($page['template']) && !isset($page['html'])) {
      if (isset($page['variables'])) {
        $output = $this->loadTemplate($page['template'], $page['variables']);
      } else {
        $output = $this->loadTemplate($page['template']);
      }

      echo $this->loadTemplate('layout.html.php', [
        'output' => $output,
        'title' => $title,
        'loggedIn' => $this->routes->getAuthentication()->isLoggedIn()
      ]);

    } else if (isset($page['output'])) {
      $output = $page['output'];

      echo $this->loadTemplate('layout.html.php', [
        'output' => $output,
        'title' => $title,
        'loggedIn' => $this->routes->getAuthentication()->isLoggedIn()
      ]);

    } else if (isset($page['file'])) {
      include __DIR__ . '/../../includes/' . $page['file'];

    } else if (isset($page['json'])) {
      echo json_encode($page['json']);

    } else if (isset($page['html'])) {
      echo $this->loadTemplate($page['template'], $page['variables']);
    }
  }
}
