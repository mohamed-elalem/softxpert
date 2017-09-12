(function() {
    angular.module("app", ["ngAnimate", "ngMaterial", "ngAria", "ngRoute", "ui.bootstrap", ngFx, "720kb.fx", "bw.paging", "uiSwitch"]).run(bootstrap);
})()

function bootstrap($rootScope, UserFactory, $location, $route, UserFactory, Auth) {
    var vm = this;
    vm.rootScope = $rootScope;
    vm.userFactory = UserFactory;
    vm.route = $route;
    var a = 5;
    vm.rootScope.auth = false;
    vm.rootScope.filter = {};
    vm.rootScope.logout = logout;
    vm.rootScope.getFilteredResults = getFilteredResults;
    vm.rootScope.$on("$routeChangeStart", routeChangeStart);

    var token = localStorage.getItem("token");

    // if (token !== null) {
        authUser(token);
    // }

    function refreshToken(token) {
        vm.userFactory.refreshToken(token).then(refreshTokenSuccess, refreshTokenError).catch(refreshTokenException);
    }

    function logout() {
        vm.userFactory.logout(token).then(logoutSuccess, logoutError).catch(logoutException);
    }


    function authUser(token) {
        vm.userFactory.authUser(token).then(authUserSuccess, authUserError).catch(authUserException);
    }

    function authUserSuccess(res) {
        console.log(res);
        if (res.status == 200) {
            vm.rootScope.username = res.data.data[0].username;
            vm.rootScope.auth = true;
            Auth.loggedIn();
        }
    }

    function authUserError(err) {
        var refreshToken = localStorage.getItem("refreshToken");
        // vm.userFactory.refreshToken(refreshToken).then(refreshTokenSuccess, refreshTokenError).catch(refreshTokenException);
        Auth.logout();
    }

    function refreshTokenSuccess(res) {
        if (res.status == 200) {
            localStorage.setItem("token", res.data.token);
            vm.rootScope.auth = true;
            vm.route.reload();
            Auth.loggedIn();
        }

    }

    function refreshTokenError(err) {
        console.log(err);
        $location.url("/login");
        Auth.logout();
    }

    function refreshTokenException(exp) {
        console.log(exp);
    }

    function authUserException(exp) {
        console.log(exp);
    }

    function logoutSuccess(res) {
        localStorage.removeItem("token");
        localStorage.removeItem("refreshToken");
        rootScope.auth = false;
        $location.url("/");
        Auth.logout();
    }

    function logoutError(err) {
        console.log(err);
    }

    function logoutException(exp) {
        console.log(exp);
    }

    function getFilteredResults() {
        var filters = vm.rootScope.filter;
        var data = {};
        var mark = {};
        for (var filter in filters) {
            var between = false;
            var state = null;

            data[filter] = filters[filter];

            if (filter.length > 3 && (filter.substr(filter.length - 4) == "_min" || filter.substr(filter.length - 4) == "_max")) {
                between = true;
                state = filter.substr(filter.length - 4);
                filter = filter.substr(0, filter.length - 4);
            }

            if (between) {
                if (mark[filter] === undefined) {
                    mark[filter] = 1;
                } else {
                    mark[filter]++;
                }
                data["__" + filter] = true;
            } else {
                if (filters[filter] == undefined || filters[filter].length == 0) {
                    delete filters[filter];
                } else {
                    data["_" + filter] = true;
                }
            }
        }
        for (var filter in mark) {
            if (mark[filter] == 1) {
                delete data["__" + filter];
                delete data[filter + "_min"];
                delete data[filter + "_max"];
            }
        }

        if (data["__seconds"] == true) {
            data["seconds_min"] *= 3600;
            data["seconds_max"] *= 3600;
        }

        vm.rootScope.data = data;
        $location.url("/tasks");
        $route.reload();
    }

    function routeChangeStart(event, next, current) {
        var route = next || current;
        console.log(route);
        console.log(route.AuthenticationRequired, Auth.isLoggedIn());
        if(! Auth.isLoggedIn() && route.AuthenticationRequired) {
            $location.url("/login");
        }
        else if(Auth.isLoggedIn() && route.GuestRequired) {
            $location.url("/");
        }
    }
}
