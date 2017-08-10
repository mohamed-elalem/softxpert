(function() {
    angular.module("app").factory("ChallengeFactory", challengeFactory);
})()

function challengeFactory($http) {
    return {
        getUnassignedChallenges: getUnassignedChallenges,
        assignChallenge: assignChallenge,
        getMyChallenges: getMyChallenges,
        newChallenge: newChallenge,
        getChallengeChildren: getChallengeChildren,
        connect: connect,
        deleteChallenge: deleteChallenge
    }


    function getUnassignedChallenges(token, user_id, page) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/trainees/" + user_id + "/challenges/" + page,
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function assignChallenge(token, userId, challengeId) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/trainees/" + userId + "/challenges",
            method: "post",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/x-www-form-urlencoded"
            },
            transformRequest: toFormData,
            data: {
                "challenge_id": challengeId,
            }
        });
    }

    function getMyChallenges(token, page) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/challenges/" + page,
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function newChallenge(token, title, description, duration) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/challenges",
            "method": "post",
            "headers": {
                "Content-Type": "application/x-www-form-urlencoded",
                "Authorization": "Bearer " + token
            },
            "transformRequest": toFormData,
            "data": {
                "title": title,
                "description": description,
                "duration": duration
            }
        })
    }

    function getChallengeChildren(token, challengeId, page) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/challenges/" + challengeId + "/dependents/" + page,
            "headers": {
                "Authorization": "Bearer " + token
            }
        });
    }

    function connect(token, parent_id, child_id) {
        return $http({
            "url": "http://localhost:8000/api/supervisor/challenges",
            "method": "patch",
            "headers": {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/x-www-form-urlencoded"
            },
            "transformRequest": toFormData,
            "data": {
                "parent_id": parent_id,
                "child_id": child_id,
            }
        });
    }

    function deleteChallenge(token, challenge_id) {
        console.log("Called");
        return $http({
            "url": "http://localhost:8000/api/supervisor/challenges",
            "method": "delete",
            headers: {
                "Authorization": "Bearer " + token,
                "Content-Type": "application/x-www-form-urlencoded"
            },
            "transformRequest": toFormData,
            "data": {
                "challenge_id": challenge_id
            }
        })
    }


    /**
     * Helpers
     */

    function toFormData(obj) {
        var str = [];
        for (var p in obj)
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        return str.join("&");
    }
}