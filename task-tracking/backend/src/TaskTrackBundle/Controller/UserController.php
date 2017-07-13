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
    
    public function registerTraineeAction(Request $request)
    {
        return $this->register($request, Role::TRAINEE);
    }
    
    public function register(Request $request, $role) {
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        $encoder = $this->container->get('security.password_encoder');

        $username = $request->request->get("username");
        $email = $request->request->get('email');
        $password = $request->request->get('password');
        $name = $request->request->get("name");
        
        $registered = count($userRepository->checkIfRegistered($username, $email)) == 1;
        
        $response = new Response();
        
        if(! $registered) {
            $userRepository->addNewUser($name, $username, $email, $encoder->encodePassword(new User(), $password), $role);
            $response = ResponseHandler::handle(new Response);
            
        }
        else {
            $response = ResponseHandler::handle(new Response, [], Status::EXIST);
            $response->setStatusCode(Status::RESPONSE_CODES[Status::EXIST]);
        }
        return $response;
    }
    
    public function registerSupervisorAction(Request $request) {
        return $this->register($request, Role::SUPERVISOR);
    }
    
    public function getAuthenticatedUserAction() {
        return ResponseHandler::handle(new Response, $this->unsetPasswords([$this->getUser()]));
    }
    
    
    public function getUserAction($id) {
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        
        $user = $userRepository->getUser($id);
        
        return ResponseHandler::handle(new Response, [$user]);
    }
    
    public function getAllUsersAction() {
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        
        $users = ($userRepository->getAllUsers());
        
        return ResponseHandler::handle(new Response, $users);
    }
    
    public function getAllTraineesAction() {
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        
        $users = $this->unsetPasswords($userRepository->getAllUsersByRole(Role::TRAINEE));
        
        return ResponseHandler::handle(new Response, $users);
    }

    public function deleteUserAction($id) {
        
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        
        $userRepository->deleteUser($id);
        
        return ResponseHandler::handle(new Response);
    }
    
    public function getUserTasksAction($user_id) {
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        
        $tasks = $userRepository->getAllUserTasks($user_id);
        
        return ResponseHandler::handle(new Response, $tasks);
    }
    
    public function getUserTaskAction($user_id, $challenge_id) {
        $taskRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:Task");
        
        
        $task = $taskRepository->getUserTasks($user_id, $challenge_id);
        
        return ResponseHandler::handle(new Response, $task);
    }
    
    public function updateUserTaskScoreAction(Request $request, $user_id, $challenge_id) {
        $taskRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:Task");
        $score = $request->request->get("score");
        
        $taskRepository->updateScore($user_id, $challenge_id, $score);
        
        return ResponseHandler(new Response);
        
    }
    
    public function updateUserTaskDurationAction(Request $request, $user_id, $challenge_id) {
        $taskRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:Task");
        $duration = $request->request->get("duration");
        
        $taskRepository->updateDuration($user_id, $challenge_id, $duration);
        
        return ResponseHandler::handle(new Response);
    }
    
    
    public function updateTaskDoneAction(Request $request, $user_id, $challenge_id) {
        $taskRepository = $this->getDoctrine->getRepository($this, "TaskTrackBundle:Task");
        $done = $request->request->get("done");
        
        $taskRepository->updateDone($done);
        
        return ResponseHandler::handle(new Response);
    }
    
    public function createNewChallengeAction(Request $request) {
        $challengeRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:Challenge");
        $user = $this->getUser();
        $parent_id = $request->request->get("parent_id");
        $duration = $request->request->get("duration");
        $description = $request->request->get("description");
        $parent = null;
        if($parent_id > 0) {
            $parent = $challengeRepository->getChallenge($parent_id);
            if(! $parent) {
                throw new Exception("Parent not found exception");
            }
        }
        $challengeRepository->addNewChallenge($user, $parent, $duration, $description);
        
        return ResponseHandler::handle(new Response);
    }
    
    public function createNewTaskAction(Request $request) {
        $taskRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:Task");
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        $challengeRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:Challenge");
        
        
        $user_id = $request->request->get("user_id");
        $challenge_id = $request->request->get("challenge_id");
        
        $task = $taskRepository->checkIfTaskExist($user_id, $challenge_id);
        
        $response = null;
        
        if(! $task) {
            $user = $userRepository->getUser($user_id);
            $challenge = $challengeRepository->getChallenge($challenge_id);

            $taskRepository->addNewTask($user, $challenge);
            
            $response = ResponseHandler::handle(new Response);
        }
        else {
            
            $response = ResponseHandler::handle(new Response, [], Status::TASK_EXIST);
        }
        return ResponseHandler::handle(new Response);
    }
    
    public function updateUserInfoAction(Request $request) {
        
        $userRepository = $this->getDoctrine()->getRepository("TaskTrackBundle:User");
        $encoder = $this->get("security.password_encoder");
        $password = null;
        $password_confirmation = null;
        $role = null;
        $name = null;
        
        $data = [];
        
        if($request->request->has("password") && $request->request->has("password_confirmation")) {
             $password = $request->request->get("password");
             $password_confirmation = $request->request->get("password_confirmation");
        }
        if($request->request->has("role")) {
            $role = $request->request->get("role");
        }
        if($request->request->has("name")) {
            $name = $request->request->get("name");
        }
        
        if($password && $password != $password_confirmation) {
            throw new Exception("Password confirmation not equal password");
        }
        
        if($password) {
            $data["password"] = $encoder->encodePassword(new User, $password);
        }
        
        if($role) {
            $data["role"] = $role;
        }
        
        if($name) {
            $data["name"] = $name;
        }
        
        $userRepository->updateUser($this->getUser(), $data);
        
        return ResponseHandler::handle(new Response);
    }
    
    public function getSupervisorChallengesAction() {
        $user = $this->getUser();
        $challenges = $user->getChallenges();
        
        return ResponseHandler::handle(new Response, $challenges);
    }
    
    public function apiAction(Request $request)
    {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }
    
    public function unsetPasswords($users) {
        foreach($users as $user) {
            $user->setPassword(null);
        }
        return $users;
    }
}
