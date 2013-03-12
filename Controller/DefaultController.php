<?php

namespace RC\PHPCRSeoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RCPHPCRSeoBundle:Default:index.html.twig', array('name' => $name));
    }
}
