app.controller("LoginController", function($location, $scope, User, $rootScope) {
    $scope.invalidCredintials = false;
    $scope.messages = MESSAGES;
    var path = $location.path().split("/");
    console.log("Role is", $rootScope.role);
    $scope.login = function(form) {
        if (form.$valid) {
            console.log("Valid form");
            var role = $rootScope.role;
            User.login($scope.username, $scope.password)
                .then(function(res) {
                    console.log(res);
                    if (res.data.hasOwnProperty("token")) {
                        localStorage.setItem("token", res.data.token);
                        $location.path("/");
                    } else {

                    }
                }).catch(function(err) {
                    if (err.data.hasOwnProperty("code") && err.data.code == 401) {
                        $scope.invalidCredintials = true;
                        $scope.err = INVALID_CREDINTIALS;
                    } else {
                        $scope.failure = true;
                        $scope.err = FAILED;
                    }
                });

            return true;
        }
        console.log("Invalid form");
        return false;
    }
});