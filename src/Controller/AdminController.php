<?php

namespace App\Controller;

use App\Entity\Specialty;
use App\Entity\User;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
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


class AdminController extends BaseAdminController
{
    const CONST_CLASS = 'class';
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function getUser(): ?User
    {
        $login = $this->get('security.token_storage')->getToken()->getUsername();
        $repository = $this->getDoctrine()->getRepository(User::class);
        /* @var User $user */
        $user = $repository->findOneBy(
            ['email' => $login]
        );
        return $user;

    }


//    /**
//     * @Route("/", name="easyadmin")
//     *
//     * @param Request $request
//     *
//     * @return RedirectResponse|Response
//     *
//     * @throws ForbiddenActionException
//     */
//    public function indexAction(Request $request)
//    {
//        $this->initialize($request);
//
//        if (null === $request->query->get('entity')) {
//            return $this->render('default/dashboard.html.twig');
//        }
//
//        $action = $request->query->get('action', 'list');
//        if (!$this->isActionAllowed($action)) {
//            throw new ForbiddenActionException(['action' => $action, 'entity_name' => $this->entity['name']]);
//        }
//
//
//        return $this->executeDynamicMethod($action . '<EntityName>Action');
//    }


    protected function editAction()
    {
        $entityName = $this->request->query->get('entity');
        $id = $this->request->query->get('id');
        $repository = $this->getDoctrine()->getRepository('App\Entity\\' . $entityName);
        $entityObject = $repository->findOneBy(
            ['id' => $id]
        );
        $user = $this->getUser();

        $this->request->query->add(['user_role'=>$this->entity['permissions']['edit']]);
        if(method_exists($entityObject,'getAllowedUsers')) {
            if (!$entityObject->getAllowedUsers()->contains($user) && isset($this->entity['permissions']['edit'])) {
                $this->denyAccessUnlessGranted($this->entity['permissions']['edit']);
            }

            if ($user->getRoles() == $this->entity['permissions']['edit'] && !$entityObject->getAllowedUsers()->contains($user)) {
                $exception = $this->createAccessDeniedException('You can`t edit this Entity');
                $exception->setAttributes($this->entity['permissions']['edit']);
                throw $exception;
            }
        }else{
            if (isset($this->entity['permissions']['edit'])) {
                $this->denyAccessUnlessGranted($this->entity['permissions']['edit']);
            }
        }

        return parent::editAction();
    }

}
