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
    ),
    array(
        'name' => 'users-authenticate',
        'route' => '/accounts/login/',
        'module' => 'Users',
        'controller' => 'Index',
        'action' => 'login',
        'layout' => 'Default'
    ),
    array(
        'name' => 'users-register',
        'route' => '/accounts/create/',
        'module' => 'Users',
        'controller' => 'Index',
        'action' => 'register',
        'layout' => 'Default'
    ),
    array(
        'name' => 'user-profile',
        'route' => '/accounts/me/',
        'module' => 'Users',
        'controller' => 'Index',
        'action' => 'profile',
        'layout' => 'Default'
    )
);

foreach ($routes as $route) {
    Router::addRoute($route);
}
