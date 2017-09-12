const ADMIN = 1;
const SUPERVISOR = 2;
const TRAINEE = 4;

(function() {
    angular.module("app").factory("UserFactory", userFactory);

})()

function userFactory($http) {

    return {
        login: login,
        getUserInfo: getUserInfo,
        register: register,
        getUsers: getUsers,
        getSingleUser: getSingleUser,
        logout: logout,
        deleteUser: deleteUser,
        refreshLogin: refreshLogin
    }

    function login(username, password) {
        return $http({
            method: "POST",
            url: "http://localhost:8000/api/login_check",
            transformRequest: function(obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            data: {
                "_username": username,
                "_password": password
            }
        });

    }

    function register(token, username, name, email, password) {
        console.log("dfknsdkndsf", token);
        return $http({
            url: "http://localhost:8000/api/admin/supervisors",
            method: "post",
            headers: {
                // "Authorization": "Bearer " + token,
                "Content-Type": "application/x-www-form-urlencoded"
            },
            transformRequest: function(obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            data: {
                "username": username,
                "name": name,
                "email": email,
                "password": password
            }
        });
    }

    function getUserInfo(token) {
        return $http({
            "url": "http://localhost:8000/api/admin",
            "headers": {
                // "Authorization": "Bearer " + token
            }
        });
    }

    function getUsers(token, data = {}, page = 1) {
        data.page = page;
        return $http({
            "url": "http://localhost:8000/api/admin/users",
            "method": "get",
            "params": data,
            "headers": {
                // "Authorization": "Bearer " + token
            },
        })
    }

    function getSingleUser(token, id) {
        return $http({
            "url": "http://localhost:8000/api/admin/users/" + id,
            "headers": {
                // "Authorization": "Bearer " + token
            }
        })
    }

    function logout(token) {
        return $http({
            "url": "http://localhost:8000/api/logout",
            "method": "delete",
            "headers": {
                // "Authorization": "Bearer " + token
            }
        });
    }

    function deleteUser(token, id) {
        return $http({
            "url": "http://localhost:8000/api/admin/users",
            "method": "delete",
            transformRequest: function(obj) {
                var str = [];
                for (var p in obj)
                    str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                return str.join("&");
            },
            "data": {
                "id": id
            },
            "headers": {
                // "Authorization": "Bearer " + token,
                "Content-Type": "application/x-www-form-urlencoded"
            },
        });
    }

    function refreshLogin(refreshToken) {
        return $http({
            "url": "http://localhost:8000/api/token/refresh",
            "params": {
                "refresh_token": refreshToken
            }
        });
    }
}
