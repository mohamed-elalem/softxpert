(function() {
    angular.module("app").factory("UserFactory", userFactory);
})()

function userFactory($http) {
    return {
        login: login,
        authUser: authUser,
        refreshToken: refreshToken,
        logout: logout,
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
            "url": "http://localhost:8000/api/user",
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