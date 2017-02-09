<?php

namespace Acme\ServerBundle\Repository;

use Acme\ServerBundle\Entity\PictureLike;
use Doctrine\ORM\EntityRepository;

class PictureLikeRepository extends EntityRepository
{
    public function save(PictureLike $object, $flush = false)
    {
        $this->getEntityManager()->persist($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return $object;
    }

    public function remove(PictureLike $object, $flush = false)
    {
        $this->getEntityManager()->remove($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return true;
    }
}
