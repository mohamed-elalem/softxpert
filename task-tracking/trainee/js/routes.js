(function() {
    angular.module("app").config(routing);
})()

function routing($routeProvider, $httpProvider) {
    $routeProvider
        .when("/", {
            "templateUrl": "templates/home.html",
        })
        .when("/login", {
            "templateUrl": "templates/login.html",
            "controller": "LoginController"
        }).when("/tasks", {
            "templateUrl": "templates/tasks.html",
            "controller": "TaskController"
        }).when("/tasks/current", {
            "templateUrl": "templates/current_tasks.html",
            "controller": "CurrentTasksController"
        }).when("/tasks/current/recommended", {
            "templateUrl": "templates/recommended_current_tasks.html",
            "controller": "RecommendedCurrentTasksController"
        });

    $httpProvider.defaults.headers.common = {};
    $httpProvider.defaults.headers.post = {};
    $httpProvider.defaults.headers.put = {};
    $httpProvider.defaults.headers.patch = {};
}