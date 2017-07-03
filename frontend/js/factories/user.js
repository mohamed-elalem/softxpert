app.factory("User", function() {
    return {
        login: function(email, password) {
            console.log("Login attempt");
            console.log("email", email);
            console.log("password", password);
        }
    }
})