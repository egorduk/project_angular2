<?php

namespace Acme\ServerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\RestBundle\Controller\FOSRestController;

class ProductController extends FOSRestController
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('AcmeServerBundle:Default:index.html.twig');
    }
}
