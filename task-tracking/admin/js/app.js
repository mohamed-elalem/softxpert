(function() {
    angular.module("app", ["ngAnimate", "ngRoute", "ui.bootstrap", ngFx, "720kb.fx", "bw.paging"]).run(kickStarter);
})();

function kickStarter($rootScope, $location, UserFactory, $route, $window) {
    var vim = this;
    vim.rootScope = $rootScope;
    vim.rootScope.logout = logout;
    vim.rootScope.submitSearch = submitSearch;

    vim.rootScope.getFilteredResults = getFilteredResults;
    vim.rootScope.filter = {};
    vim.rootScope.role = {};
    vim.rootScope.search = {};

    var token = localStorage.getItem("token");
    UserFactory.getUserInfo(token).then(userInfoSuccess, userInfoError).catch(userInfoException);

    function userInfoSuccess(res) {
        vim.rootScope.name = res.data.data[0].name;
        vim.rootScope.auth = true;
    }

    function userInfoError(err) {
        var refreshToken = localStorage.getItem("refreshToken");
        console.log(refreshToken);
        if (refreshToken != undefined) {
            UserFactory.refreshLogin(refreshToken).then(refreshLoginSuccess, refreshLoginError).catch(refreshLoginException);
        }


        function refreshLoginSuccess(res) {
            console.log(res);
            if (res.status == 200) {
                localStorage.setItem("token", res.data.token);
                $window.location.reload();
            }
        }

        function refreshLoginError(err) {
            vim.rootScope.auth = false;
            console.log(err);
        }

        function refreshLoginException(exp) {
            console.log(exp);
        }

    }

    function userInfoException(exp) {
        console.log(exp);
        vim.rootScope.auth = false;
    }


    function logout() {
        var token = localStorage.getItem("token");
        UserFactory.logout(token).then(logoutSuccess, logoutError).catch(logoutException);
    }

    function logoutSuccess(res) {
        console.log(res);
        vim.rootScope.auth = false;
        localStorage.removeItem("token");
        localStorage.removeItem("refreshToken");
        $location.url("/login");
    }

    function logoutError(err) {
        console.log(err);
    }

    function logoutException(exp) {
        console.log(exp);
    }

    function submitSearch() {
        var data = {};
        if (vim.rootScope.search.hasOwnProperty("username")) {
            data = {
                "_username": true,
                "username": vim.rootScope.search.username
            }
            vim.rootScope.data = data;
            $location.url("/users");
            // vim.rootScope.$emit("filtering", data);
        }
    }

    function getFilteredResults() {
        var filters = vim.rootScope.filter;
        var data = {};
        var mark = {};
        for (var filter in filters) {
            var between = false;
            var state = null;

            data[filter] = filters[filter];

            if (filter.length > 3 && (filter.substr(filter.length - 4) == "_min" || filter.substr(filter.length - 4) == "_max")) {
                between = true;
                state = filter.substr(filter.length - 4);
                filter = filter.substr(0, filter.length - 4);
            }

            if (between) {
                if (mark[filter] === undefined) {
                    mark[filter] = 1;
                } else {
                    mark[filter]++;
                }
                data["__" + filter] = true;
            } else {
                if (filters[filter] == undefined || filters[filter].length == 0) {
                    delete filters[filter];
                } else {
                    data["_" + filter] = true;
                }
            }
        }
        for (var filter in mark) {
            if (mark[filter] == 1) {
                delete data["__" + filter];
                delete data[filter + "_min"];
                delete data[filter + "_max"];
            }
        }
        var role = vim.rootScope.role;
        if (role.admin || role.supervisor || role.trainee) {
            data["_role"] = true;
            data["role"] = role.admin | (role.supervisor << 1) | (role.trainee << 2);
        }
        // vim.rootScope.$emit("filtering", data);
        vim.rootScope.data = data;
        $location.url("/users");
    }
}