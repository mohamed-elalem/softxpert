(function() {
    angular.module("app").controller("MyChallengesController", myChallengesController);
})()

function myChallengesController($scope, $rootScope, $location, ChallengeFactory, $routeParams, $route) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.location = $location;
    vm.challengeFactory = ChallengeFactory;
    vm.routeParams = $routeParams;
    vm.route = $route;

    vm.scope.page = 1;

    vm.scope.getMyChallenges = getMyChallenges;
    vm.scope.deleteChallenge = deleteChallenge;

    var token = localStorage.getItem("token");

    if (angular.equals(vm.routeParams, {})) {
        getMyChallenges(1);
    } else {
        getChallengeChildren(1);
    }

    getMyChallenges(1);

    function getMyChallenges(page) {
        vm.scope.page = page;
        vm.challengeFactory.getMyChallenges(token, page).then(getMyChallengesSuccess, getMyChallengesError).catch(getMyChallengesException);
    }

    function getMyChallengesSuccess(res) {
        if (res.status == 200) {
            vm.scope.challenges = res.data.data.challenges;
            vm.scope.itemsPerPage = res.data.data.itemsPerPage;
            vm.scope.total = res.data.data.total;
        }
    }

    function getMyChallengesError(err) {
        console.log(err);
    }

    function getMyChallengesException(exp) {
        console.log(exp);
    }

    function deleteChallenge(challengeId) {
        vm.challengeFactory.deleteChallenge(token, challengeId).then(deleteChallengeSuccess, deleteChallengeError).catch(deleteChallengeException);
    }

    function deleteChallengeSuccess(res) {
        console.log(res);
        if (res.status == 200) {
            getMyChallenges(vm.scope.page);
        }
    }

    function deleteChallengeError(err) {
        console.log(err);
    }

    function deleteChallengeException(exp) {
        console.log(exp);
    }
}