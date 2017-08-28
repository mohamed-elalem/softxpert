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

class ChallengeController extends Controller
{
    public function getMyChallengesAction(Request $request) {
        $page = $request->query->get("page");
        $data = $this->get("services.challenge_service")
                ->getMyChallenges(
                $this->getUser()->getId(), $this->get("helpers.paginator_helper"), $page, $this->getParameter("paginator_items_per_page")
        );
        return ResponseHandler::getResponse($data);
    }

    public function createNewChallengeAction(Request $request) {
//        $validator = $this->get("services.form_service")->init($this->createForm(ChallengeType::class, new Challenge), $request->request->all());
        $data = $this->get("services.challenge_service")
                ->createNewChallenge(
                $this->getUser()->getId(), $request->request->get("title"), $request->request->get("duration"), $request->request->get("description")
        );
        return ResponseHandler::getResponse($data);
    }
    
    public function updateChallengeAction(Request $request) {
        $data = $this->get("services.challenge_service")
                ->updateChallenge(
                $this->getUser()->getId(), $request->request->get("challenge_id"), $request->request->get("duration"), $request->request->get("description")
        );
        return ResponseHandler::getResponse($data);
    }

    public function addChallengeChildAction(Request $request) {
        $data = $this->get("services.challenge_service")->addChallengeChild($request->request->get("parent_id"), $request->request->get("child_id"), $this->get("services.graph_service.kosaraju"));
        return ResponseHandler::getResponse($data);
    }
    
    public function getFilteredChallengesAction(Request $request) {
        $filter = $this->get("services.filters.entity_filter.filter_by_user")
                ->init(
                $this->getUser()->getId(), $this->get("services.filters.entity_filter")
        );

        $filters =$request->query->all();
        $page = $filters["page"];
        unset($filters["page"]);
        
        $filter = $this->get("services.filters.entity_filter.factory")->getFilters($filters, $filter);
        
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");

        $data = $this->get("services.challenge_service")->getFilteredChallenges($filter, $paginator, $page, $itemsPerPage);

        return ResponseHandler::getResponse($data);
    }

    public function getUnassignedChallengesAction(Request $request, $user_id) {
        $page = $request->query->get("page");
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        
        $data = $this->get("services.challenge_service")->getUnassignedChallenges(
                $this->getUser()->getId(), $user_id, $paginator, $page, $itemsPerPage
        );
        return ResponseHandler::getResponse($data);
    }

    public function getChallengeChildrenAction(Request $request, $challenge_id) {
        $page = $request->query->get("page");
        $paginator = $this->get("helpers.paginator_helper");
        $itemsPerPage = $this->getParameter("paginator_items_per_page");
        
        $data = $this->get("services.challenge_service")->getChallengeChildren(
                $this->getUser()->getId(), $challenge_id, $paginator, $page, $itemsPerPage
        );
        return ResponseHandler::getResponse($data);
    }

    
    public function deleteChallengeAction(Request $request) {
        $data = $this->get("services.challenge_service")->deleteChallenge($this->getUser()->getId(), $request->request->get("challenge_id"));

        return ResponseHandler::getResponse($data);
    }

    public function getSingleChallengeAction($challenge_id) {
        $data = $this->get("services.challenge_service")->getSingleChallenge($challenge_id);
        return ResponseHandler::getResponse($data);
    }
    
}
