<?php

namespace Acme\ServerBundle\Helper;


use Doctrine\Common\Persistence\ObjectManager;

class AuthHelper
{
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }
}