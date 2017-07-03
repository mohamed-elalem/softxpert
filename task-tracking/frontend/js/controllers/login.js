app.controller("LoginController", function($scope, User) {
    $scope.login = function(form) {
        if (form.$valid) {
            console.log("Valid form");
            var pro = User.login($scope.email, $scope.password);

            pro.then(function(res) {
                console.log('success', res);
            }).then(function(err) {
                console.log('error', err);
            });

            return true;
        }
        console.log("Invalid form");
        return false;
    }
});