(function() {
    angular.module("app").controller("TaskController", taskController);
})()

function taskController($scope, $rootScope, TaskFactory, $route) {
    var vm = this;

    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.taskFactory = TaskFactory;
    vm.route = $route;

    vm.scope.getFilteredTasks = getFilteredTasks;
    vm.scope.toggleTaskInProgress = toggleTaskInProgress;
    vm.scope.page = 1;


    var token = localStorage.getItem("token");


    function getFilteredTasks(page) {
        vm.scope.page = page;
        vm.taskFactory.getFilteredTasks(token, vm.rootScope.data || {}, page).then(getFilteredTasksSuccess, getFilteredTasksError).catch(getFilteredTasksException);
    }
    getFilteredTasks(1);


    function getFilteredTasksSuccess(res) {
        if (res.status == 200) {
            var tasks = res.data.data.tasks;
            vm.scope.tasks = tasks;
            vm.scope.itemsPerPage = res.data.data.itemsPerPage;
            vm.scope.total = res.data.data.total;
        }
    }

    function getFilteredTasksError(err) {
        console.log(err);
    }

    function getFilteredTasksException(exp) {
        console.log(exp);
    }

    function toggleTaskInProgress(task_id) {
        vm.taskFactory.toggleTaskInProgress(token, task_id).then(toggleTaskInProgressSuccess, toggleTaskInProgressError).catch(toggleTaskInProgressException);
    }

    function toggleTaskInProgressSuccess(res) {
        if (res.status == 200) {
            vm.route.reload();
        }
    }

    function toggleTaskInProgressError(err) {

    }

    function toggleTaskInProgressException(exp) {

    }

}