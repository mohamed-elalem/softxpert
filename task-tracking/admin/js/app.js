(function() {
    angular.module("app", ["ngAnimate", "ngRoute", "ui.bootstrap", ngFx]).run(kickStarter);
})();

function kickStarter($rootScope, $location, UserFactory, $route) {
    var vim = this;
    vim.rootScope = $rootScope;
    vim.UserFactory = UserFactory;

    vim.rootScope.logout = logout;

    var token = localStorage.getItem("token");
    UserFactory.getUserInfo(token).then(userInfoSuccess, userInfoError).catch(userInfoException);

    function userInfoSuccess(res) {
        $rootScope.name = res.data.data[0].name;
        $rootScope.auth = true;
    }

    function userInfoError(err) {
        console.log(err);
        $rootScope.auth = false;
    }

    function userInfoException(exp) {
        console.log(exp);
        $rootScope.auth = false;
    }


    function logout() {
        var token = localStorage.getItem("token");
        UserFactory.logout(token).then(logoutSuccess, logoutError).catch(logoutException);
    }

    function logoutSuccess(res) {
        console.log(res);
        $rootScope.auth = false;
        localStorage.removeItem("token");
        $location.url("/login");
    }

    function logoutError(err) {
        console.log(err);
    }

    function logoutException(exp) {
        console.log(exp);
    }
}