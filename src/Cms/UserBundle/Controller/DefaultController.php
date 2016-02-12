<?php

namespace Cms\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->render('CmsUserBundle:Default:index.html.twig');
    }
}
