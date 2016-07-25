<?php
namespace Cms\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cms\UserBundle\Entity\User;
use Cms\UserBundle\Entity\Role;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints\Date;

class SecurityController extends Controller
{
    /**
     * @Route("/formValidate", name="formValidate")
     */
    public function validateRegistryForm(Request $request)
    {
        $message = array();

        if($request->getMethod() == "POST"){
            $salt = uniqid(mt_rand(), true);
            $role =  $this->getDoctrine()->getRepository('CmsUserBundle:Role')->findBy(array('name' => 'Custom User'))[0];
            $user = new User();

            $userData = $request->request->all();
            $user
                ->setUsername($userData['username'])
                ->setEmail($userData['email'])
                ->setPassword(md5($this->get('security.password_encoder')->encodePassword($user, $salt)))
                ->setDateOfBirthday( new \DateTime($userData['brthdayDate']))
                ->setAbout($userData['about'])
                ->setSalt($salt)
                ->setIsActive(FALSE)
                ->setRoles($role)
                ->setEraseCredentials('erase');

            $validator = $this->get('validator');
            $errors = $validator->validate($user);

            $usernameIsset = $this->getDoctrine()->getRepository('CmsUserBundle:User')->findBy(array('username' => $userData['username']));
            $emailIsset = $this->getDoctrine()->getRepository('CmsUserBundle:User')->findBy(array('email' => $userData['email']));

            if(count($usernameIsset) != 0)
            {
                $message['error'][] = "Podany login jest już zajęty";
            }
            if(count($emailIsset) != 0)
            {
                $message['error'][] = "Podany email jest już zajęty";
            }
            if(count($errors) != 0)
            {
                $message['error'][] = $errors[0]->getMessage();
            }

            if(count($errors) != 0 || count($message) != 0){
                $message['success'] = false;
            }
            else{
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $message['success'] = true;
            }
        }

        return new JsonResponse($message);
    }
    /**
     * @Route("/redirect", name="redirect")
     */
    public function redirectAction()
    {
        return $this->redirect('login'.'#force');
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $session = new Session();

        if ($request->getMethod() == 'POST') {
            $userData = $request->request->all();
            $newUser = new User();
            $foundUser = $this->getDoctrine()->getRepository('CmsUserBundle:User')->loadUsername($userData['username']);
            if ($foundUser != null) {
                $encodedPassword = md5($this->get('security.password_encoder')->encodePassword($newUser, $foundUser->getSalt()));

                if ($encodedPassword == $foundUser->getPassword() && $foundUser->getIsActive() == true) {

                    $role =  $this->getDoctrine()->getRepository('CmsUserBundle:Role')->findBy(array('id' => $foundUser->getRoles()))[0];

                    $token = new UsernamePasswordToken($foundUser, $foundUser->getPassword(), 'default', array($role->getRole()) );
                    $this->get('security.token_storage')->setToken($token);

                    $session->getFlashBag()->add('success', 'Pomyślnie zalogowano');

                    return $this->redirect($this->generateUrl('index'));
                } else {
                    $session->getFlashBag()->add('success', 'Nieprawidłowy login lub hasło');
                }

            } else {
                $session->getFlashBag()->add('success', 'Błędne dane');
            }
        }

        return $this->render('CmsUserBundle:Default:login.html.twig',
            array(
                'sessions' => $session->getFlashBag()->get('success'),
            )
        );
    }

    /**
     * @Route("/login_checker", name="login_check")
     */
    public function loginCheckerAction()
    {

    }

    /**
     * @Route("/registry", name="registry")
     */
    public function registryAction()
    {
        return $this->render('CmsUserBundle:Default:registry.html.twig');
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        $this->get("security.context")->setToken(null);

        return $this->redirect('index');
    }

    /**
     * @Route("/role", name="role")
     */
    public function roleAction(Request $request)
    {
        $data = $request->request->all();
        if($request->getMethod() == "POST")
        {
            $role = new Role();
            $role->setRole($data['role'])
                ->setIsActive($data['isActive'])
                ->setName($data['name']);

            $em = $this->getDoctrine()->getManager();
            $em->persist($role);
            $em->flush();

            return $this->redirect($this->generateUrl('role'));
        }
        $roles = $this->getDoctrine()->getRepository('CmsUserBundle:Role')->findAll();

        return $this->render('CmsUserBundle:Default:role.html.twig', array('roles' => $roles));
    }
}