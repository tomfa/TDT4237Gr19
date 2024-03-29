<?php
require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Slim\Slim([
    'templates.path' => __DIR__.'/webapp/templates/',
    'debug' => false,
    'view' => new \Slim\Views\Twig()
]);

$app->add(new \Slim\Extras\Middleware\CsrfGuard());

$view = $app->view();
$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);

try {
    // Create (connect to) SQLite database in file
    $app->db = new PDO('sqlite:app.db');
    // Set errormode to silent
    $app->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
} catch(PDOException $e) {
    echo $e->getMessage();
    exit();
}

$ns ='tdt4237\\webapp\\controllers\\';

// Home page at http://localhost/
$app->get('/', $ns . 'IndexController:index');

// Login form
$app->get('/login', $ns . 'LoginController:index');
$app->post('/login', $ns . 'LoginController:login');

// Forgot password
$app->get('/user/forgot', $ns . 'LoginController:forgot');
$app->post('/user/forgot', $ns . 'LoginController:sendPassword');

// Reset password
$app->get('/password/reset', $ns . 'ResetPasswordController:reset');
$app->post('/password/reset', $ns . 'ResetPasswordController:updatePassword');

// New user
$app->get('/createuser', $ns . 'UserController:index')->name('newuser');
$app->post('/createuser', $ns . 'UserController:create');

// Edit logged in user
$app->get('/editprofile', $ns . 'UserController:edit')->name('editprofile');
$app->post('/editprofile', $ns . 'UserController:edit');

// Show a user by name
$app->get('/user/:username', $ns . 'UserController:show')->name('showuser');

// Show all users
$app->get('/users', $ns . 'UserController:all');

// Log out
$app->post('/logout', $ns . 'UserController:logout')->name('logout');

// Admin restricted area
$app->get('/admin', $ns . 'AdminController:index')->name('admin');
$app->post('/admin/delete', $ns . 'AdminController:delete')->name('deleteuser');

// Movies
$app->get('/movies', $ns . 'MovieController:index')->name('movies');
$app->get('/movies/:movieid', $ns . 'MovieController:show');
$app->post('/movies/:movieid', $ns . 'MovieController:addReview');

return $app;
