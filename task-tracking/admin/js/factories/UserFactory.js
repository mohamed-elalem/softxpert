(function() {
    angular.module("app").factory("UserFactory", userFactory);

    function userFactory() {
        return {
            login: login,
            getUserInfo: getUserInfo,
            register: register
        }

        function login(username, password) {
            return $http({
                    url: "http://localhost/api/login_check"
                }).then(success)
                .then(failure);

            function success(response) {
                return response.data;
            }

            function failure(err) {
                return err.data;
            }

        }

        function register(username, email, name, password) {
            return $http({

                })
                .then(success)
                .then(failure)

            function success(response) {
                return response.data;
            }

            function failure(err) {
                return response.data;
            }
        }
    }

    userFactory.$inject($http);
})()