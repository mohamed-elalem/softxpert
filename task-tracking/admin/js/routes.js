(function() {
    angular.module("app").config(function($routeProvider, $httpProvider) {
        $routeProvider.when("/", {
                templateUrl: "/templates/home.html",
                "controller": "HomeController"
            })
            .when("/login", {
                templateUrl: "/templates/login.html",
                controller: "LoginController"
            }).when("/register", {
                templateUrl: "/templates/register.html",
                controller: "RegisterationController"
            }).when("/users/", {
                templateUrl: "/templates/users.html",
                controller: "UserController"
            }).when("/users/:id", {
                templateUrl: "/templates/user.html",
                controller: "UserController"
            });
        $httpProvider.defaults.headers.common = {};
        $httpProvider.defaults.headers.post = {};
        $httpProvider.defaults.headers.put = {};
        $httpProvider.defaults.headers.patch = {};
    });
})()