<?php
/**
 * @return array
 */
function getUriParts()
{
    $uri = parse_url($_SERVER['REQUEST_URI'])['path'];
    $uriParts = (explode('/', $uri));
    return $uriParts;
}

/**
 * @param $size
 * @return mixed
 */
function getWidthFromQueryString($size)
{
    return $config['sizes'][$_GET['size']]['w'] ?? explode('x', $size)[0];
}

/**
 * @param $size
 * @return mixed
 */
function getHeightFromQueryString($size)
{
    return $config['sizes'][$_GET['size']]['h'] ?? explode('x', $size)[1];
}

/**
 * @param $config
 * @param $uriParts
 * @return string
 */
function getImageUrl($config, $uriParts)
{
    $imageUrl = $config['cdn_base_url'] . implode('/', $uriParts);
    return $imageUrl;
}

/**
 * @param $url
 */
function showSameImage($url)
{
    $extension = str_replace('.', '', strtolower(strrchr($url, '.')));
    if ($extension == 'jpg') {
        $extension = 'jpeg';
    }
    header("Content-type: image/" . $extension);
    print file_get_contents($url);
}