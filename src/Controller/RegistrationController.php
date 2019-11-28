<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Repository\UserRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegistrationController extends Controller
{
    /**
     * @Route("/reg/{token}", name="reg")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function reg(Request $request, $token)
    {
        $this->get('security.token_storage')->setToken(null);
        $request->getSession()->invalidate();
        return $this->redirectToRoute('registration',[$request,"token"=>$token]);
    }
    /**
     * @Route("/registration/{token}", name="registration")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function register(Request $request, $token)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(
            ['token' => $token]
        );

        if (is_null($user) || is_null($user->getToken())) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                foreach ($form->getErrors() as $error){
                    $this->addFlash('error', $error->getMessage());
                }
            } else {
                $password = $this->get('security.password_encoder')->encodePassword($user, $user->getPassword());
                $user->setPassword($password);
                $user->setToken(null);
                try {
                    $em = $this->getDoctrine()->getManager();
                    $em->persist($user);
                    $em->flush();
                    $this->addFlash('success', 'Your data successfully edited.');
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'Something went wrong, check your data');
                }
                return $this->redirectToRoute('home');
            }
        }

        return $this->render(
            'registration/index.html.twig',
            array('registration_form' => $form->createView())
        );
    }

}
