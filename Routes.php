<?php
use Fennec\Library\Router;

$routes = array(
    array(
        'name' => 'admin-users-list',
        'route' => '/admin/users/',
        'module' => 'Users',
        'controller' => 'Admin\\Index',
        'action' => 'index',
        'layout' => 'Admin/Default'
    ),
    array(
        'name' => 'admin-user-create',
        'route' => '/admin/user/create/',
        'module' => 'Users',
        'controller' => 'Admin\\Index',
        'action' => 'create',
        'layout' => 'Admin/Default'
    ),
    array(
        'name' => 'admin-user-edit',
        'route' => '/admin/user/edit/([0-9]+)/',
        'params' => array(
            'id'
        ),
        'module' => 'Users',
        'controller' => 'Admin\\Index',
        'action' => 'create',
        'layout' => 'Admin/Default'
    )
);

foreach ($routes as $route) {
    Router::addRoute($route);
}
