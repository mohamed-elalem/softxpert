app.config(function($routeProvider) {
    $routeProvider
        .when("/", {
            templateUrl: "/templates/home.html"
        })
        .when("/login", {
            templateUrl: "/templates/login.html",
            controller: "LoginController"
        })
        .when("/register", {
            templateUrl: "/templates/register.html",
            controller: "RegisterController"
        });
});