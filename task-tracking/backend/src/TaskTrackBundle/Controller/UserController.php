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
use Symfony\Component\Config\Definition\Exception\Exception;

class UserController extends Controller
{
    
    public function getHelper() {
        return $this->get("TaskTrackBundle\Helpers\Helper");
    }
    
    public function registerTraineeAction(Request $request) {
        return $this->register($request, Role::TRAINEE);
    }
    
    public function register(Request $request, $role) {
        $encoder = $this->container->get('security.password_encoder');

        $data = $this->get("services.user_service")
                ->register($request->request->get("username"), 
                            $request->request->get("email"), 
                            $encoder->encodePassword(new User, $request->request->get("password")), 
                            $request->request->get("name"), 
                            $role);
        
        return $this->getResponse($data);
    }
    
    public function registerSupervisorAction(Request $request) {
        return $this->register($request, Role::SUPERVISOR);
    }
    
    public function getAuthenticatedUserAction() {
        return ResponseHandler::handle(Status::STATUS_SUCCESS, $this->unsetPasswords([$this->getUser]));

    }
    
    public function getUserAction($id) {
        $data = $this->get("services.user_service")->getUser($id);
        return $this->getResponse($data);
    }
    
    public function getAllUsersAction() {
        $data = $this->get("services.user_service")->getAllUsers(new Response);
        return $this->getResponse($data);
    }
    
    public function getAllSupervisorsAction() {
        $data = $this->get("services.user_service")->getAllUsersByRole(Role::SUPERVISOR);
        return $this->getResponse($data);
    }
    
    public function getAllTraineesAction() {
        return $this->get("services.user_service")->getAllUsersByRole(new Response, Role::TRAINEE);
    }

    public function deleteUserAction(Request $request) {
        $id = $request->request->get("id");
        
        return $this->get("services.user_service")->deleteUser(new Response, $id);
    }
    
    public function getUserTasksAction($user_id) {
        return $this->get("services.user_service")->getUserTasks(new Response, $user_id);
    }
    
    public function getUserTaskAction($user_id, $challenge_id) {
        return $this->get("services.user_service")->getUserTasks(new Response, $user_id, $challenge_id);
    }
    
    public function getMyTasksAction() {
        return $this->get("services.user_service")->getMyTasks(new Response, $this->getUser(), $this->get("services.graph_service.kosaraju"));
    }
    
    public function getMyChallengesAction() {
        
        return $this->get("services.user_service")->getMyChallenges(new Response, $this->getUser());
    }
    
    public function updateUserTaskScoreAction(Request $request, $user_id, $challenge_id) {
        $score = $request->request->get("score");
        
        return $this->get("services.user_service")->updateUserTaskScore(new Response, $user_id, $challenge_id, $score);
    }
    
    public function updateUserTaskDurationAction(Request $request, $user_id, $challenge_id) {
        $duration = $request->request->get("duration");
        return $this->get("services.user_service")->updateUserTaskDuration(new Response, $user_id, $challenge_id, $duration);
    }
    
    public function updateTaskDoneAction(Request $request, $user_id, $challenge_id) {
        $done = $request->request->get("done");
        return $this->get("services.user_service")->updateTaskDone(new Response, $user_id, $challenge_id, $done);
    }
    
    public function createNewChallengeAction(Request $request) {
        $user = $this->getUser();
        $title = $request->request->get("title");
        $duration = $request->request->get("duration");
        $description = $request->request->get("description");
        return $this->get("services.user_service")->createNewChallenge(new Response, $user, $title, $duration, $description);
    }
    
    public function createNewTaskAction(Request $request, $user_id) {
        
        $challenge_id = $request->request->get("challenge_id");
        
        return $this->get("services.user_service")->createNewTask(new Response, $this->getUser(), $user_id, $challenge_id);
    }
    
    public function updateUserInfoAction(Request $request) {
        
        $encoder = $this->get("security.password_encoder");
        $user = $this->getUser();
        $password = null;
        $password_confirmation = null;
        $email = null;
        $name = null;
        
        if($request->request->has("password") && $request->request->has("password_confirmation")) {
             $password = $encoder->encodePassword(new User, $request->request->get("password"));
             $password_confirmation = $encoder->encodePassword(new User, $request->request->get("password_confirmation"));
        }
        if($request->request->has("email")) {
            $email = $request->request->get("email");
        }
        if($request->request->has("name")) {
            $name = $request->request->get("name");
        }
        
        return $this->get("services.user_service")->updateUserInfo($user, $password, $password_confirmation, $email, $name);
    }

    public function updateChallengeAction(Request $request) {
        $duration = $request->request->get("duration");
        $description = $request->request->get("description");
        $challenge_id = $request->request->get("challenge_id");
        
        return $this->get("services.user_service")->updateChallenge(new Response, $this->getUser(), $challenge_id, $duration, $description);
    }
    
    public function addChallengeChildAction(Request $request) {
        $parent_id = $request->request->get("parent");
        $child_id = $request->request->get("child");
        return $this->get("services.user_service")->addChallengeChild(new Response, $parent_id, $child_id);
    }
    
    public function apiAction(Request $request) {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }
    
    private function getResponse($data) {
        if(! isset($data["code"])) {
            $data["code"] = Status::STATUS_SUCCESS;
        }
        if(! isset($data["extra"])) {
            $data["extra"] = [];
        }
        if(! isset($data["err_code"])) {
            $data["err_code"] = -1;
        }
        if(! isset($data["err_message"])) {
            $data["err_message"] = '';
        }
        return ResponseHandler::handle($data["code"], $data["extra"], $data["err_code"], $data["err_message"]);
    }

}
