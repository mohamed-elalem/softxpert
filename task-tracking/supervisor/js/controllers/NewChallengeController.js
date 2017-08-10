(function() {
    angular.module("app").controller("NewChallengeController", newChallengeController);
})()

function newChallengeController($scope, $location, $routeParams, ChallengeFactory, $window) {
    var vm = this;
    vm.scope = $scope;
    vm.location = $location;
    vm.routeParams = $routeParams;
    vm.challengeFactory = ChallengeFactory;
    vm.window = $window;

    vm.scope.newChallenge = newChallenge;


    var token = localStorage.getItem("token");


    function newChallenge() {
        vm.challengeFactory.newChallenge(token, vm.scope.title, vm.scope.description, vm.scope.duration).then(newChallengeSuccess, newChallengeError).catch(newChallengeException);
    }

    function newChallengeSuccess(res) {
        if (res.status == 200) {
            vm.window.history.back();
        }
    }

    function newChallengeError(err) {
        console.log(err);
    }

    function newChallengeException(exp) {
        console.log(exp);
    }
}