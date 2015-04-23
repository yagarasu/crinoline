var app = angular.module('tailor', []);
app.controller('builder', function($scope) {
	$scope.className = '';
	$scope.appPath = '';
	$scope.plugins = [
		{
			name: 'CRAlerts',
			active: false,
			config: ''
		},
		{
			name: 'CRLaces',
			active: true,
			config: ''
		},
		{
			name: 'CRRoles',
			active: false,
			config: ''
		},
		{
			name: 'CRSession',
			active: true,
			config: ''
		}
	];
	$scope.getPlugins = function() {
		return filterFilter($scope.availablePlugins, {active:true})
	};

	$scope.routes = [
		{
			method: 'ALL',
			path: '/',
			presenter: 'home',
			action: 'main'
		}
	];
	$scope.newRoute = {
		method: 'ALL',
		path: '',
		presenter: '',
		action: ''
	};
	$scope.routeAdd = function() {
		var r = angular.copy($scope.newRoute);
		$scope.routes.push(r);
		$scope.newRoute.method = 'ALL';
		$scope.newRoute.path = '';
		$scope.newRoute.presenter = '';
		$scope.newRoute.action = '';
	};
	$scope.routeDelete = function(item) {
		var i = $scope.routes.indexOf(item);
		if(i===-1) return;
		$scope.routes.splice(i, 1);
	};
	$scope.routeMoveUp = function(item) {
		var i = $scope.routes.indexOf(item);
		if(i<=0) return;
		var val = $scope.routes[i];
		var newPos = i-1;
		$scope.routes.splice(i,1);
		$scope.routes.splice(newPos,0,val);
	};
	$scope.routeMoveDown = function(item) {
		var i = $scope.routes.indexOf(item);
		if(i===-1) return;
		if(i>=$scope.routes.length) return;
		var val = $scope.routes[i];
		var newPos = i+1;
		$scope.routes.splice(i,1);
		$scope.routes.splice(newPos,0,val);
	};

	$scope.newDb = {};
	$scope.databases = [
		{
			id: 'mainDB',
			host: 'localhost',
			user: 'root',
			pass: 'root',
			name: 'crinoline'
		}
	];
	$scope.databaseAdd = function() {
		var db = angular.copy($scope.newDb);
		$scope.databases.push(db);
		$scope.newDb = {};
	}
	$scope.databaseDelete = function(item) {
		var i = $scope.databases.indexOf(item);
		if(i===-1) return;
		$scope.databases.splice(i, 1);
	};
	$scope.databaseEdit = function(item) {
		var i = $scope.databases.indexOf(item);
		$scope.newDb = $scope.databases[i];
		$scope.databases.splice(i, 1);
	};

	$scope.models = [
		{
			name: 'ContactMap',
			type: 'DBDataMap',
			settings : {
				database: 'mainDb',
				primaryKey: 'id',
				assignedTable: 'contacts',
				sanitizeKeys: 'name, email'
			}
		},
		{
			name: 'ContactCollection',
			type: 'DBDataMapCollection',
			settings: {
				database: 'mainDb',
				baseClass: 'ContactMap',
				assignedTable: 'contacts'
			}
		},
		{
			name: 'EmptyModel',
			type: 'DataMap',
			settings: {}
		}
	];
	$scope.newModel = {
		DataMap: {name:'',type:'',settings:{}},
		DataMapCollection: {name:'',type:'',settings:{}},
		DBDataMap: {name:'',type:'',settings:{}},
		DBDataMapCollection: {name:'',type:'',settings:{}}
	}
	$scope.modelHasSettings = function(model) {
		return !angular.equals({}, model.settings);
	};
	$scope.modelAdd = function(type) {
		var t = type || 'DataMap';
		var r = angular.copy($scope.newModel[type]);
		r.type = t;
		$scope.models.push(r);
		$scope.newModel[type] = {name:'',type:'',settings:{}}
	};
	$scope.modelDelete = function(item) {
		for(var i = 0; i < $scope.models.length; i++) {
			if($scope.models[i].name===item) {
				$scope.models.splice(i, 1);
				break;
			}
		}
	};

	$scope.configs = [
	];
	$scope.newConfig = {
		Hardcode: {},
		Json: {},
		MySQL: {},
		SQLite: {}
	}
	
	$scope.generateCode = function() {
		console.log("generate!");
	};
});
