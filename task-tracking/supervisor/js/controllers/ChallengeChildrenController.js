(function() {
    angular.module("app").controller("ChallengeChildrenController", challengeChildrenController);
})()

function challengeChildrenController($scope, $routeParams, ChallengeFactory, $window, $route) {
    var vm = this;
    vm.scope = $scope;
    vm.routeParams = $routeParams;
    vm.challengeFactory = ChallengeFactory;
    vm.window = $window;
    vm.route = $route;

    vm.scope.page = 1;
    vm.scope.error = false;
    vm.scope.cycles = {};
    vm.scope.getChallengeChildren = getChallengeChildren;
    vm.scope.connect = connect;


    var token = localStorage.getItem("token");
    getChallenge();
    getChallengeChildren(1);

    function getChallengeChildren(page) {
        vm.scope.error = false;
        vm.scope.cycles = {}
        vm.scope.page = page;
        vm.challengeFactory.getChallengeChildren(token, vm.routeParams.challenge_id, page).then(getChallengeChildrenSuccess, getChallengeChildrenError).catch(getChallengeChildrenException);
    }

    function getChallengeChildrenSuccess(res) {
        if (res.status == 200) {
            console.log(res);
            vm.scope.challenges = res.data.data.challenges;
            vm.scope.total = res.data.data.total;
            vm.scope.itemsPerPage = res.data.data.itemsPerPage;
        }
    }

    function getChallengeChildrenError(err) {
        console.log(err);
    }

    function getChallengeChildrenException(exp) {
        console.log(exp);
    }

    function connect(challengeId) {
        vm.challengeFactory.connect(token, vm.routeParams.challenge_id, challengeId).then(connectSuccess, connectError).catch(connectException);
    }

    function connectSuccess(res) {
        if (res.status == 200) {
            getChallengeChildren(vm.scope.page);
        }
    }

    function connectError(err) {
        if (err.status == 406) {
            vm.scope.error = true;
            vm.scope.cycles = err.data.data.cycles;
        }
    }

    function connectException(exp) {
        console.log(exp);
    }


    function getChallenge() {
        vm.challengeFactory.getChallenge(token, vm.routeParams.challenge_id).then(getChallengeSuccess, getChallengeError).catch(getChallengeException);
    }

    function getChallengeSuccess(res) {
        if (res.status == 200) {
            vm.scope.challenge = res.data.data.challenge[0];
        }
    }

    function getChallengeError(err) {
        console.log(err);
    }

    function getChallengeException(exp) {
        console.log(exp);
    }
}
