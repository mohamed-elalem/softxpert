(function() {
    angular.module("app").controller("LoginController", LoginController)
})()

function LoginController($scope, $location, $rootScope) {
    var vm = this;
    console.log(vm);
}