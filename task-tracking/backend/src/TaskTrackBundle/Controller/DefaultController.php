<?php

namespace TaskTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('TaskTrackBundle:Default:index.html.twig');
    }
}
