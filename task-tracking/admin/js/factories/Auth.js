(function() {
    angular.module("app").factory("Auth", auth);
})()

function auth() {
    var vm = this;
    vm.auth = true;

    return {
        loggedIn: loggedIn,
        isLoggedIn: isLoggedIn,
        logout: logout,
    }

    function loggedIn() {
        vm.auth = true;
    }

    function isLoggedIn() {
        return vm.auth;
    }

    function logout() {
        vm.auth = false;
    }
}
