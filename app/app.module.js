'use strict';

// Declare app level module which depends on views, and components
angular.module('cctmApp', [
    'ngRoute',
    'cctmApp.main',
    'cctmApp.settings'
]).
config(['$routeProvider', function($routeProvider) {
    $routeProvider
    //.when("/main", {
    //    //templateUrl: cctm.url . "/components/main/main.html",
    //    templateUrl: "/wp-content/plugins/custom-content-type-manager/app/components/main/main.html",
    //    controller: "MainController"
    //})
    //.when("/settings", {
    //    //templateUrl: cctm.url . "/components/settings/settings.html",
    //    templateUrl: "/wp-content/plugins/custom-content-type-manager/app/components/settings/settings.html",
    //    controller: "SettingsController"
    //})
    .otherwise({redirectTo: '/main'});
}]);
