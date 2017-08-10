(function() {
    angular.module("app").controller("UserController", userController);
})()

function userController($scope, $rootScope, $location, UserFactory, $routeParams, $route, $uibModal) {
    var vim = this;
    vim.scope = $scope;
    vim.rootScope = $rootScope;
    vim.location = $location;
    vim.userFactory = UserFactory;
    vim.routeParams = $routeParams;
    vim.route = $route;
    vim.uibModal = $uibModal;

    vim.scope.setUserToDelete = setUserToDelete;
    vim.scope.unsetUserToDelete = unsetUserToDelete;
    vim.scope.deleteUser = deleteUser;
    vim.scope.page = 1;
    vim.scope.total = 10;
    vim.scope.itemsPerPage = 10;
    vim.scope.getAllUsers = getAllUsers;
    vim.scope.data = vim.rootScope.data || {};
    vim.rootScope.data = {};

    var userToDelete = -1;


    var token = localStorage.getItem("token");
    if (angular.equals($routeParams, {})) {
        getAllUsers(vim.scope.page);
    } else {
        var id = $routeParams.id;
        vim.userFactory.getSingleUser(token, id).then(getSingleUserSuccess, getSingleUserError).catch(getSingleUserException);
    }

    vim.rootScope.$on("filtering", function(event, data) {
        vim.scope.data = data;
        vim.userFactory.getUsers(token, data).then(getUsersSuccess, getUsersError).catch(getUsersException);
    });

    /**
     * Getting all users from database
     * To be paginated later
     */

    function getAllUsers(page) {
        vim.scope.page = page;
        vim.userFactory.getUsers(token, vim.scope.data, page).then(getUsersSuccess, getUsersError).catch(getUsersException, vim.rootScope.searchFilters || {});
    }



    /**
     * Handling events
     */

    function setUserToDelete(id) {
        userToDelete = id;

        var modalInstance = vim.uibModal.open({
            ariaLabelledBy: 'modal-title',
            ariaDescribedBy: 'modal-body',
            templateUrl: 'myModalContent.html',
            controller: function($uibModalInstance, $scope, UserFactory) {
                $scope.unsetUserToDelete = function() {
                    $uibModalInstance.dismiss('cancel');
                }

                $scope.deleteUser = function() {
                    vim.userFactory.deleteUser(token, userToDelete).then(deleteUserSuccess, deleteUserError).catch(deleteUserException);
                    $uibModalInstance.dismiss('cancel');
                }
            },
            size: 'sm',
        });

        modalInstance.result.then(function() {}, function() {});
    }

    function unsetUserToDelete() {
        userToDelete = -1;
        console.log("There");
        vim.modalInstance.dismiss('cancelDeletion');
    }

    function deleteUser() {
        vim.userFactory.deleteUser(token, userToDelete).then(deleteUserSuccess, deleteUserError).catch(deleteUserException);
    }


    /**
     * End of event handling
     */

    /**
     * Handling success
     */

    function getSingleUserSuccess(res) {
        if (res.status == 200) {
            var user = res.data.data[0];
            delete user.id;
            delete user.created_at;
            delete user.updated_at;
            vim.scope.user = res.data.data[0];
        }
    }

    function getUsersSuccess(res) {
        if (res.status == 200) {
            vim.scope.users = res.data.data.users;
            vim.scope.itemsPerPage = res.data.data.itemsPerPage;
            vim.scope.total = res.data.data.total;
        }
    }

    function deleteUserSuccess(res) {
        if (res.status == 200) {
            vim.route.reload();
        }
    }

    /**
     * End of success handling
     */

    /**
     * Handling Errors
     */

    function getSingleUserError(err) {
        console.log(err);
    }

    function getUsersError(err) {
        console.log(err);
    }

    function deleteUserError(err) {
        console.log(err);
    }

    /**
     * End of error handling
     */

    /**
     * Handling exceptions
     */




    function getSingleUserException(exp) {
        console.log(exp);
    }

    function getUsersException(exp) {
        console.log(exp);
    }

    function deleteUserException(exp) {
        console.log(exp);
    }


    /**
     * End of exception handling
     */
}