(function() {
    angular.module("app").config(routing);
})()

function routing($routeProvider, $httpProvider) {
    $routeProvider
        .when("/", {
            "templateUrl": "templates/home.html",
            "restricted": false
        })
        .when("/login", {
            "templateUrl": "templates/login.html",
            "controller": "LoginController",
            "restricted": false
        }).when("/tasks", {
            "templateUrl": "templates/tasks.html",
            "controller": "TaskController",
            "restricted": true
        }).when("/tasks/current", {
            "templateUrl": "templates/current_tasks.html",
            "controller": "CurrentTasksController",
            "restricted": true
        }).when("/tasks/current/recommended", {
            "templateUrl": "templates/recommended_current_tasks.html",
            "controller": "RecommendedCurrentTasksController",
            "restricted": true
        }).when("/register", {
            "templateUrl": "templates/register.html",
            "controller": "RegistrationController",
            "restricted": false
        });

    $httpProvider.defaults.headers.common = {};
    $httpProvider.defaults.headers.post = {};
    $httpProvider.defaults.headers.put = {};
    $httpProvider.defaults.headers.patch = {};
    $httpProvider.interceptors.push('MyInterceptor');
}
