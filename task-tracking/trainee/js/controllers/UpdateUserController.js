(function() {
    angular.module("app").controller("RegistrationController", registrationController);
})()

function registrationController($scope, $rootScope, $location, UserFactory) {
    var vim = this;
    vim.scope = $scope;
    vim.rootScope = $rootScope;
    vim.location = $location;
    vim.userFactory = UserFactory;

    vim.scope.submit = submit;

    var token = localStorage.getItem("token");

    function submit() {
        vim.scope.createdSuccess = false;
        var username = vim.scope.supervisor.username;
        var password = vim.scope.supervisor.password;
        var passwordConfirmation = vim.scope.supervisor.passwordConfirmation;
        var email = vim.scope.supervisor.email;
        var name = vim.scope.supervisor.name;

        vim.userFactory.register(token, username, name, email, password).then(success, error).catch(exception);

        function success(res) {
            if (res.status == 200) {
                vim.scope.createdSuccess = true;
            }
        }

        function error(err) {
            console.log(err);
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