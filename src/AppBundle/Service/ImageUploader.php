<?php


namespace AppBundle\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload($file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new FileException('Failed to upload file');
        }
        return $fileName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}