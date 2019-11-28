<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        return $this->render('home/index.html.twig', [
            'home_content' => 'Home Page',
        ]);
    }

    /**
     * @Route("/about", name="about")
     */
    public function about()
    {
        return $this->render('home/about.html.twig', [
            'about_content' => 'About Page',
        ]);
    }
    /**
     * @Route("/contact", name="contact")
    */
        public function contact()
        {
            return $this->render('home/contact.html.twig', [
                'about_content' => 'About Page',
            ]);
        }
}
