var myApp = angular.module('myApp', [
	'ngRoute', //telling angular that we r using routing
	'artistControllers'  //module that we are routing.
]);


//configure how to run partials (using routeprovider service)
myApp.config(['$routeProvider', function($routeProvider){
	$routeProvider.
	when('/login', { //when list called, call this url:
		templateUrl: 'partials/login.html',
		controller: 'FormController' //name of its controller
	}).
	when('/list', { //when list called, call this url:
		templateUrl: 'partials/list.html',
		controller: 'ListController' //name of its controller
	}).
	when('/details/:itemId',{
		templateUrl: 'partials/details.html',
		controller: 'DetailsController'
	}).
	otherwise({ //default route, when accessing homepage
		redirectTo: '/login'
	});
}])