<?php
namespace Cms\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cms\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Validator\Constraints\Date;

class SecurityController extends Controller
{
    protected $session;

    protected $user;

    public function __construct()
    {
        $this->session = new Session();
        $this->user = new User();
    }

    /**
     * @Route("/formValidate", name="formValidate")
     */
    public function validateRegistryForm(Request $request)
    {
        $message = array();

        if($request->getMethod() == "POST"){
            $salt = uniqid(mt_rand(), true);

            $userData = $request->request->all();
            $this->user
                ->setUsername($userData['username'])
                ->setEmail($userData['email'])
                ->setPassword(md5($this->get('security.password_encoder')->encodePassword($this->user, $salt)))
                ->setDateOfBirthday( new \DateTime($userData['brthdayDate']))
                ->setAbout($userData['about'])
                ->setSalt($salt)
                ->setIsActive(FALSE)
                ->setRoles('ROLE_USER')
                ->setEraseCredentials('erase');

            $validator = $this->get('validator');
            $errors = $validator->validate($this->user);

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
                $em->persist($this->user);
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

        $authenticationUtils = $this->get('security.authentication_utils');
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        if ($request->getMethod() == 'POST') {
            $userData = $request->request->all();

            $user = $this->getDoctrine()->getRepository('CmsUserBundle:User')->loadUsername($userData['username']);
            if ($user != null) {
                $encodedPassword = md5($this->get('security.password_encoder')->encodePassword($this->user, $user->getSalt()));

                if ($encodedPassword == $user->getPassword() && $user->getIsActive() == true) {

                    $token = new UsernamePasswordToken($user, $user->getPassword(), 'default', $user->getRoles() );
                    $this->get('security.token_storage')->setToken($token);

                    $this->session->getFlashBag()->add('success', 'Pomyślnie zalogowano');

                    return $this->redirect($this->generateUrl('index'));
                } else {
                    $this->session->getFlashBag()->add('success', 'Nieprawidłowy login lub hasło');
                }

            } else {
                $this->session->getFlashBag()->add('success', 'Błędne dane');
            }
        }

        return $this->render('CmsUserBundle:Default:login.html.twig',
            array(
                'sessions' => $this->session->getFlashBag()->get('success'),
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

}