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

class TaskController extends Controller
{
    public function getUserTasksAction(Request $request, $user_id) {
        $page = $request->query->get("page");
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        $data = $this->get("services.task_service")->getUserTasks($this->getUser()->getId(), $user_id, $paginator, $page, $itemsPerPage);
        return ResponseHandler::getResponse($data);
    }

    public function getUserTaskAction($user_id, $challenge_id) {


        $data = $this->get("services.user_service")->getUserTasks($user_id, $challenge_id, $paginator, $page, $itemsPerPage);

        return ResponseHandler::getResponse($data);
    }

    public function getMyTasksAction() {
        $data = $this->get("services.task_service")->getMyTasks($this->getUser()->getId(), $this->get("services.graph_service.kosaraju"));
        return ResponseHandler::getResponse($data);
    }
    
    public function getMyRecommendedTasksAction() {
        $data = $this->get("services.task_service")->getMyRecommendedTasks($this->getUser()->getId(), $this->get("services.graph_service.kosaraju"));
        return ResponseHandler::getResponse($data);
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
        
        return ResponseHandler::getResponse($data);
    }

    public function updateUserTaskDurationAction(Request $request, $task_id) {
        $data = [];
        if($request->request->get("duration") <= 0) {
            $data = [
                "code" => Status::STATUS_FAILURE,
                "err_code" => Status::ERR_FORM_VALIDATION_ERROR,
                "err_message" => "Duration can be greater than zero"
            ];
        }
        else {
            $data = $this->get("services.user_service")->updaterUserTaskDuration($task_id, $request->request->get("duration"));
        }
        return ResponseHandler::getResponse($data);
    }

    public function updateTaskDoneAction(Request $request, $task_id) {
        $data = $this->get("services.task_service")->updateTaskDone($task_id, $request->request->get("done"));
        return ResponseHandler::getResponse($data);
    }
    
    public function createNewTaskAction(Request $request, $user_id) {
//        $validator = $this->get("services.form_service")->init($this->createForm(TaskType::class, new Task), $request->request->all());
//        if($validator->isValid()) {
            $data = $this->get("services.task_service")->createNewTask($this->getUser()->getId(), $user_id, $request->request->get("challenge_id"), $this->get("services.graph_service.kosaraju"));
//        }
//        else {
//            $data["code"] = Status::STATUS_FAILURE;
//            $data["extra"] = $validator->getErrors();
//        }
        return ResponseHandler::getResponse($data);
    }
    
        public function getFilteredTasks($filters, $page) {
        $filter = $this->get("services.filters.entity_filter.factory")->getFilters($filters);

        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.task_service")->getFilteredTasks($filter, $paginator, $page, $itemsPerPage);

        return $data;
    }

    public function getTraineeFilteredTasksAction(Request $request) {
        $filters = $request->query->all();
        $page = $filters["page"];
        unset($filters["page"]);
        $filters["_user"] = true;
        $filters["user"] = $this->getUser()->getId();
        
        return ResponseHandler::getResponse($this->getFilteredTasks($filters, $page));
    }
    
    public function getSupervisorFilteredTasksAction(Request $request) {
        $page = $request->query->get("page");
        $filter = $this->get("services.filters.entity_filter.filter_by_supervisor")
                ->init(
                $this->getUser()->getId(), $this->get("services.filters.entity_filter")
        );
        return ResponseHandler::getResponse($this->getFilteredTasks($filters, $page));
    }
    
    public function deleteTaskAction(Request $request) {
        $data = $this->get("services.task_service")->deleteTask($this->getUser()->getId(), $request->request->get("task_id"));
        
        return ResponseHandler::getResponse($data);
    }
    
    public function toggleTaskInProgressAction($task_id) {
        $data = $this->get("services.task_service")->toggleTaskInProgress($this->getUser()->getId(), $task_id);
        return ResponseHandler::getResponse($data);
    }

}
