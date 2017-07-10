app.config(function($routeProvider, $httpProvider) {
    $routeProvider
        .when("/", {
            templateUrl: "/templates/home.html",
            access: ALLOWED
        })
        .when("/login", {
            templateUrl: "/templates/login.html",
            controller: "LoginController",
            access: ANONYMOUS,
        })
        .when("/admin/login", {
            templateUrl: "/templates/login.html",
            controller: "LoginController",
            access: ANONYMOUS,
        })
        .when("/admin/login", {
            templateUrl: "/templates/login.html",
            controller: "LoginController",
            access: ANONYMOUS,
        })
        .when("/register", {
            templateUrl: "/templates/register.html",
            controller: "RegisterController",
            access: ANONYMOUS
        })

    $httpProvider.defaults.headers.common = {};
    $httpProvider.defaults.headers.post = {};
    $httpProvider.defaults.headers.put = {};
    $httpProvider.defaults.headers.patch = {};
});