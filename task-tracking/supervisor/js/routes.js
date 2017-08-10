(function() {
    angular.module("app").config(router);
})()

function router($routeProvider, $httpProvider) {
    $routeProvider
        .when("/", {
            templateUrl: "templates/home.html",
            controller: "HomeController"
        }).when("/login", {
            templateUrl: "templates/login.html",
            controller: "LoginController"
        }).when("/trainees", {
            templateUrl: "templates/trainees.html",
            controller: "TraineeController"
        })
        .when("/trainees/:user_id/challenges", {
            templateUrl: "templates/assign_challenges.html",
            controller: "FreeChallengeController"
        }).when("/challenges", {
            templateUrl: "templates/my_challenges.html",
            controller: "MyChallengesController"
        }).when("/challenges/new", {
            templateUrl: "templates/new_challenge.html",
            controller: "NewChallengeController"
        }).when("/challenges/:challenge_id", {
            templateUrl: "templates/children.html",
            controller: "ChallengeChildrenController"
        }).when("/trainees/:user_id/tasks", {
            templateUrl: "templates/tasks.html",
            controller: "TraineeTasksController"
        });


    $httpProvider.defaults.headers.common = {};
    $httpProvider.defaults.headers.post = {};
    $httpProvider.defaults.headers.put = {};
    $httpProvider.defaults.headers.patch = {};
}