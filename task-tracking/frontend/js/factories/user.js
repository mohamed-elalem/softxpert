app.factory("User", function($http, $q) {
    // var base = "http://localhost:8000/";
    // var prefix = "api/";
    // var loginUrl = "login_check";
    // var registerationURl = "register";
    // var getUserFullNameUrl = "get_user_info";

    return {
        login: function(username, password, role) {
            console.log("Login attempt");
            console.log("username", username);
            console.log("password", password);
            role = role || '';

            return $http({
                method: "post",
                url: BASE + PREFIX + role + LOGIN_URL,
                transformRequest: function(obj) {
                    var str = [];
                    for (var p in obj)
                        str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
                    return str.join("&");
                },
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                dataType: 'json',
                data: { "_username": username, "_password": password }
            });
        },
        register: function(username, name, email, password) {
            console.log("Registration attempt");
            console.log("username", username);
            console.log("name", name);
            console.log("email", email);
            console.log("password", password);


            return $http({
                method: "post",
                url: BASE + PREFIX + REGISTRATION_URl,
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
                    "username": username,
                    "name": name,
                    "email": email,
                    "password": password
                }
            });
        },
        getUserInfo: function() {
            return $http({
                "method": "post",
                "url": BASE + PREFIX + GET_USER_FULL_NAME_URL,
                "headers": {
                    "Authorization": "Bearer " + localStorage.getItem("token")
                },
                "dataType": "json"
            });
        }
    }
})