(function() {
    angular.module("app").config(routing);
})()

function routing($routeProvider, $httpProvider) {
    $routeProvider
        .when("/", {
            "templateUrl": "templates/home.html",
            "AuthenticationRequired": false,
            "GuestRequired": false
        })
        .when("/login", {
            "templateUrl": "templates/login.html",
            "controller": "LoginController",
            "AuthenticationRequired": false,
            "GuestRequired": true
        }).when("/tasks", {
            "templateUrl": "templates/tasks.html",
            "controller": "TaskController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        }).when("/tasks/current", {
            "templateUrl": "templates/current_tasks.html",
            "controller": "CurrentTasksController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        }).when("/tasks/current/recommended", {
            "templateUrl": "templates/recommended_current_tasks.html",
            "controller": "RecommendedCurrentTasksController",
            "AuthenticationRequired": true,
            "GuestRequired": false
        }).when("/register", {
            "templateUrl": "templates/register.html",
            "controller": "RegistrationController",
            "AuthenticationRequired": false,
            "GuestRequired": true
        }).otherwise({
            "templateUrl": "templates/404.html"
        });

    $httpProvider.defaults.headers.common = {};
    $httpProvider.defaults.headers.post = {};
    $httpProvider.defaults.headers.put = {};
    $httpProvider.defaults.headers.patch = {};
    $httpProvider.interceptors.push('MyInterceptor');
}
