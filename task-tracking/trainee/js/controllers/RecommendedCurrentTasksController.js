(function() {
    angular.module("app").controller("RecommendedCurrentTasksController", currentRecommendedTasks);
})()

function currentRecommendedTasks($scope, $rootScope, $route, TaskFactory) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.route = $route;
    vm.taskFactory = TaskFactory;

    var token = localStorage.getItem("token");
    console.log("token");
    getCurrentRecommendedTasks();



    function getCurrentRecommendedTasks() {
        vm.taskFactory.getCurrentRecommendedTasks(token).then(getCurrentRecommendedTasksSuccess, getCurrentRecommendedTasksError).catch(getCurrentRecommendedTasksException);
    }

    function getCurrentRecommendedTasksSuccess(res) {
        if (res.status == 200) {
            vm.scope.challenges = res.data.data.priority;
        }
    }

    function getCurrentRecommendedTasksError(err) {
        console.log(err);
    }

    function getCurrentRecommendedTasksException(exp) {
        console.log(exp);
    }
}