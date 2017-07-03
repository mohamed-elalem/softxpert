<?php

namespace SoftXpert\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function testAction() {
        $response = new Response("{'fnhknff'}");
        $response->headers->set("Content-Type", "application/json");
        return $response;
    }
}
