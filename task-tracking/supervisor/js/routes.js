(function() {
    angular.module("app").config(router);
})()

function router($routeProvider, $httpProvider) {
    $routeProvider
        .when("/", {
            templateUrl: "templates/home.html",
            controller: "HomeController",
            "AuthenticationRequired": false,
            "GuestRequired": false
        }).when("/login", {
            templateUrl: "templates/login.html",
            controller: "LoginController",
            "AuthenticationRequired": false,
            "GuestRequired": true
        }).when("/trainees", {
            templateUrl: "templates/trainees.html",
            controller: "TraineeController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        })
        .when("/trainees/:user_id/challenges", {
            templateUrl: "templates/assign_challenges.html",
            controller: "FreeChallengeController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        }).when("/challenges", {
            templateUrl: "templates/my_challenges.html",
            controller: "MyChallengesController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        }).when("/challenges/new", {
            templateUrl: "templates/new_challenge.html",
            controller: "NewChallengeController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        }).when("/challenges/:challenge_id", {
            templateUrl: "templates/children.html",
            controller: "ChallengeChildrenController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        }).when("/trainees/:user_id/tasks", {
            templateUrl: "templates/tasks.html",
            controller: "TraineeTasksController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        });


    $httpProvider.defaults.headers.common = {};
    $httpProvider.defaults.headers.post = {};
    $httpProvider.defaults.headers.put = {};
    $httpProvider.defaults.headers.patch = {};
    $httpProvider.interceptors.push('MyInterceptor');
}
