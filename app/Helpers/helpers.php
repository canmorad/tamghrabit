<?php
use App\Core\Router;
function basePath($path)
{
    return BASE_PATH . $path;
}

function url($path = '')
{
    return BASE_URL . ltrim($path, '/');
}

function route($name)
{
    return Router::getRouter()->route($name);
}
