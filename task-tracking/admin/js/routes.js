(function() {
    angular.module("app").config(function($routeProvider, $httpProvider) {
        $routeProvider.when("/", {
                templateUrl: "/templates/home.html",
                "controller": "HomeController",
                "AuthenticationRequired": false,
                "GuestRequired": false
            })
            .when("/login", {
                templateUrl: "/templates/login.html",
                controller: "LoginController",
                "AuthenticationRequired": false,
                "GuestRequired": true
            }).when("/register", {
                templateUrl: "/templates/register.html",
                controller: "RegisterationController",
                "AuthenticationRequired": false,
                "GuestRequired": true
            }).when("/users/", {
                templateUrl: "/templates/users.html",
                controller: "UserController",
                "AuthenticationRequired": true,
                "GuestRequired": false
            }).when("/users/:id", {
                templateUrl: "/templates/user.html",
                controller: "UserController",
                "AuthenticationRequired": true,
                "GuestRequired": false
            });
        $httpProvider.defaults.headers.common = {};
        $httpProvider.defaults.headers.post = {};
        $httpProvider.defaults.headers.put = {};
        $httpProvider.defaults.headers.patch = {};
        $httpProvider.interceptors.push('MyInterceptor');
    });
})()
