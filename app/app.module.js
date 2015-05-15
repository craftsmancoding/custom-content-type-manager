angular.module('cctmApp',['ngRoute'])
    .run(['$route', function ($route) {
        $route.reload();
    }])
    .factory('dataFactory', ['$http', function($http) {

        var dataFactory = {};

        dataFactory.getData = function () {
            return $http.get('api/read.php');
        };

        dataFactory.updateData = function (d) {
            return $http.post('/angular/api/write.php', d);
        };

        return dataFactory;
    }])
    .controller('MainController', ['$scope', 'dataFactory',
        function ($scope, dataFactory) {

            $scope.status;
            $scope.data;
            $scope.genders = [
                {"id":"m","label":"Male"},
                {"id":"f","label":"Female"}
            ];
            //$scope.gender = $scope.genders[0]; // "m"; // default
            //getData();
            //
            //function getData() {
            //    dataFactory.getData()
            //        .success(function (data) {
            //            $scope.data = data;
            //        })
            //        .error(function (error) {
            //            $scope.status = 'Unable to load data: ' + error.message;
            //        });
            //}
            //
            //$scope.updateData = function () {
            //    console.log('Updating data',$scope.data);
            //    dataFactory.updateData($scope.data)
            //        .success(function () {
            //            $scope.status = 'Updated Data! Refreshing data list.';
            //        })
            //        .error(function (error) {
            //            $scope.status = 'Unable to update data: ' + error.message;
            //        });
            //};
        }])
    .controller('SettingsController', ['$scope', 'dataFactory',
        function ($scope, dataFactory) {

        }]);