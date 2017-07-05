app.controller("RegisterController", function($scope, User) {
    $scope.register = function(form) {
        if (form.$valid) {
            console.log("Valid");
            User.register($scope.name, $scope.email, $scope.password);
            return true;
        }
        console.log("Invalid");
        return false;
    }
});