(function() {
    angular.module("app").controller("LoginController", LoginController)
})()

function LoginController($scope, $location, $rootScope, UserFactory, Auth) {

    vim = this;
    vim.scope = $scope;
    vim.rootScope = $rootScope;
    vim.location = $location;
    vim.userFactory = UserFactory;
    console.log(vim);
    vim.scope.submit = submit;

    /**
     * Handling submitting form
     * @param {} loginForm
     */

    function submit(loginForm) {
        vim.scope.loginError = false;
        vim.userFactory.login($scope.username, $scope.password).then(success, error).catch(exception);


        /**
         * Handling Login request Promise success
         * @param {*} res
         */
        function success(res) {
            if (res.status == 200) {
                var token = res.data.token;
                var refreshToken = res.data.refresh_token;
                localStorage.setItem("token", token);
                localStorage.setItem("refreshToken", refreshToken);
                vim.userFactory.getUserInfo(token).then(success, error).catch(exception);
            }

            /**
             * Handling User info request success
             * @param {*} res
             */

            function success(res) {
                var user = res.data.data[0];

                vim.rootScope.name = user.name;
                vim.rootScope.auth = true;
                Auth.loggedIn();
                vim.location.path("/");
            }

            /**
             * Handling User info request reject
             * @param {*} err
             */

            function error(err) {
                vim.scope.loginError = true;
                vim.scope.errorMessage = "You're not authorized to access this domain. Please contact us for more information";
                Auth.logout();
                localStorage.clear();
            }

            /**
             * Handling User info request exceptions
             * @param {*} exp
             */

            function exception(exp) {
                console.log(exp);
            }
        }

        /**
         * Handling login request reject
         * @param {*} err
         */

        function error(err) {
            console.log(err);
            if (err.status == 401) {
                vim.scope.loginError = true;
                vim.scope.errorMessage = "Invalid Credintials please supply the correct ones";
            } else if (err.status == -1) {
                vim.scope.loginError = true;
                vim.scope.errorMessage = "Server is down this moment. please try again later"
            }
        }

        /**
         * Handling login exceptions
         * @param {*} exp
         */

        function exception(exp) {
            console.log(exp);
        }
    }
}
