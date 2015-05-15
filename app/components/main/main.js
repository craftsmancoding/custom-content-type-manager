'use strict';

angular.module('cctmApp.main', ['ngRoute'])

    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/main', {
            templateUrl: cctm.url + "/app/components/main/main.html",
            controller: 'MainController'
        });
    }])

    .controller('MainController', [function() {

    }]);