<?php
// src/Blogger/BlogBundle/Controller/BlogController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Form\BlogType;
use Blogger\BlogBundle\Entity\Blog;
use Symfony\Component\HttpFoundation\Request;

/**
 * Blog controller.
 */
class BlogController extends Controller
{
    /**
     * Show a blog entry
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository('BloggerBlogBundle:Blog')->find($id);
        $comments = $em->getRepository('BloggerBlogBundle:Comment')
                       ->getCommentsForBlog($blog->getId());

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        return $this->render('BloggerBlogBundle:Blog:show.html.twig', array(
            'blog'      => $blog,
            'comments'  => $comments,
        ));
    }
    /*public function newBlogAction()
    {

        $blog = new Blog();
        $form   = $this->createForm(BlogType::class, $blog);
        return $this->render('BloggerBlogBundle:Page:newBlog.html.twig', array(
            'form'   => $form->createView()
        ));
    }*/

    public function newBlogAction(Request $request)
    {
        $blog  = new Blog();
        $form    = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()
                       ->getManager();
            $em->persist($blog);
            $em->flush();
            return $this->redirect($this->generateUrl('blogger_blog_newBlog'));
        }
        return $this->render('BloggerBlogBundle:Page:newBlog.html.twig', array('form' => $form->createView()));
    }
}