app.directive("passwordConfirm", function() {
    return {
        restrict: "A",
        require: 'ngModel',
        scope: {
            password: "=passwordConfirm"
        },
        link: function(scope, element, attr, ctrl) {
            var validator = function(repassword) {
                console.log(scope.password, repassword);
                if (scope.password === repassword) {
                    console.log("Valid");
                    ctrl.$setValidity("match", true);
                } else {
                    console.log("Invalid");
                    ctrl.$setValidity("match", false);
                }
                return repassword;
            }
            ctrl.$parsers.push(validator);

            scope.$watch("password", function() {
                console.log(ctrl);
                ctrl.$validate();
            });
        }
    }
});