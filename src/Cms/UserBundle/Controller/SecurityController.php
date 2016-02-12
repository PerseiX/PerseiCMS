<?php
namespace Cms\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Cms\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
     * @Route("/redirect", name="redirect")
     */
    public function redirectAction()
    {
        return $this->redirect('login'.'#force');
    }

    /**
     * @Route("/profile", name="profile")
     *
     */
    public function profileAction()
    {
        return $this->render('CmsUserBundle:Default:profile.html.twig', array('user' => $this->getUser()));
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

                if ($encodedPassword == $user->getPassword()) {

                    $token = new UsernamePasswordToken($user, $user->getPassword(), 'default', $user->getRoles() );
                    $this->get('security.token_storage')->setToken($token);
                    $this->session->getFlashBag()->add('succes', 'Pomyślnie zalogowano');
                } else {
                    $this->session->getFlashBag()->add('succes', 'Nieprawidłowy login lub hasło');
                }

            } else {
                $this->session->getFlashBag()->add('succes', 'Błędne dane');
            }
        }

        return $this->render('CmsUserBundle:Default:login.html.twig',
            array(
                'sessions' => $this->session->getFlashBag()->get('succes'),
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
    public function registryAction(Request $request)
    {
        if($request->getMethod() == "POST"){
            $salt = uniqid(mt_rand(), true);

            $userData = $request->request->all();
            $date = new \DateTime($userData['brthday-date']);
            $this->user->setUsername($userData['username'])
                ->setPassword(md5($this->get('security.password_encoder')->encodePassword($this->user, $salt)))
                ->setDateBirthday($date)
                ->setAbout($userData['about'])
                ->setSalt($salt)
                ->setRoles('ROLE_USER')
                ->setEraseCredentials('erase');

            $em = $this->getDoctrine()->getManager();
            $em->persist($this->user);
            $em->flush();

            return $this->redirect('login');
        }

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