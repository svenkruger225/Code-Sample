var artistControllers = angular.module('artistControllers', ['ngSanitize']); //dependancy inside []...

artistControllers.controller('ListController', ['$scope', '$http', function($scope, $http) {
  $http.get('js/data.json').success(function(data) {
    $scope.artists = data;
    $scope.artistOrder = 'name';
  });
}]);

artistControllers.controller('DetailsController', [
	'$scope','$http','$routeParams',
	function($scope,$http,$routeParams){
		$http.get('js/data.json').success(function(data){
			$scope.artists = data;
			$scope.whichItem = $routeParams.itemId;

		if($routeParams.itemId > 0){
			$scope.prevItem = Number($routeParams.itemId)-1;
		}
		else{
			$scope.prevItem = $scope.artists.length-1;	
		}

		if($routeParams.itemId < $scope.artists.length-1){
			$scope.nextItem = Number($routeParams.itemId)+1;
		}
		else{
			$scope.nextItem = 0;
		}
	});

}]);

artistControllers.controller('FormController',['$scope', '$http', '$location', function ($scope, $http, $location) {
/*
* This method will be called on click event of button.
* Here we will read the email and password value and call our PHP file.
*/

	var changeLocation = function(url, force) {
  //this will mark the URL change
	  $location.path(url); //use $location.path(url).replace() if you want to replace the location instead

	  $scope = $scope || angular.element(document).scope();
	  if(force || !$scope.$$phase) {
	    //this will kickstart angular if to notice the change
	    $scope.$apply();
	  }
	};


	$scope.autho = function autho() {
		$http.post('http://demo.shayansolutions.com/Service/auth.php', {'uname': $scope.username, 'pswd': $scope.password}
        ).success(function(data, status, headers, config) {
            if (data.msg != '')
            {
                changeLocation('/list',true);
                $scope.message = '<span style="color:green">' + data.error + '</span>';
            }
            else
            {
                $scope.message = '<span style="color:red">' + data.error + '</span>';
            }
        }).error(function(data, status) { // called asynchronously if an error occurs
			// or server returns response with an error status.
            alert('failure, ' + data + ', ' + status);
        });
	}
}]);
