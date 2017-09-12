(function() {
    angular.module("app", ["ngAnimate", "ngRoute", "ui.bootstrap", ngFx, "720kb.fx", "bw.paging", "uiSwitch"]).run(bootstrap);
})()

function bootstrap($rootScope, UserFactory, $location, $route, UserFactory, Auth) {
    var vm = this;
    vm.rootScope = $rootScope;
    vm.userFactory = UserFactory;
    vm.route = $route;
    vm.rootScope.auth = false;
    vm.rootScope.logout = logout;
    vm.rootScope.$on("$routeChangeStart", routeChangeStart);


    var token = localStorage.getItem("token");

    if (token !== null) {
        authUser(token);
    }
    else {
        Auth.logout();
    }

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
        if (refreshToken !== null) {
            vm.userFactory.refreshToken(refreshToken).then(refreshTokenSuccess, refreshTokenError).catch(refreshTokenException);
        }
        else {
            Auth.logout();
        }

        function refreshTokenSuccess(res) {
            if (res.status == 200) {
                localStorage.setItem("token", res.data.token);
                vm.rootScope.auth = true;
                Auth.loggedIn();
                vm.route.reload();
            }
        }

        function refreshTokenError(err) {
            console.log(err);
            Auth.logout();
        }

        function refreshTokenException(exp) {
            console.log(exp);
        }
    }

    function authUserException(exp) {
        console.log(exp);
    }

    function logoutSuccess(res) {
        localStorage.removeItem("token");
        localStorage.removeItem("refreshToken");
        rootScope.auth = false;
        Auth.logout();
        $location.url("/");
    }

    function logoutError(err) {
        console.log(err);
    }

    function logoutException(exp) {
        console.log(exp);
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
