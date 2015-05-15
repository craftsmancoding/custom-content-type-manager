'use strict';

angular.module('cctmApp.main', ['ngRoute'])

    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/main', {
            //templateUrl: cctm.url + "/app/components/main/main.html",
            templateUrl: ajaxurl + "?action=cctm&_resource=Page&_id=partials/main",
            controller: 'MainController'
        });
    }])

    .controller('MainController', [function() {

    }]);