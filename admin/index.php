<?php 
require_once realpath(dirname(__FILE__)). '/../kernel/Router.php';
$router = Router::getInstance('admin.index');

$router->setUrls(array(
    'img' => 'admin.img',
    'css' => 'admin.css',
    'js' => 'admin.js',
    'packs' => 'admin.packs',
    'show' => 'admin.show',
    'add' => 'admin.add',
    'doadd' => 'admin.do_add',
    'set' => 'admin.set',
    'del' => 'admin.del',
    'dodel' => 'admin.do_del',
    'login' => 'admin.login',
    'dologin' => 'admin.do_login',
    'logout' => 'admin.logout',
));

echo $router->rout(realpath(dirname(__FILE__)));
?>
