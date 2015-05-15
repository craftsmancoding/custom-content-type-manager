'use strict';
//alert(cctm.url + "/app/components/settings/settings.html");
angular.module('cctmApp.settings', ['ngRoute'])

    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/settings', {
            //templateUrl: cctm.url + "/app/components/settings/settings.html",
            //templateUrl: ajaxurl + "?action=cctm&_resource=Page&_id=partials/settings",
            templateUrl: ajaxurl + "?action=cctm2",
            controller: 'SettingsController'
        });
    }])

    .controller('SettingsController', [function() {

    }]);