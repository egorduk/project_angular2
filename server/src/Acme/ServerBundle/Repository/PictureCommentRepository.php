<?php

namespace Acme\ServerBundle\Repository;

use Acme\ServerBundle\Entity\PictureComment;
use Doctrine\ORM\EntityRepository;

class PictureCommentRepository extends EntityRepository
{
    public function save(PictureComment $object, $flush = false)
    {
        $this->getEntityManager()->persist($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return $object;
    }

    public function remove(PictureComment $object, $flush = false)
    {
        $this->getEntityManager()->remove($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return true;
    }
}
