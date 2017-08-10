(function() {
    angular.module("app").controller("HomeController", homeController);
})()

function homeController($scope, $rootScope) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
}