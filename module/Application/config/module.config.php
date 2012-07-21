<?php
use Zend\Mvc\Router\Http;

return array(
    'di' => array(
        'instance' => array(
            $router = new Http\TreeRouteStack();
            $route = new Http\LiteralRoute(
                '/',
                array('controller' => 'index')
            );
            $router->addRoute($route);
        )
    )
);
