(function() {
    angular.module("app").controller("TraineeTasksController", traineeTasksController);
})()

function traineeTasksController($scope, $rootScope, $location, $route, $routeParams, TaskFactory) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.location = $location;
    vm.route = $route;
    vm.routeParams = $routeParams;
    vm.taskFactory = TaskFactory;

    vm.scope.page = 1;

    vm.scope.getUserTasks = getUserTasks;
    vm.scope.deleteTask = deleteTask;

    var token = localStorage.getItem("token");

    getUserTasks(1);
    console.log("There");

    function getUserTasks(page) {
        vm.scope.page = page;
        vm.taskFactory.getUserTasks(token, vm.routeParams.user_id, page).then(getUserTasksSuccess, getUserTasksError).catch(getUserTasksException);
    }

    function getUserTasksSuccess(res) {
        if (res.status == 200) {
            vm.scope.tasks = res.data.data.tasks;
            vm.scope.total = res.data.data.total;
            vm.scope.itemsPerPage = res.data.data.itemsPerPage;
        }
    }

    function getUserTasksError(err) {
        console.log(err);
    }

    function getUserTasksException(exp) {
        console.log(exp);
    }

    function deleteTask(taskId) {
        vm.taskFactory.deleteTask(token, taskId).then(deleteTaskSuccess, deleteTaskError).catch(deleteTaskException);
    }

    function deleteTaskSuccess(res) {
        if (res.status == 200) {
            getUserTasks(vm.scope.page);
        }
    }

    function deleteTaskError(err) {
        console.log(err);
    }

    function deleteTaskException(exp) {
        console.log(exp);
    }

}