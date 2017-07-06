app.factory("User", function($http, $q) {
    var loginUrl = "http://localhost:8000/api/login";
    var registerationURl = "http://localhost:8000/api/register";

    return {
        login: function(email, password) {
            console.log("Login attempt");
            console.log("email", email);
            console.log("password", password);

            var def = $q.defer();

            $http({
                method: "post",
                url: loginUrl,
                transformRequest: function(obj) {
                    var str = [];
                    for (var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                },
                data: { "email": email, "password": password }
            }).then(function(res) {
                console.log(res);
                def.resolve();
            }).then(function(err) {
                console.log(err);
                def.reject();
            });
            return def.promise;
        },
        register: function(name, email, password) {
            console.log("Registration attempt");
            console.log("name", name);
            console.log("email", email);
            console.log("password", password);

            var def = $q.defer();

            $http({
                method: "post",
                url: registerationURl,
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                transformRequest: function(obj) {
                    var str = [];
                    for (var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                },
                data: {
                    "name": name,
                    "email": email,
                    "password": password
                }
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