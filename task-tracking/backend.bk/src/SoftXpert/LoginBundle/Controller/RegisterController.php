<?php

namespace SoftXpert\LoginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use SoftXpert\Constants\Role;
use SoftXpert\LoginBundle\Constants\Status;
use SoftXpert\Handlers\ResponseHandler;

/** Deleted after fix **/

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

/** ***************** **/

use SoftXpert\UserBundle\Entity\User;


class RegisterController extends Controller
{
    private $serializer;

    // deleted after fix
    
    public function __construct() {
        $encoders = array(new XmlEncoder(), new JsonEncoder());
        $normalizers = array(new ObjectNormalizer());
        $this->serializer = new Serializer($normalizers, $encoders);
    }
    
    public function registerAction(Request $request) {
        
        dump(Status::SUCCESS);
        die();
        
        $em = $this->get("doctrine")->getManager();
        
        $user = new User();
        $user->setName($request->get("name"));
        $password = $request->get("password");
        
        $encoded = $encoder->encodePassword($user, $request->get("password"));
        $user->setPassword($encoded);
        $user->setEmail($request->get("email"));
        $user->setCreatedAt();
        $user->getUpdatedAt();
        
        $em->presist($user);
        $em->flush();
        
        $response = new Response($this->handle(0, 0));
        
        $response->headers->set("Content-Type", "application/json");
        return $response;
    }
    
    // Until this issue is fixed handling response here
    
    public function handle($status, $format) {
        return $this->serializer->serialize([
            "status" => 0,
            "messages" => "working"
        ], "json");
    }
}
