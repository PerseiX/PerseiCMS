<?php
namespace Cms\UserBundle\EventSubscribers;


use Cms\UserBundle\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;

class UserActivationEmail implements EventSubscriber
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Router
     */
    private $router;

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return array
     */
    public function getSubscribedEvents()
    {
        return [
            'postPersist'
        ];
    }

    /**
     * UserActivationEmail constructor.
     * @param \Swift_Mailer $mailer
     * @param Router $router
     */
    public function __construct(\Swift_Mailer $mailer, Router $router)
    {
        $this->router = $router;
        $this->mailer = $mailer;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->user = $args->getObject();
        if (!$this->user instanceof User) {
            return;
        }
        $this->sendEmail();
    }

    /**
     * SendEmail
     */
    public function sendEmail()
    {
        /**
         * @var \Swift_Mime_Message
         */
        $message = \Swift_Message::newInstance()
            ->setSubject('Activate your account, click link below')
            ->setFrom($this->user->getEmail())
            ->setTo($this->user->getEmail())
            ->setBody($this->router->generate('active-account', ['hash' => $this->user->getActivatedHash()], UrlGeneratorInterface::ABSOLUTE_URL));

        $this->mailer->send($message);
    }
}