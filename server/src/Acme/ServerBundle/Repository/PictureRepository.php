<?php

namespace Acme\ServerBundle\Repository;

use Acme\ServerBundle\Entity\Picture;
use Doctrine\ORM\EntityRepository;

class PictureRepository extends EntityRepository
{
    public function save(Picture $object, $flush = false)
    {
        $this->getEntityManager()->persist($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return $object;
    }

    public function remove(Picture $object, $flush = false)
    {
        $this->getEntityManager()->remove($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return true;
    }
}
