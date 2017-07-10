app.controller("RegisterController", function($scope, User, $location) {
    $scope.registered = false;
    $scope.failure = false;
    $scope.messages = MESSAGES;
    $scope.register = function(form) {
        if (form.$valid) {
            console.log("Valid");
            User.register($scope.username, $scope.name, $scope.email, $scope.password)
                .then(function(res) {
                    console.log(res);
                    if (res.data.code == EXIST) {
                        $scope.err = EXIST;
                        $scope.registered = true;
                    } else if (res.data.code == FAILED) {
                        $scope.failure = true;
                        $scope.err = FAILED;
                    } else {
                        $location.path("/login");
                    }
                }).then(function(err) {
                    console.log(err);
                });
        }
        console.log("Invalid");
        return false;
    }

    $scope.clear = function() {
        $scope.register = false;
        $scope.failure = false;
    }
});