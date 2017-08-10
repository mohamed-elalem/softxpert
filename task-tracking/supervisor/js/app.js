(function() {
    angular.module("app", ["ngAnimate", "ngRoute", "ui.bootstrap", ngFx, "720kb.fx", "bw.paging", "uiSwitch"]).run(bootstrap);
})()

function bootstrap($rootScope, UserFactory, $location, $route, UserFactory) {
    var vm = this;
    vm.rootScope = $rootScope;
    vm.userFactory = UserFactory;
    vm.route = $route;

    vm.rootScope.auth = false;
    vm.rootScope.logout = logout;

    var token = localStorage.getItem("token");

    if (token !== undefined) {
        authUser(token);
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
        }
    }

    function authUserError(err) {
        var refreshToken = localStorage.getItem("refreshToken");
        if (refreshToken !== null) {
            vm.userFactory.refreshToken(refreshToken).then(refreshTokenSuccess, refreshTokenError).catch(refreshTokenException);
        }

        function refreshTokenSuccess(res) {
            if (res.status == 200) {
                localStorage.setItem("token", res.data.token);
                vm.rootScope.auth = true;
                vm.route.reload();
            }

        }

        function refreshTokenError(err) {
            console.log(err);
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
    }

    function logoutError(err) {
        console.log(err);
    }

    function logoutException(exp) {
        console.log(exp);
    }

}