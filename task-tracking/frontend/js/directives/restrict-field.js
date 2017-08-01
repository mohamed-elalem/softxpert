app.directive('restrictField', function() {
    return {
        restrict: 'AE',
        scope: {
            restrictField: '='
        },
        link: function(scope) {
            // this will match spaces, tabs, line feeds etc
            // you can change this regex as you want
            var regex = /\s/g;

            scope.$watch('restrictField', function(newValue, oldValue) {
                if (newValue != oldValue && regex.test(newValue)) {
                    scope.restrictField = newValue.replace(regex, '');
                }
            });
        }
    };
});