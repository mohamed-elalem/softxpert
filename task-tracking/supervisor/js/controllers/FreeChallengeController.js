(function() {
    angular.module("app").controller("FreeChallengeController", challengeController);
})()

function challengeController($scope, $rootScope, $location, ChallengeFactory, $routeParams, $route) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.location = $location;
    vm.challengeFactory = ChallengeFactory;
    vm.routeParams = $routeParams;

    vm.scope.assignChallenge = assignChallenge;


    vm.scope.page = 1;

    var token = localStorage.getItem("token");

    getUnassignedChallenges(1);

    function getUnassignedChallenges(page) {
        vm.scope.page = page;
        vm.challengeFactory.getUnassignedChallenges(token, vm.routeParams.user_id, page).then(getUnassignedChallengesSuccess, getUnassignedChallengesError).catch(getUnassignedChallengesException);
    }

    function getUnassignedChallengesSuccess(res) {
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
        vm.challengeFactory.assignChallenge(token, vm.routeParams.user_id, challengeId).then(assignChallengeSuccess, assignChallengeError).catch(assignChallengeException);
    }

    function assignChallengeSuccess(res) {
        $route.reload();
    }

    function assignChallengeError(err) {
        console.log(err);
    }

    function assignChallengeException(exp) {
        console.log(exp);
    }
}