<?php

namespace App\Controller;

//use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;
use EasyCorp\Bundle\EasyAdminBundle\Exception\EntityRemoveException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\NoEntitiesConfiguredException;
use EasyCorp\Bundle\EasyAdminBundle\Exception\UndefinedEntityException;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFormType;
use Pagerfanta\Pagerfanta;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


class UserController extends AdminController
{
    protected $mailer;
    protected $twig;

    public function __construct(\Swift_Mailer $mailer,\Twig_Environment $twig, Security $security)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User $user
     * @throws \Exception
     */
    public function persistEntity($user)
    {
        $user->setUpdatedAt(new \DateTime());
        $user->setCreatedAt(new \DateTime());
        $length = 5;
        try {
            $token = bin2hex(random_bytes($length));
        } catch (\Exception $e) {
        }
        $user->setToken($token);
        $message = (new \Swift_Message('Hello Email'))
            ->setFrom('soulrivsmtp@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'emails/registration.html.twig',
                    array('name' => $user, 'token' => $user->getToken())
                ),
                'text/html');
        $this->mailer->send($message);
        parent::persistEntity($user);
    }
}
