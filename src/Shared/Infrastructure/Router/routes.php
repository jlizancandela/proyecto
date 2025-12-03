<?php

use FastRoute\RouteCollector;

return function (RouteCollector $r) {
    $r->addRoute("GET", "/", "HomeController@index");

    $r->addRoute("GET", "/login", "AuthController@showLogin");
    $r->addRoute("POST", "/login", "AuthController@login");
    $r->addRoute("GET", "/register", "AuthController@showRegister");
    $r->addRoute("POST", "/register", "AuthController@register");
    $r->addRoute("GET", "/logout", "AuthController@logout");
};
