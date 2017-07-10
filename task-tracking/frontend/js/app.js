var app = angular.module("starter", ["ngRoute", "ngAnimate"]);
app.run(function($rootScope, User, $location) {
    console.log("Inside run");
    $rootScope.auth = false;

    var updateName = function() {
        console.log("Updating name");
        if (localStorage.getItem("token") !== null) {
            User.getUserInfo()
                .then(function(res) {
                    console.log(res);
                    if (res.data.hasOwnProperty("extra")) {
                        localStorage.setItem("name", res.data.extra.name);
                        $rootScope.auth = true;
                        $rootScope.name = res.data.extra.name;
                    }
                }).then(function(err) {});
        }
    }

    updateName();

    $rootScope.$on("$routeChangeSuccess", function($currentRoute, $previousRoute) {
        updateName();
    });


    $rootScope.$on("$routeChangeStart", function(event, next, current) {
        var route = next || current;
        console.log(route, $rootScope.auth);
        if (route.access == AUTHENTICATED && $rootScope.auth == false) {
            $location.path("/" + $rootScope.role);
        } else if (route.access == ANONYMOUS && $rootScope.auth == true) {
            $location.path("/" + $rootScope.role);
        }
    });
});