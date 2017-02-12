<?php

namespace Acme\ServerBundle\Helper;

use Symfony\Component\Config\Definition\Exception\InvalidTypeException;

class ImageThumbnailHelper
{
    private $maxWidth = 450;
    private $maxHeight = 900;
    private $thumbnailWidth = 0;
    private $thumbnailHeight = 0;
    private $parameters = [];

    public function __construct($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param string $sourceImagePath
     * @param string $sourceImageMimeType
     *
     * @return bool
     */
    public function createImageThumbnail($sourceImagePath, $sourceImageMimeType)
    {
        $newWidth = $this->maxWidth;
        $newHeight = $this->maxHeight;

        $fileName = basename($sourceImagePath);

        list($width, $height) = getimagesize($sourceImagePath);
        $ratioOriginal = $width / $height;

        if ($this->maxWidth / $this->maxHeight > $ratioOriginal) {
            $newWidth = $this->maxHeight * $ratioOriginal;
        } else {
            $newHeight = $this->maxWidth / $ratioOriginal;
        }

        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);

        if ($sourceImageMimeType === 'image/jpeg') {
            header('Content-Type: image/jpeg');
            $source = imagecreatefromjpeg($sourceImagePath);
        } elseif ($sourceImageMimeType === 'image/png') {
            header('Content-Type: image/png');
            $source = imagecreatefrompng($sourceImagePath);
        } elseif ($sourceImageMimeType === 'image/gif') {
            header('Content-Type: image/gif');
            $source = imagecreatefromgif($sourceImagePath);
        } else {
            throw new InvalidTypeException();
        }

        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $this->setThumbnailHeight($newHeight);
        $this->setThumbnailWidth($newWidth);

        return imagejpeg($thumbnail, $this->parameters['resize'] . DIRECTORY_SEPARATOR . $fileName);
    }

    /**
     * @return int
     */
    public function getThumbnailWidth()
    {
        return $this->thumbnailWidth;
    }

    /**
     * @param int $thumbnailWidth
     */
    public function setThumbnailWidth($thumbnailWidth)
    {
        $this->thumbnailWidth = $thumbnailWidth;
    }

    /**
     * @return int
     */
    public function getThumbnailHeight()
    {
        return $this->thumbnailHeight;
    }

    /**
     * @param int $thumbnailHeight
     */
    public function setThumbnailHeight($thumbnailHeight)
    {
        $this->thumbnailHeight = $thumbnailHeight;
    }
}
