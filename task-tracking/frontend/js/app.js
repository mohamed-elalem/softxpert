var app = angular.module("starter", ["ngRoute", "ngAnimate"]);
app.run(function($rootScope, User, $location) {
    console.log("Inside run");
    $rootScope.auth = false;

    var updateName = function() {
        console.log("Updating name");
        if (localStorage.getItem("token") !== null) {
            if (!$rootScope.name) {
                User.getUserInfo()
                    .then(function(res) {
                        console.log(res);
                        if (res.data.hasOwnProperty("extra")) {
                            localStorage.setItem("name", res.data.extra.name.split("\s+")[0]);
                            $rootScope.auth = true;
                            $rootScope.name = res.data.extra.name.split()[0];
                            $rootScope.role = res.data.extra.role;
                            $rootScope.roleLocation = res.data.extra.role + "/";
                        }
                    }).then(function(err) {});
            }
        }
    }

    updateName();

    $rootScope.$on("$routeChangeSuccess", function($currentRoute, $previousRoute) {
        updateName();
        var path = $location.path().split("/");
        var role = '';
        if (path.length > 2) {
            role = path[2];
        }
        $rootScope.role = role;
    });


    $rootScope.$on("$routeChangeStart", function(event, next, current) {
        var route = next || current;
        console.log(route);
        var path = $location.path().split("/");
        var role = '';
        if (path.length > 2) {
            role = path[2];
        }
        console.log('checking role ', role);
        if ($rootScope.authenticated && route.access == AUTHENTICATED) {
            if ($rootScope.authenticated && route.access == ANONYMOUS) {
                $location.path("/" + $rootScope.role);
            } else if (role != $rootScope.role) {
                $location.path("/" + $rootScope.role);
            }
        }
        // if (route.access == AUTHENTICATED && $rootScope.auth == false) {
        //     $location.path("/" + $rootScope.role);
        // } else if (route.access == ANONYMOUS && $rootScope.auth == true) {
        //     $location.path("/" + $rootScope.role);
        // }
    });
});