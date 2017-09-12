(function() {
    angular.module("app").factory("MyInterceptor", myInterceptor);
})()

function myInterceptor($q, $injector) {

    var vm = this;
    vm.q = $q;
    vm.injector = $injector;


    var deffered = vm.q.defer();

    return {
        request: request
    }

    function request(config) {
        var token = localStorage.getItem("token");
        console.log("Inside interceptors", token);
        // var refreshToken = localStorage.getItem("refreshToken");
        //
        // if(token === null && refreshToken === null) {
        //     // to be checked later
        // }
        // else if(token === null) {
        //     var http = vm.injector.get("http")
        //     return http({
        //         "url": "http://localhost:8000/api/token/refresh",
        //         params: {
        //             "refresh_token": refreshToken
        //         }
        //     })
        //     .then(refreshTokenSuccess, refreshTokenError).catch(refreshTokenException);
        // }
        // else {
        //     config.headers["Authorization"] = "Bearer " + token;
        //     return config;
        // }
        config.headers["Authorization"] = "Bearer " + token;
        return config;

        function refreshTokenSuccess(res) {
            localStorage.setItem("token", res.data.token);
            config.headers["Authorization"] = "Bearer " + token;
            return config;
        }

        function refreshTokenError(err) {
            console.log(err);
            return config;
        }

        function refreshTokenException(exp) {
            console.log(exp);
            return config;
        }
    }
}
