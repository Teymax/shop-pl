<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ForgotType;
use App\Form\ForgPassType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use App\Repository\UserRepository;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ForgotController extends Controller
{
    /**
     * @Route("/forgot", name="forgot")
     * @throws \Exception
     */
    public function index(Request $request, \Swift_Mailer $mailer)
    {
        $form = $this->createForm(ForgotType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            $repository = $this->getDoctrine()->getRepository(User::class);
            $user = $repository->findOneBy(
                ['email' => $contactFormData['email']]
            );
            if(is_null($user)){
                $this->addFlash('error', 'There no such email.');
                return $this->redirectToRoute('forgot');
            }
            $length = 5;
            $token = bin2hex(random_bytes($length));
            $user->setToken($token);
            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();
                $message = (new \Swift_Message('You Got Mail!'))
                    ->setFrom("soulrivsmtp@gmail.com")
                    ->setTo($contactFormData['email'])
                    ->setBody(
                        $this->renderView(
                            'emails/forgot.html.twig', [
                                'token' => $token
                            ]
                        ),
                        'text/html'
                    );
                $mailer->send($message);
                $this->addFlash('success', 'Email has been sent.');
            } catch (\Exception $exception) {
                $this->addFlash('error', 'Something went wrong');
            }
            return $this->redirectToRoute('index');
        }
        return $this->render('forgot/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/forgpass/{token}", name="forgpass")
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \LogicException
     */
    public function passForm(Request $request,$token)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(
            ['token' => $token]
        );
        if (is_null($user)) {
            throw $this->createNotFoundException();
        }
        $form = $this->createForm(ForgPassType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
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
            return $this->redirectToRoute('index');
        }
        return $this->render('forgot/pass.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}
