(function() {
    angular.module("app").controller("RegisterationController", registrationController);
})()

function registrationController($scope, $rootScope, $location, UserFactory) {
    var vim = this;
    vim.scope = $scope;
    vim.rootScope = $rootScope;
    vim.location = $location;
    vim.userFactory = UserFactory;

    vim.scope.submit = submit;

    var token = localStorage.getItem("token");
    UserFactory.getUserInfo(token).then(success, error).catch(exception);

    function submit() {
        vim.scope.createdSuccess = false;
        var username = vim.scope.username;
        var password = vim.scope.password;
        var passwordConfirmation = vim.scope.passwordConfirmation;
        var email = vim.scope.email;
        var name = vim.scope.name;

        vim.userFactory.register(token, username, name, email, password).then(success, error).catch(exception);

        function success(res) {
            if (res.status == 200) {
                vim.scope.createdSuccess = true;
            }
        }

        function error(err) {
            vim.scope.error = true;
            vim.scope.formErrors = err.data.data;
            vim.scope.errorMessages = err.data.err_message;
        }

        function exception(exp) {
            console.log(exp);
        }

    }

    function success(res) {
        $rootScope.name = res.data.data[0].name;
        $rootScope.auth = true;
    }

    function error(err) {
        console.log(err);
        $rootScope.auth = false;
    }

    function exception(exp) {
        console.log(exp);
        $rootScope.auth = false;
    }


}