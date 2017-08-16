(function() {
    angular.module("app").controller("CurrentTasksController", currentTasksController);
})()

function currentTasksController($scope, $rootScope, TaskFactory, $route) {
    var vm = this;
    vm.scope = $scope;
    vm.rootScope = $rootScope;
    vm.route = $route;
    vm.taskFactory = TaskFactory;

    var token = localStorage.getItem("token");
    console.log("token");
    getCurrentTasks();


    function getMaxPriority(challenges) {
        var maxPriority = 0;
        var n = challenges.length;
        for (var i = 0; i < n; i++) {
            maxPriority = Math.max(maxPriority, challenges[i][1]);
        }
        return maxPriority;
    }


    function getCurrentTasks() {
        vm.taskFactory.getCurrentTasks(token).then(getCurrentTasksSuccess, getCurrentTasksError).catch(getCurrentTasksException);
    }

    function getCurrentTasksSuccess(res) {
        if (res.status == 200) {
            console.log(res);
            vm.scope.challenges = res.data.data.priority;
            vm.scope.maxPriority = getMaxPriority(vm.scope.challenges);
        }
    }

    function getCurrentTasksError(err) {
        console.log(err);
    }

    function getCurrentTasksException(exp) {
        console.log(exp);
    }
}