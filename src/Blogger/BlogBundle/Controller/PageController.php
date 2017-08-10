<?php
// src/Blogger/BlogBundle/Controller/PageController.php

namespace Blogger\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Blogger\BlogBundle\Entity\Enquiry;
use Blogger\BlogBundle\Form\EnquiryType;
use Symfony\Component\HttpFoundation\Request;

class PageController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $blogs = $em->getRepository('BloggerBlogBundle:Blog')->getLatestBlogs();

        return $this->render('BloggerBlogBundle:Page:index.html.twig', array('blogs' => $blogs));
    }
    public function aboutAction()
    {
        return $this->render('BloggerBlogBundle:Page:about.html.twig');
    }
    public function contactAction(Request $request)
    {
        $enquiry = new Enquiry();
        $form = $this->createForm(EnquiryType::class, $enquiry);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = \Swift_Message::newInstance()
                        ->setSubject('Contact enquiry from symblog')
                        ->setFrom('enquiries@symblog.co.uk')
                        ->setTo('email@email.com')
                        ->setBody($this->renderView('BloggerBlogBundle:Page:contactEmail.txt.twig',
                            array('enquiry'=>$enquiry)));
            $this->get('mailer')->send($message);
            $this->addFlash('notice', 'mensaje enviado');
        }
        return $this->render('BloggerBlogBundle:Page:contact.html.twig', array(
                'form' => $form->createView()));
    }
    public function faqAction()
    {
        return $this->render('BloggerBlogBundle:Page:faq.html.twig');
    }

     public function sidebarAction()
   {
        $em = $this->getDoctrine()
                   ->getManager();
        $tags = $em->getRepository('BloggerBlogBundle:Blog')
                   ->getTags();
        $tagWeights = $em->getRepository('BloggerBlogBundle:Blog')
                         ->getTagWeights($tags);
        $commentLimit   = $this->container
                               ->getParameter('blogger_blog.comments.latest_comment_limit');
        $latestComments = $em->getRepository('BloggerBlogBundle:Comment')
                            ->getLatestComments($commentLimit);

        return $this->render('BloggerBlogBundle:Page:sidebar.html.twig', array(
            'tags'              => $tagWeights,
            'latestComments'    => $latestComments
        ));
    }
}