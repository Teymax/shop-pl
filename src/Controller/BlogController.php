<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BlogController extends AbstractController
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index()
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render('blog/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    /**
     * @Route("/post/{post}", name="post")
     * @param Post $post
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function post(Post $post)
    {
        return $this->render('blog/post.html.twig', [
            'post' => $post,
        ]);
    }
}
