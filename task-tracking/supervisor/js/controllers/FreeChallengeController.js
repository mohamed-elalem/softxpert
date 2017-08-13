(function() {
    angular.module("app").controller("FreeChallengeController", challengeController);
})()

function challengeController($scope, $rootScope, $location, ChallengeFactory, $routeParams, $route, UserFactory) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.location = $location;
    vm.challengeFactory = ChallengeFactory;
    vm.userFactory = UserFactory;
    vm.routeParams = $routeParams;

    vm.scope.getUnassignedChallenges = getUnassignedChallenges;
    vm.scope.assignChallenge = assignChallenge;
    vm.scope.error = false;


    vm.scope.page = 1;

    var token = localStorage.getItem("token");
    
    getSingleUser();

    getUnassignedChallenges(1);

    function getUnassignedChallenges(page) {
        vm.scope.page = page;
        vm.challengeFactory.getUnassignedChallenges(token, vm.routeParams.user_id, page).then(getUnassignedChallengesSuccess, getUnassignedChallengesError).catch(getUnassignedChallengesException);
    }

    function getUnassignedChallengesSuccess(res) {
        vm.scope.error = false;
        if (res.status == 200) {
            vm.scope.challenges = res.data.data.challenges;
            vm.scope.total = res.data.data.total;
            vm.scope.itemsPerPage = res.data.data.itemsPerPage;
        }
    }

    function getUnassignedChallengesError(err) {
        console.log(err);
    }

    function getUnassignedChallengesException(exp) {
        console.log(exp);
    }

    function assignChallenge(challengeId) {
        vm.scope.error = false;
        vm.challengeFactory.assignChallenge(token, vm.routeParams.user_id, challengeId).then(assignChallengeSuccess, assignChallengeError).catch(assignChallengeException);
    }

    function assignChallengeSuccess(res) {
        $route.reload();
    }

    function assignChallengeError(err) {
        console.log(err);
        vm.scope.prerequisits = err.data.data.taskPriorities;
        vm.scope.error = true;
    }

    function assignChallengeException(exp) {
        console.log(exp);
    }

    function getSingleUser() {
        vm.userFactory.getSingleUser(token, vm.routeParams.user_id).then(getSingleUserSuccess, getSingleUserError).catch(getSingleUserException);
    }

    function getSingleUserSuccess(res) {
        if(res.status == 200) {
            vm.scope.user = res.data.data[0];
        }
    }

    function getSingleUserError(err) {
        console.log(err);
    }

    function getSingleUserException(exp) {
        console.log(exp);
    }
}