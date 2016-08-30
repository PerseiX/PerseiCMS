<?php
namespace Cms\UserBundle\EventSubscribers;

use Doctrine\Common\EventSubscriber;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Cms\UserBundle\Entity\User;
use Cms\UserBundle\Services\UploaderService;

class UserUploadSubscriber implements EventSubscriber
{
    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'prePersist',
            'preUpdate'
        ];
    }

    /**
     * @var UploaderService
     */
    private $uploader;

    /**
     * UserUploadSubscriber constructor.
     * @param UploaderService $uploader
     */
    public function __construct(UploaderService $uploader)
    {
        $this->uploader = $uploader;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args){
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    /**
     * @param $entity
     */
    private function uploadFile($entity)
    {
        if (!$entity instanceof User) {
            return;
        }
        $file = $entity->getProfilePicturePath();

            if (!$file instanceof UploadedFile) {
         return;
        }

        $fileName = $this->uploader->upload($file);
        $entity->setProfilePicturePath($fileName);
    }
}