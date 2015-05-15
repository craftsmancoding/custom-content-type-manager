'use strict';

// Declare app level module which depends on views, and components
angular.module('cctmApp', [
    'ngRoute',
    'cctmApp.main',
    'cctmApp.settings'
]).
config(['$routeProvider', function($routeProvider) {
    $routeProvider
    .otherwise({redirectTo: '/main'});
}]);
