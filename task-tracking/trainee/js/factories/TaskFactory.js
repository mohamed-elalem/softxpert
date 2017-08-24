(function() {
    angular.module("app").factory("TaskFactory", taskFactory);
})()


function taskFactory($http) {
    return {
        getFilteredTasks: getFilteredTasks,
        toggleTaskInProgress: toggleTaskInProgress,
        getCurrentTasks: getCurrentTasks,
        getCurrentRecommendedTasks: getCurrentRecommendedTasks
    }

    function getFilteredTasks(token, data, page) {
        data.page = page;
        return $http({
            "url": "http://localhost:8000/api/tasks",
            "params": data,
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function getCurrentTasks(token) {
        return $http({
            "url": "http://localhost:8000/api/tasks",
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function getCurrentRecommendedTasks(token) {
        return $http({
            "url": "http://localhost:8000/api/tasks/recommended",
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function toggleTaskInProgress(token, task_id) {
        return $http({
            "url": "http://localhost:8000/api/tasks/" + task_id + "/in_progress/toggle",
            "method": "put",
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }
}