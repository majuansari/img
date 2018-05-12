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

    /**
     * @var bool|resource
     */
    private $image;

    /**
     * @var
     */
    private $imageResized;

    /**
     * @var string
     */
    private $header = "Content-type: image/";

    /**
     * @var string
     */
    private $func = "imagejpeg";


    /**
     * Image constructor.
     * @param string $path
     * @param int $width
     * @param int $height
     */
    public function __construct(string $path, int $width = 0, int $height = 0, $type = 'crop')
    {
        $this->path = $path;
        $this->height = $height;
        $this->width = $width;
        $this->image = $this->openFile($this->path);

        if (!$this->image) {
            exit;
        }

        // *** Get width and height
        $this->originalWidth = imagesx($this->image);
        $this->originalHeight = imagesy($this->image);
        $this->resizeImage($width, $height, $type);
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
        $img = $this->getImage($path);
        switch ($extension) {
            case '.jpg':
            case '.jpeg':
                $this->header .= "jpeg";
                $this->func = "imagejpeg";
                break;
            case '.gif':
                $this->header .= "gif";
                $this->func = "imagegif";
                break;
            case '.png':
                $this->header .= "png";
                $this->func = "imagepng";
                break;
            default:
                $img = false;
                break;
        }
        $img = imagecreatefromstring($img);
        return $img;
    }


    /**
     * @param $imageurl
     * @return bool|string
     */
    private function getImage($imageurl)
    {
        return file_get_contents($imageurl);
    }


    /**
     * Resize the imgage
     */
    private function resize()
    {
        $this->imageResized = imagecreatetruecolor($this->width, $this->height);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $this->width, $this->height,
            imagesx($this->image), imagesy($this->image));
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


    ## --------------------------------------------------------


    /**
     * @param $newWidth
     * @param $newHeight
     * @param $option
     * @return array
     */
    private function getDimensions($newWidth, $newHeight, $option)
    {
        switch ($option) {
            case 'exact':
                $optimalWidth = $newWidth;
                $optimalHeight = $newHeight;
                break;
            case 'portrait':
                $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                $optimalHeight = $newHeight;
                break;
            case 'landscape':
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
                break;
            case 'auto':
                $optionArray = $this->getSizeByAuto($newWidth, $newHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
            case 'crop':
                $optionArray = $this->getOptimalCrop($newWidth, $newHeight);
                $optimalWidth = $optionArray['optimalWidth'];
                $optimalHeight = $optionArray['optimalHeight'];
                break;
        }
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }


    ## --------------------------------------------------------


    /**
     * @param $newHeight
     * @return float|int
     */
    private function getSizeByFixedHeight($newHeight)
    {
        $ratio = $this->originalWidth / $this->originalHeight;
        $newWidth = $newHeight * $ratio;
        return $newWidth;
    }


    /**
     * @param $newWidth
     * @return float|int
     */
    private function getSizeByFixedWidth($newWidth)
    {
        $ratio = $this->originalHeight / $this->originalWidth;
        $newHeight = $newWidth * $ratio;
        return $newHeight;
    }


    /**
     * @param $newWidth
     * @param $newHeight
     * @return array
     */
    private function getSizeByAuto($newWidth, $newHeight)
    {
        if ($this->originalHeight < $this->originalWidth) { // *** Image to be resized is wider (landscape)
            $optimalWidth = $newWidth;
            $optimalHeight = $this->getSizeByFixedWidth($newWidth);
        } elseif ($this->originalHeight > $this->originalWidth) { // *** Image to be resized is taller (portrait)
            $optimalWidth = $this->getSizeByFixedHeight($newHeight);
            $optimalHeight = $newHeight;
        } else { // *** Image to be resizerd is a square
            if ($newHeight < $newWidth) {
                $optimalWidth = $newWidth;
                $optimalHeight = $this->getSizeByFixedWidth($newWidth);
            } else {
                if ($newHeight > $newWidth) {
                    $optimalWidth = $this->getSizeByFixedHeight($newHeight);
                    $optimalHeight = $newHeight;
                } else {
                    // *** Sqaure being resized to a square
                    $optimalWidth = $newWidth;
                    $optimalHeight = $newHeight;
                }
            }
        }
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }


    /**
     * @param $newWidth
     * @param $newHeight
     * @return array
     */
    private function getOptimalCrop($newWidth, $newHeight)
    {
        $heightRatio = $this->originalHeight / $newHeight;
        $widthRatio = $this->originalWidth / $newWidth;
        if ($heightRatio < $widthRatio) {
            $optimalRatio = $heightRatio;
        } else {
            $optimalRatio = $widthRatio;
        }
        $optimalHeight = $this->originalHeight / $optimalRatio;
        $optimalWidth = $this->originalWidth / $optimalRatio;
        return array('optimalWidth' => $optimalWidth, 'optimalHeight' => $optimalHeight);
    }


    /**
     * @param $optimalWidth
     * @param $optimalHeight
     * @param $newWidth
     * @param $newHeight
     */
    private function crop($optimalWidth, $optimalHeight, $newWidth, $newHeight)
    {
        // *** Find center - this will be used for the crop
        $cropStartX = ($optimalWidth / 2) - ($newWidth / 2);
        $cropStartY = ($optimalHeight / 2) - ($newHeight / 2);
        $crop = $this->imageResized;
        //imagedestroy($this->imageResized);
        // *** Now crop from center to exact requested size
        $this->imageResized = imagecreatetruecolor($newWidth, $newHeight);
        imagecopyresampled($this->imageResized, $crop, 0, 0, $cropStartX, $cropStartY, $newWidth, $newHeight, $newWidth,
            $newHeight);
    }


    /**
     * @param $newWidth
     * @param $newHeight
     * @param string $option
     */
    public function resizeImage($newWidth, $newHeight, $option = "auto")
    {
        // *** Get optimal width and height - based on $option
        $optionArray = $this->getDimensions($newWidth, $newHeight, $option);
        $optimalWidth = $optionArray['optimalWidth'];
        $optimalHeight = $optionArray['optimalHeight'];
        // *** Resample - create image canvas of x, y size
        $this->imageResized = imagecreatetruecolor($optimalWidth, $optimalHeight);
        imagecopyresampled($this->imageResized, $this->image, 0, 0, 0, 0, $optimalWidth, $optimalHeight,
            $this->originalWidth, $this->originalHeight);
        // *** if option is 'crop', then crop too
        if ($option == 'crop') {
            $this->crop($optimalWidth, $optimalHeight, $newWidth, $newHeight);
        }
    }

}
