<?php

namespace TaskTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class AboutPageController extends Controller
{
    public function aboutUsAction() {
        $aboutPageRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:AboutPage");
    
        $pageContent = $aboutPageRepository->getPageContent();
        
        return $this->render("TaskTrackBundle:about_us:about.html.twig", [
            "content" => $pageContent
        ]);
    }
}
