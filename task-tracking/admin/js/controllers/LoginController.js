(function() {
    angular.module("app").controller("LoginController", LoginController)
})()

function LoginController($scope, $location, $rootScope, UserFactory) {

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
        vim.scope.invalidCredintials = false;
        vim.scope.forbidden = false;
        vim.userFactory.login($scope.username, $scope.password).then(success, error).catch(exception);


        /**
         * Handling Login request Promise success
         * @param {*} res 
         */
        function success(res) {
            if (res.status == 200) {
                var token = res.data.token;
                var refreshToken = res.data.refresh_token;
                vim.userFactory.getUserInfo(token).then(success, error).catch(exception);
            }

            /**
             * Handling User info request success
             * @param {*} res 
             */

            function success(res) {
                var user = res.data.data[0];
                localStorage.setItem("token", token);
                localStorage.setItem("refreshToken", refreshToken);
                vim.rootScope.name = user.name;
                vim.rootScope.auth = true;
                vim.location.path("/");
            }

            /**
             * Handling User info request reject
             * @param {*} err 
             */

            function error(err) {
                console.log("Forbidden access");
                vim.scope.forbidden = true;
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
                vim.scope.invalidCredintials = true;
                console.log("Invalid credintials");
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