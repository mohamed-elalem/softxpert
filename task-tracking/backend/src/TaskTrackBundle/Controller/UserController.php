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
use TaskTrackBundle\Form\UserType;
use TaskTrackBundle\Form\TaskType;
use TaskTrackBundle\Form\ChallengeType;

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


        return $this->getResponse(["code" => Status::STATUS_SUCCESS]);
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

        return $this->getResponse($data);
    }

    public function registerTraineeAction(Request $request) {
        return $this->register($request, Role::TRAINEE);
    }

    public function register(Request $request, $role) {
        $encoder = $this->container->get('security.password_encoder');

        $data = $this->get("services.user_service")
                ->register($request->request->get("username"), $request->request->get("email"), $encoder->encodePassword(new User, $request->request->get("password")), $request->request->get("name"), $role);

        return $this->getResponse($data);
    }

    public function registerSupervisorAction(Request $request) {
        $validator = $this->get("services.form_service")->init($this->createForm(UserType::class, new User()), $request->request->all());
        $data = [];
        if ($validator->isValid()) {
            $data = $this->register($request, Role::SUPERVISOR);
        }
        
        $data["code"] = Status::STATUS_FAILURE;
        $data["extra"] = $validator->getErrors();
        $data["err_code"] = Status::ERR_FORM_VALIDATION_ERROR;
        
        return $this->getResponse($data);
    }

    public function getAuthenticatedUserAction() {
        $data = $this->get("services.user_service")->getAuthenticatedUser($this->getUser()->getId());
        return $this->getResponse($data);
    }

    public function getUserAction($id) {
        $data = $this->get("services.user_service")->getUser($id);
        return $this->getResponse($data);
    }

    public function getAllUsersAction($page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        $data = $this->get("services.user_service")->getAllUsers($paginator, $page, $itemsPerPage);
        return $this->getResponse($data);
    }

    public function getAllSupervisorsAction($page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        $data = $this->get("services.user_service")->getAllUsersByRole(Role::SUPERVISOR, $paginator, $itemsPerPage);
        return $this->getResponse($data);
    }

    public function getAllTraineesAction($page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.user_service")->getAllUsersByRole(Role::TRAINEE, $paginator, $page, $itemsPerPage);
        return $this->getResponse($data);
    }

    public function deleteUserAction(Request $request) {
        $data = $this->get("services.user_service")->deleteUser($request->request->get("id"));
        return $this->getResponse($data);
    }

    public function getUserTasksAction($user_id, $page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        $data = $this->get("services.task_service")->getUserTasks($user_id, $paginator, $page, $itemsPerPage);
        return $this->getResponse($data);
    }

    public function getUserTaskAction($user_id, $challenge_id) {


        $data = $this->get("services.user_service")->getUserTasks($user_id, $challenge_id, $paginator, $page, $itemsPerPage);

        return $this->getResponse($data);
    }

    public function getMyTasksAction() {
        $data = $this->get("services.task_service")->getMyTasks($this->getUser()->getId(), $this->get("services.graph_service.kosaraju"));
        return $this->getResponse($data);
    }
    
    public function getMyRecommendedTasksAction() {
        $data = $this->get("services.task_service")->getMyRecommendedTasks($this->getUser()->getId(), $this->get("services.graph_service.kosaraju"));
        return $this->getResponse($data);
    }

    public function getMyChallengesAction($page) {
        $data = $this->get("services.challenge_service")
                ->getMyChallenges(
                $this->getUser()->getId(), $this->get("helpers.paginator_helper"), $page, $this->getParameter("paginator_items_per_page")
        );
        return $this->getResponse($data);
    }

    public function updateUserTaskScoreAction(Request $request, $task_id) {
        $validator = $this->get("services.form_service")->init($this->createForm(TaskType::class, new Task), $request->request->all());
        $data = [];
        if($validator->isValid()) {
            $data = $this->get("services.task_service")->updateUserTaskScore($task_id, $request->request->get("score"));
        }
        else {
            $data["code"] = Status::STATUS_FAILURE;
            $data["extra"] = $validator->getErrors();
        }
        
        return $this->getResponse($data);
    }

    public function updateUserTaskDurationAction(Request $request, $task_id) {
        if($request->request->get("duration") <= 0) {
            throw new Exception("Error duration cannot be less than 1", 1001);
        }
        $data = $this->get("services.user_service")->updaterUserTaskDuration($task_id, $request->request->get("duration"));
        return $this->getResponse($data);
    }

    public function updateTaskDoneAction(Request $request, $task_id) {
        $data = $this->get("services.task_service")->updateTaskDone($task_id, $request->request->get("done"));
        return $this->getResponse($data);
    }

    public function createNewChallengeAction(Request $request) {
        $validator = $this->get("services.form_service")->init($this->createForm(ChallengeType::class, new Challenge), $request->request->all());
        $data = $this->get("services.challenge_service")
                ->createNewChallenge(
                $this->getUser()->getId(), $request->request->get("title"), $request->request->get("duration"), $request->request->get("description")
        );
        return $this->getResponse($data);
    }

    public function createNewTaskAction(Request $request, $user_id) {
        $validator = $this->get("services.form_service")->init($this->createForm(TaskType::class, new Task), $request->request->all());
        if($validator->isValid()) {
            $data = $this->get("services.task_service")->createNewTask($this->getUser()->getId(), $user_id, $request->request->get("challenge_id"), $this->get("services.graph_service.kosaraju"));
        }
        else {
            $data["code"] = Status::STATUS_FAILURE;
            $data["extra"] = $validator->getErrors();
        }
        return $this->getResponse($data);
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
        return $this->getResponse($data);
    }

    public function updateChallengeAction(Request $request) {
        $data = $this->get("services.challenge_service")
                ->updateChallenge(
                $this->getUser()->getId(), $request->request->get("challenge_id"), $request->request->get("duration"), $request->request->get("description")
        );
        return $this->getResponse($data);
    }

    public function addChallengeChildAction(Request $request) {
        $data = $this->get("services.challenge_service")->addChallengeChild($request->request->get("parent_id"), $request->request->get("child_id"), $this->get("services.graph_service.kosaraju"));
        return $this->getResponse($data);
    }

    public function getSupervisorFilteredTasksAction(Request $request, $page) {
        $filter = $this->get("services.filters.entity_filter.filter_by_supervisor")
                ->init(
                $this->getUser()->getId(), $this->get("services.filters.entity_filter")
        );
        return $this->getResponse($this->getFilteredTasks($request, $page, $filter));
    }

    public function getFilteredTasks($filters, $page) {
        $filter = $this->get("services.filters.entity_filter.factory")->getFilters($filters);

        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.task_service")->getFilteredTasks($filter, $paginator, $page, $itemsPerPage);

        return $data;
    }

    public function getTraineeFilteredTasksAction(Request $request, $page) {
        $filters = array_merge($request->query->all(), ["_user", "user" => $this->getUser()->getId()]);
        return $this->getResponse($this->getFilteredTasks($filters, $page));
    }

    public function getFilteredUsersAction(Request $request, $page) {

        $filter = $this->get("services.filters.entity_filter.factory")->getFilters($request->query->all());
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.user_service")->getFilteredUsers($filter, $paginator, $page, $itemsPerPage);

        return $this->getResponse($data);
    }

    public function getFilteredChallengesAction(Request $request, $page) {
        $filter = $this->get("services.filters.entity_filter.filter_by_user")
                ->init(
                $this->getUser()->getId(), $this->get("services.filters.entity_filter")
        );

        $filter = $this->get("services.filters.entity_filter.factory")->getFilters($request->query->all(), $filter);

        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.challenge_service")->getFilteredChallenges($filter, $paginator, $page, $itemsPerPage);

        return $this->getResponse($data);
    }

    public function getUnassignedChallengesAction(Request $request, $user_id, $page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.challenge_service")->getUnassignedChallenges(
                $this->getUser()->getId(), $user_id, $paginator, $page, $itemsPerPage
        );
        return $this->getResponse($data);
    }

    public function getChallengeChildrenAction(Request $request, $challenge_id, $page) {
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.challenge_service")->getChallengeChildren(
                $this->getUser()->getId(), $challenge_id, $paginator, $page, $itemsPerPage
        );
        return $this->getResponse($data);
    }

    public function deleteTaskAction(Request $request) {
        $data = $this->get("services.task_service")->deleteTask($this->getUser()->getId(), $request->request->get("task_id"));
        return $this->getResponse($data);
    }

    public function deleteChallengeAction(Request $request) {
        $data = $this->get("services.challenge_service")->deleteChallenge($this->getUser()->getId(), $request->request->get("challenge_id"));

        return $this->getResponse($data);
    }

    public function getSingleChallengeAction($challenge_id) {
        $data = $this->get("services.challenge_service")->getSingleChallenge($challenge_id);
        return $this->getResponse($data);
    }

    public function toggleTaskInProgressAction($task_id) {
        $data = $this->get("services.task_service")->toggleTaskInProgress($this->getUser()->getId(), $task_id);
        return $this->getResponse($data);
    }

    public function apiAction(Request $request) {
        return new Response(sprintf('Logged in as %s', $this->getUser()->getUsername()));
    }

    private function getResponse($data) {
        if (!isset($data["code"])) {
            $data["code"] = Status::STATUS_FAILURE;
        }
        if (!isset($data["extra"])) {
            $data["extra"] = [];
        }
        if (!isset($data["err_code"])) {
            $data["err_code"] = -1;
        }
        if (!isset($data["err_message"])) {
            $data["err_message"] = null;
        }
        return ResponseHandler::handle($data["code"], $data["extra"], $data["err_code"], $data["err_message"]);
    }

}
