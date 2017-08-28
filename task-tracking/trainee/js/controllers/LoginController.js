(function() {
    angular.module("app").controller("LoginController", loginController);
})()

function loginController($scope, $rootScope, $location, UserFactory, Auth) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.location = $location;
    vm.userFactory = UserFactory;

    vm.scope.login = login;


    function login() {
        vm.scope.error = false;
        vm.userFactory.login(vm.scope.username, vm.scope.password).then(loginSuccess, loginError).catch(loginException);
    }

    /**
     * Success
     */

    function loginSuccess(res) {
        console.log(res.data.token);
        if (res.status == 200) {
            var token = res.data.token;
            var refreshToken = res.data.refresh_token;
            localStorage.setItem("token", token);
            localStorage.setItem("refreshToken", refreshToken);
            vm.userFactory.authUser(token).then(loginSuccessSuccess, loginSuccessError).catch(loginSuccessException);
        }

        function loginSuccessSuccess(res) {
            var token = res.data.token;
            var refreshToken = res.data.refresh_token;
            vm.rootScope.auth = true;
            vm.location.url("/");
            Auth.loggedIn();
        }

        function loginSuccessError(err) {
            vm.rootScope.auth = false;
            if (err.status == 401) {
                vm.scope.error = true;
                localStorage.clear();
                vm.scope.errMessage = "You're not authorized to use this domain. Please contact us for more information."
            }
            Auth.logout();
        }

        function loginSuccessException(exp) {
            console.log(exp);
        }
    }

    /**
     * Error
     */

    function loginError(err) {
        vm.rootScope.auth = false;
        console.log(err);
        if (err.status == 401) {
            vm.scope.error = true;
            vm.scope.errMessage = "Username and/or password aren't correct";
        } else if (err.status == -1) {
            vm.scope.error = true;
            vm.scope.errMessage = "Server is down this moment. please try again later"
        }
    }

    /**
     * Exception
     */

    function loginException(exp) {
        console.log(exp);
    }
}
