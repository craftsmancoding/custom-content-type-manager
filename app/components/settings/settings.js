'use strict';
//alert(cctm.url + "/app/components/settings/settings.html");
angular.module('cctmApp.settings', ['ngRoute'])

    .config(['$routeProvider', function($routeProvider) {
        $routeProvider.when('/settings', {
            //templateUrl: cctm.url + "/app/components/settings/settings.html",
            templateUrl: ajaxurl + "?action=cctm",

            controller: 'SettingsController'
        });
    }])

    .controller('SettingsController', [function() {

    }]);