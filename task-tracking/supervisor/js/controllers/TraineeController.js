(function() {
    angular.module("app").controller("TraineeController", traineeController);
})()

function traineeController($scope, $rootScope, $location, UserFactory, $routeParams) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.location = $location;
    vm.userFactory = UserFactory;
    vm.routeParams = $routeParams;

    vm.scope.getAllTrainees = getAllTrainees;

    var token = localStorage.getItem("token");

    getAllTrainees(1);


    function getAllTrainees(page) {
        vm.scope.page = page;
        vm.userFactory.getTrainees(token, vm.scope.page).then(getTraineesSuccess, getTraineesError).catch(getTraineesException);
    }


    function getTraineesSuccess(res) {
        vm.scope.users = res.data.data.users;
        vm.scope.total = res.data.data.total;
        vm.scope.itemsPerPage = res.data.data.itemsPerPage;
        console.log(res);
    }

    function getTraineesError(err) {
        console.log(err);
    }

    function getTraineesException(exp) {
        console.log(exp);
    }

}