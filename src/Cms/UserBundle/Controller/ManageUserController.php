<?php

namespace Cms\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class ManageUserController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function indexAction()
    {
        return $this->render('CmsUserBundle:Default:index.html.twig');
    }

    /**
     * @Route("/users", name="users")
     *
     */
    public function profileAction()
    {
        $users = $this->getDoctrine()->getRepository('CmsUserBundle:User')->findAll();

        return $this->render('CmsUserBundle:Default:users.html.twig', array('users' => $users));
    }

    /**
     * @Route("/delete/{userId}", name="deletedUser")
     */
    public function deletedUser($userId)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $em->getRepository('CmsUserBundle:User')->findOneBy(array('id' => $userId));

        if (!$user) {
            $unset = false;
        } else {
            $em->remove($user);
            $em->flush();
            $unset = true;
        }
        return new JsonResponse(array('unset' => $unset));
    }
}
