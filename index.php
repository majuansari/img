<?php
require_once 'Image.php';
require_once 'inc/helpers.php';
define('INCLUDE_DIR', dirname(__FILE__) . '/config/');

$uriParts = getUriParts();
$app = $uriParts[1] ?? '';
unset($uriParts[0], $uriParts[1]);
if ($app) {
    include(INCLUDE_DIR . $app . '.php');
}
if(empty($config)){
    throw new \Exception('Cannot load configuration for app');
}
$imageUrl = getImageUrl($config, $uriParts);
if(empty($_GET['size'])){
    showSameImage($imageUrl);
    exit;
}
$width = getWidthFromQueryString($_GET['size']);
$height = getHeightFromQueryString($_GET['size']);
(new Image($imageUrl, getWidthFromQueryString($_GET['size']), getHeightFromQueryString($_GET['size'])))->print();