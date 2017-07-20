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
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\BufferedOutput;
use \Symfony\Component\Console\Input\ArrayInput;

class UserController extends Controller
{
    
    public function revokeRefreshToken(Request $request) {
        $kernel = $this->get("kernel");
        $application = new Application($kernel);
        $application->setAutoExit(true);
        $input = new ArrayInput([
            "command" => "gesdinet:jwt:revoke",
            "refresh_token" => $request->request->get("refresh_token")
        ]);
        
        $output = new BufferedOutput();
        $application->run($input);
        

        return $this->getResponse(["code" => Status::STATUS_SUCCESS, "extra" => [$content]]);
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
        $data = $this->get("services.user_service")->getAuthenticatedUser($this->getUser()->getId());
        return $this->getResponse($data);
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
        $data = $this->get("services.user_service")->getAllUsersByRole(Role::TRAINEE);
        return $this->getResponse($data);
    }

    public function deleteUserAction(Request $request) {
        $data = $this->get("services.user_service")->deleteUser($request->request->get("id"));
        return $this->getResponse($data);
    }
    
    public function getUserTasksAction($user_id) {
        $data = $this->get("services.task_service")->getUserTasks($user_id);
        return $this->getResponse($data);
    }
    
    public function getUserTaskAction($user_id, $challenge_id) {
        $data = $this->get("services.user_service")->getUserTasks($user_id, $challenge_id);
        return $this->getResponse($data);
    }
    
    public function getMyTasksAction() {
        $data = $this->get("services.task_service")->getMyTasks($this->getUser()->getId(), $this->get("services.graph_service.kosaraju"));
        return $this->getResponse($data);
    }
    
    public function getMyChallengesAction() {
        $data = $this->get("services.challenge_service")->getMyChallenges($this->getUser()->getId());
        return $this->getResponse($data);
    }
    
    public function updateUserTaskScoreAction(Request $request, $user_id, $challenge_id) {
        $data = $this->get("services.task_service")->updateUserTaskScore($user_id, $challenge_id, $request->request->get("score"));
        return $this->getResponse($data);
    }
    
    public function updateUserTaskDurationAction(Request $request, $user_id, $challenge_id) {
        $data = $this->get("services.user_service")->updaterUserTaskDuration($user_id, $challenge_id, $request->request->get("duration"));
        return $this->getResponse($data);
    }
    
    public function updateTaskDoneAction(Request $request, $user_id, $challenge_id) {
        $data = $this->get("services.task_service")->updateTaskDone($user_id, $challenge_id, $request->request->get("done"));
        return $this->getResponse($data);
    }
    
    public function createNewChallengeAction(Request $request) {
        $data = $this->get("services.challenge_service")
                ->createNewChallenge(
                        $this->getUser()->getId(),
                        $request->request->get("title"),
                        $request->request->get("duration"),
                        $request->request->get("description")
                        );
        return $this->getResponse($data);
    }
    
    public function createNewTaskAction(Request $request, $user_id) {
        $data = $this->get("services.task_service")->createNewTask($this->getUser()->getId(), $user_id, $request->request->get("challenge_id"));
        return $this->getResponse($data);
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
        
        $data =  $this->get("services.user_service")->updateUserInfo($user, $password, $password_confirmation, $email, $name);
    
        return $this->getResponse($data);
    }

    public function updateChallengeAction(Request $request) {
        $data = $this->get("services.challenge_service")
                ->updateChallenge(
                        $this->getUser()->getId(),
                        $request->request->get("challenge_id"),
                        $request->request->get("duration"),
                        $request->request->get("description")
                        );
        return $this->getResponse($data);
    }
    
    public function addChallengeChildAction(Request $request) {
        $data = $this->get("services.challenge_service")->addChallengeChild($request->request->get("parent"), $request->request->get("child"));
        return $this->getResponse($data);
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
            $data["err_message"] = null;
        }
        return ResponseHandler::handle($data["code"], $data["extra"], $data["err_code"], $data["err_message"]);
    }

}
