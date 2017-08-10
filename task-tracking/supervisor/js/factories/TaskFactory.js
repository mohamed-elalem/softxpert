(function() {
    angular.module("app").factory("TaskFactory", taskFactory);
})()

function taskFactory($http) {
    return {
        getUserTasks: getUserTasks,
        deleteTask: deleteTask,
        createNewTask: createNewTask
    }

    function getUserTasks(token, userId, page) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/trainees/" + userId + "/tasks/" + page,
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function deleteTask(token, taskId) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/trainees/" + taskId + "/tasks",
            "method": "delete",
            "headers": {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/x-www-form-urlencoded"
            },
            "transformRequest": transformRequest,
            "data": {
                "task_id": taskId
            }
        });
    }

    function createNewTask(token, userId, challengeId) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/trainees/" + user_id + "/tasks",
            "method": "post",
            "headers": {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/x-www-form-urlencoded"
            },
            "data": {
                "challenge_id": challengeId,
                "userId": userId
            }
        });
    }

    function transformRequest(obj) {
        var str = [];
        for (var p in obj)
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        return str.join("&");
    }
}