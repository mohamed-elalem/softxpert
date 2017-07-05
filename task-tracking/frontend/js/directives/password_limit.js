app.directive("passwordLimit", function() {
    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attr, ctrl) {
            function passwordLimit() {
                var password = element[0].value;
                console.log(password.length);
                if (password.length >= 6 && password.length <= 60) {
                    ctrl.$setValidity("match", true);
                    console.log("Valid");
                } else {
                    ctrl.$setValidity("match", false);
                    console.log("Invalid");
                }
                return password;
            }
            ctrl.$parsers.push(passwordLimit);
        }
    }
});