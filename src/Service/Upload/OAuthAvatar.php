<?php

namespace App\Service\Upload;

class OAuthAvatar
{
    private string $pictureUrl;
    private string $pictureName;

    public function __construct(string $pictureUrl, string $pictureName)
    {
        $this->pictureUrl = $pictureUrl;
        $this->pictureName = $pictureName;
    }

    public function getPictureUrl()
    {
        return$this->pictureUrl;
    }

    public function getPictureName()
    {
        return$this->pictureName;
    }
}