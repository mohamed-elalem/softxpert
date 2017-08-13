(function() {
    angular.module("app").factory("UserFactory", userFactory);
})()

function userFactory($http) {
    return {
        login: login,
        authUser: authUser,
        refreshToken: refreshToken,
        logout: logout,
        getTrainees: getTrainees,
        getSingleUser: getSingleUser
    }

    function login(username, password) {
        return $http({
            "url": "http://localhost:8000/api/login_check",
            "method": "post",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            "transformRequest": toFormData,
            "data": {
                "_username": username,
                "_password": password
            }
        });
    }

    function authUser(token) {
        return $http({
            "url": "http://localhost:8000/api/supervisor",
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function refreshToken(token) {
        return $http({
            "url": "http://localhost:8000/api/token/refresh",
            params: {
                "refresh_token": token
            }
        });
    }


    function logout(token) {
        return $http({
            "url": "http://localhost:8000/api/logout",
            "method": "delete",
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function getTrainees(token, page = 1) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/trainees/" + page,
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function getSingleUser(token, user_id) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/trainees/" + user_id + "/info",
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }


    /**
     * Helpers
     */

    function toFormData(obj) {
        var str = [];
        for (var p in obj)
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        return str.join("&");
    }
}