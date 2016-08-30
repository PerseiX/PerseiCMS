<?php
namespace Cms\UserBundle\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderService
{
    /**
     * @var string
     */
    private $targetDir;

    /**
     * UploaderService constructor.
     * @param $targetDir
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    /**
     * @param UploadedFile $file
     * @return string
     */
    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        $file->move($this->targetDir, $fileName);

        return $fileName;
    }
}