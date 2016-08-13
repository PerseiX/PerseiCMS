<?php

namespace Cms\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\Date;
use Doctrine\ORM\EntityNotFoundException;

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
    public function usersAction()
    {
        $users = $this->getDoctrine()->getRepository('CmsUserBundle:User')->getAllUsers();
        $roles = $this->getDoctrine()->getRepository('CmsUserBundle:Role')->getAllRoles();
        return $this->render('CmsUserBundle:Default:users.html.twig', array('users' => $users, 'roles' => $roles));
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

    /**
     * @Route("/edit/{userId}", name="editUser")
     */
    public function editUser($userId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $userData = $request->request->all();
        $user = $em->getRepository('CmsUserBundle:User')->findOneBy(array('id' =>$userId));

        if($request->getMethod() == "POST"){
            $role = $em->getRepository('CmsUserBundle:Role')->findOneBy(array('role' => $userData['role']));

            $user->setUsername($userData['username'])
                 ->setEmail($userData['email'])
                 ->setIsActive($userData['isActive'])
                 ->setRoles($role)
                 ->setAbout($userData['about']);

            $validator = $this->get('validator');
            $errors = $validator->validate($user);
            if(count($errors) == 0)
            {
                $em->flush();
                $message['success'] = true;
            }
            else{
                $message['error'][] = $errors[0]->getMessage();
            }
        }

        return new JsonResponse($message);
    }

    /**
     * @Route("/edit-role/{roleId}", name="edit-role")
     */
    public function editRole(Request $request, $roleId)
    {
        $em = $this->getDoctrine()->getManager();
        $chosenRole = $em->getRepository('CmsUserBundle:Role')->findOneBy(array('id' => $roleId));
        if(!$chosenRole) {
            throw new EntityNotFoundException("Not found role");
        }

        $roleData = $request->request->all();
        if($request->getMethod() == "POST")
        {
            $chosenRole->setIsActive($roleData['isActive'])
                ->setRole($roleData['role'])
                ->setName($roleData['name']);
            $em->flush();
        }
        return new JsonResponse(true);
    }
}
