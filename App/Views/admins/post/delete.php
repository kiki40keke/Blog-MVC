<?php

use App\Helpers\Upload;
use App\Core\Connection;
use App\Helpers\Session;
use App\Table\PostTable;
use App\Models\Repository\PostRepository;

$link = $router->url('admin_posts');

$id = (int) $params['id'];
$filemage = $_POST['image'] ?? null;
$pdo = Connection::getPDO();

$table = new PostRepository($pdo);
//$v = Upload::deleteFile($filemage);


if ($table->deletePost($id)) {
    $v = Upload::deleteFile($filemage);
    Session::setFlash('success', "L’article #{$id} a été supprimé 🗑️");
} else {
    Session::setFlash('error', "Une erreur est survenue lors de la suppression de l’article #{$id}.");
}
http_response_code(301);
header('Location: ' . $link);
exit();
