app.factory("User", function($http, $q) {
    var loginUrl = "http://localhost:8000/api/login";

    return {
        login: function(email, password) {
            console.log("Login attempt");
            console.log("email", email);
            console.log("password", password);

            var def = $q.defer();

            $http({
                method: "post",
                url: loginUrl,
                data: { "email": email, "password": password }
            }).then(function(res) {
                console.log(res);
                def.resolve();
            }).then(function(err) {
                console.log(err);
                def.reject();
            });
            return def.promise;
        }
    }
})