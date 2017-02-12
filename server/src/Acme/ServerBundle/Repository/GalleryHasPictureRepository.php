<?php

namespace Acme\ServerBundle\Repository;

use Acme\ServerBundle\Entity\GalleryHasPicture;
use Doctrine\ORM\EntityRepository;

class GalleryHasPictureRepository extends EntityRepository
{
    public function save(GalleryHasPicture $object, $flush = false)
    {
        $this->getEntityManager()->persist($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return $object;
    }

    public function remove(GalleryHasPicture $object, $flush = false)
    {
        $this->getEntityManager()->remove($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return true;
    }
}
