<?php

namespace Acme\ServerBundle\Repository;

use Acme\ServerBundle\Entity\PictureGallery;
use Doctrine\ORM\EntityRepository;

class PictureGalleryRepository extends EntityRepository
{
    public function save(PictureGallery $object, $flush = false)
    {
        $this->getEntityManager()->persist($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return $object;
    }

    public function remove(PictureGallery $object, $flush = false)
    {
        $this->getEntityManager()->remove($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return true;
    }
}
