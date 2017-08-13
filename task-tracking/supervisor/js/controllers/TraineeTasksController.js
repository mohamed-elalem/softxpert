(function() {
    angular.module("app").controller("TraineeTasksController", traineeTasksController);
})()

function traineeTasksController($scope, $rootScope, $location, $route, $routeParams, TaskFactory, $timeout) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.location = $location;
    vm.route = $route;
    vm.routeParams = $routeParams;
    vm.taskFactory = TaskFactory;
    vm.timeout = $timeout;

    vm.scope.page = 1;

    vm.scope.getUserTasks = getUserTasks;
    vm.scope.deleteTask = deleteTask;
    vm.scope.setScore = setScore;
    vm.scope.setDone = setDone;

    var token = localStorage.getItem("token");
    var updateSuccessMessage = "Task is now updated";
    var updateErrorMessage = "Error occured, task was not updated";
    var updateState = 0;

    getUserTasks(1);

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

    function setDone(taskId, done) {
        vm.taskFactory.setDone(token, vm.routeParams.user_id, taskId, done).then(setDoneSuccess, setDoneError).catch(setDoneException);
    }

    function setDoneSuccess(res) {
        if (res.status == 200) {
            vm.scope.updateState = 1;
            vm.timeout(function() {
                vm.scope.updateState = 0;
            }, 10000);
            vm.scope.updateMessage = updateSuccessMessage;
            getUserTasks(vm.scope.page);
        }
    }

    function setDoneError(err) {
        vm.scope.updateState = 2;
        vm.timeout(function() {
            vm.scope.updateState = 0;
        }, 10000);
        vm.scope.updateMessage = updateErrorMessage;
    }

    function setDoneException(exp) {
        console.log(exp);
    }

    function setScore(e, taskId, score) {
        if (e.keyCode == 13)
            vm.taskFactory.setScore(token, vm.routeParams.user_id, taskId, score).then(setScoreSuccess, setScoreError).catch(setScoreException);
    }

    function setScoreSuccess(res) {
        if (res.status == 200) {
            vm.scope.updateState = true;
            vm.timeout(function() {
                vm.scope.updateState = 0;
            }, 10000);
            vm.scope.updateMessage = updateSuccessMessage;
        }
    }

    function setScoreError(err) {
        vm.scope.updateState = 2;
        vm.timeout(function() {
            vm.scope.updateState = 0;
        }, 10000);
        vm.scope.updateMessage = updateErrorMessage;
    }

    function setScoreException(exp) {
        console.log(exp);
    }

}