
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../app/controllers/AuthController.php';
require_once __DIR__ . '/../app/controllers/PostController.php';
require_once __DIR__ . '/../app/controllers/ProfileController.php';

$database = new Database();
$db = $database->connect();

$auth = new AuthController($db);

$page = $_GET['page'] ?? 'login';

switch ($page) {
    case 'login':
        $auth->login();
        break;

    case 'register':
        $auth->register();
        break;

    case 'logout':
        $auth->logout();
        break;

    case 'feed':
        $postController = new PostController($db);
        $postController->feed();
        break;

    case 'profile':
        $profileController = new ProfileController($db);
        $profileController->show();
        break;

    default:
        http_response_code(404);
        echo "404 Page Not Found";
        break;
}