<?php

namespace Acme\ServerBundle\Repository;

use Acme\ServerBundle\Entity\Friend;
use Doctrine\ORM\EntityRepository;

class FriendRepository extends EntityRepository
{
    public function save(Friend $object, $flush = false)
    {
        $this->getEntityManager()->persist($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return $object;
    }

    public function remove(Friend $object, $flush = false)
    {
        $this->getEntityManager()->remove($object);

        if ($flush === true) {
            $this->getEntityManager()->flush();
        }

        return true;
    }
}
