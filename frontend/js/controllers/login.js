app.controller("LoginController", function($scope, User) {
    $scope.login = function(form) {
        if (form.$valid) {
            console.log("Valid form");
            User.login($scope.email, $scope.password);
            return true;
        }
        console.log("Invalid form");
        return false;
    }
});