<?php

namespace TaskTrackBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use TaskTrackBundle\Entity\User;
use TaskTrackBundle\Constants\Role;
use TaskTrackBundle\Constants\Status;
use TaskTrackBundle\Handlers\ResponseHandler;
use TaskTrackBundle\Entity\Challenge;
use TaskTrackBundle\Entity\Task;
//use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use \Symfony\Component\Console\Input\ArrayInput;
use TaskTrackBundle\Form\UserType;
use TaskTrackBundle\Form\TaskType;
use TaskTrackBundle\Form\ChallengeType;
use TaskTrackBundle\Exceptions;

class UserController extends Controller {

    public function revokeRefreshToken(Request $request) {
        $this->createForm($type)->submit($submittedData)->isValid();
        $kernel = $this->get("kernel");
        $application = new Application($kernel);
        $application->setAutoExit(true);
        $input = new ArrayInput([
            "command" => "gesdinet:jwt:revoke",
            "refresh_token" => $request->request->get("refresh_token")
        ]);

        $application->run($input);


        return ResponseHandler::getResponse(["code" => Status::STATUS_SUCCESS]);
    }

    public function logoutAction(Request $request) {
        $token = $request->headers->get("Authorization");
        $data = $this->get("services.user_service")->logout($token);

//        $kernel = $this->get("kernel");
//        $application = new Application($kernel);
//        $application->setAutoExit(true);
//        $input = new ArrayInput([
//            "command" => "gesdinet:jwt:revoke",
//            "refresh_token" => $request->request->get("refresh_token")
//        ]);
//        
//        $application->run($input);

        return ResponseHandler::getResponse($data);
    }

    public function registerTraineeAction(Request $request) {
        return $this->register($request, Role::TRAINEE);
    }

    public function register(Request $request, $role) {
        $encoder = $this->container->get('security.password_encoder');
        $validator = $this->get("services.form_service")->init($this->createForm(UserType::class, new User()), $request->request->all());
        $data = [];
        
        $password = $request->request->get("password");
        $password_confirmation = $request->request->get("password_confirmation");
        
//        if($password != $password_confirmation) {
//            $data["code"] = Status::STATUS_FAILURE;
//            $data["err_code"] = Status::ERR_FORM_VALIDATION_ERROR;
//            $data["err_message"] = "Password confirmation doesn't match";
//        }
        if ($validator->isValid()) {
            $data = $this->get("services.user_service")
                    ->register($request->request->get("username"), $request->request->get("email"), $encoder->encodePassword(new User, $request->request->get("password")), $request->request->get("name"), $role);
        }
        else {
//            $data["code"] = Status::STATUS_FAILURE;
//            $data["extra"] = $validator->getErrors();
//            $data["err_code"] = Status::ERR_FORM_VALIDATION_ERROR;
            throw new Exceptions\FormValidationException(null, $validator->getErrors());
        }
        return ResponseHandler::getResponse($data);
    }

    public function registerSupervisorAction(Request $request) {
        return $this->register($request, Role::SUPERVISOR);
    }

    public function getAuthenticatedUserAction() {
        $data = $this->get("services.user_service")->getAuthenticatedUser($this->getUser()->getId());
        return ResponseHandler::getResponse($data);
    }

    public function getUserAction($id) {
        $data = $this->get("services.user_service")->getUser($id);
        return ResponseHandler::getResponse($data);
    }

    public function getAllUsersAction($page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        $data = $this->get("services.user_service")->getAllUsers($paginator, $page, $itemsPerPage);
        return ResponseHandler::getResponse($data);
    }

    public function getAllSupervisorsAction($page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        $data = $this->get("services.user_service")->getAllUsersByRole(Role::SUPERVISOR, $paginator, $itemsPerPage);
        return ResponseHandler::getResponse($data);
    }

    public function getAllTraineesAction(Request $request) {
        $page = $request->query->get("page");
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.user_service")->getAllUsersByRole(Role::TRAINEE, $paginator, $page, $itemsPerPage);
        return ResponseHandler::getResponse($data);
    }

    public function deleteUserAction(Request $request, $id) {
        $data = $this->get("services.user_service")->deleteUser($id);
        return ResponseHandler::getResponse($data);
    }

    public function updateUserInfoAction(Request $request) {
        
        $user = $this->getUser();
        $request->request->set("username", $user->getUsername());
        
        $encoder = $this->get("security.password_encoder");
        $user = $this->getUser();
        $password = null;
        $password_confirmation = null;
        $email = null;
        $name = null;

        if ($request->request->has("password") && $request->request->has("password_confirmation")) {
            $password = $encoder->encodePassword(new User, $request->request->get("password"));
            $password_confirmation = $encoder->encodePassword(new User, $request->request->get("password_confirmation"));
        
            $request->request->remove("password_confirmation");
        }
        else {
            $request->request->set("password", $user->getPassword());
        }
        if ($request->request->has("email")) {
            $email = $request->request->get("email");
        }
        else {
            $request->request->set("email", $user->getEmail());
        }
        if ($request->request->has("name")) {
            $name = $request->request->get("name");
        }
        else {
            $request->request->set("name", $user->getName());
        }
        $validator = $this->get("services.form_service")->init($this->createForm(UserType::class, $user), $request->request->all());
        $data = [];
        if($validator->isValid() && !empty($request->request->all())) {
            $data = $this->get("services.user_service")->updateUserInfo($user, $password, $password_confirmation, $email, $name);
        }
        if(empty($request->request->all())) {
            $data["code"] = Status::STATUS_FAILURE;
        }
        else {
            $data["code"] = Status::STATUS_FAILURE;
            $data["extra"] = $validator->getErrors();
        }
        return ResponseHandler::getResponse($data);
    }

    public function getFilteredUsersAction(Request $request) {
        $filters = $request->query->all();
        $page = $filters["page"];
        unset($filters["page"]);
        $filter = $this->get("services.filters.entity_filter.factory")->getFilters($filters);
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.user_service")->getFilteredUsers($filter, $paginator, $page, $itemsPerPage);

        return ResponseHandler::getResponse($data);
    }

    public function apiAction(Request $request) {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }

}
