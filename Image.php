<?php
/**
 * Created by PhpStorm.
 * User: Ciprian
 * Date: 1/24/18
 * Time: 9:55 AM
 */

class Image
{
    /**
     * @var string
     */
    private $path;
    /**
     * @var int
     */
    private $height;
    /**
     * @var int
     */
    private $width;

    private $image;

    private $imageResized;

    /**
     * @var string
     */
    private $header = "Content-type: image/";

    /**
     * @var string
     */
    private $func = "imagejpeg";

    public function __construct(string $path, int $height = 0, int $width = 0)
    {


        $this->path = $path;
        $this->height = $height;
        $this->width = $width;

        $this->image = $this->openFile($this->path);
        $this->resize();
    }

    /**
     * Open File
     *
     * @param string $path
     * @return bool|resource
     */
    private function openFile(string $path)
    {

        $extension = strtolower(strrchr($path, '.'));

        switch ($extension) {
            case '.jpg':
            case '.jpeg':
                $img = @imagecreatefromjpeg($path);
                $this->header .= "jpeg";
                $this->func = "imagejpeg";
                break;
            case '.gif':
                $img = @imagecreatefromgif($path);
                $this->header .= "gif";
                $this->func = "imagegif";
                break;
            case '.png':
                $img = @imagecreatefrompng($path);
                $this->header .= "png";
                $this->func = "imagepng";
                break;
            default:
                $img = false;
                break;
        }

        return $img;
    }

    /**
     * Resize the imgage
     */
    private function resize()
    {
        $this->imageResized = imagecreatetruecolor($this->height, $this->width);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $this->width, $this->height, imagesx($this->image), imagesy($this->image));
    }

    /**
     * Show the image
     */
    public function print()
    {
        imagedestroy($this->image);
        header($this->header);
        call_user_func($this->func, $this->imageResized);
    }
}