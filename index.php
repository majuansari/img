<?php
/**
 * Created by PhpStorm.
 * User: Ciprian
 * Date: 1/24/18
 * Time: 9:55 AM
 */

include('Image.php');

 (new Image($_GET["path"],$_GET["height"],$_GET["width"]))->print();