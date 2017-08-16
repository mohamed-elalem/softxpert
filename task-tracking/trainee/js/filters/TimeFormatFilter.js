(function() {
    angular.module("app").filter("timeformat", dateFormat);
})()

function dateFormat() {
    return handle;

    function handle(hours) {
        seconds = [3600 * 168, 3600 * 24, 3600, 60, 1];
        var suffixes = ["week", "day", "hour", "minute", "seconds"];
        var time = [0, 0, 0, 0, 0];
        var output = "";
        for (var i = 0; i < 5; i++) {
            time[i] = Math.floor(hours / seconds[i]);
            hours %= seconds[i];
            if (time[i] > 0) {
                if (output.length > 0)
                    output += " ";
                output += time[i] + " " + suffixes[i];
            }
        }

        if (output.length == 0) {
            output = "Not yet started";
        }

        return output;

    }
}