var app = angular.module('tailor', []);
app.controller('builder', ['$scope','$http', function($scope, $http) {
	$scope.alerts = [];
	$scope.modLock = false;
	$scope.alertWarnings = 0;
	$scope.consoleAdd = function(msg, type) {
		$scope.alerts.push({'message': msg, 'type': type});
	};

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
		var plgs = [];
		for(var i=0; i<$scope.plugins.length; i++) {
			if($scope.plugins[i].active) {
				plgs.push($scope.plugins[i]);
			}
		}
		return plgs;
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
		if(!/^(\/(\w+|%\w+%))*\/?$/.test(r.path)) {
			alert('Your path is not valid. Please check before adding it.');
			return false;
		}
		if(!/^[a-z]\w*$/i.test(r.presenter)) {
			alert('Your presenter is not valid. Please check before adding it.');
			return false;
		}
		if(!/^[a-z_]\w*$/i.test(r.action)) {
			alert('Your action is not valid. Please check before adding it.');
			return false;
		}
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
	$scope.databaseIdAvailable = function(id) {
		for(var i = 0; i < $scope.databases.length; i++) {
			if($scope.databases[i].id===id) {
				return false;
			}
		}
		return true;
	};
	$scope.databaseAdd = function() {
		while(!$scope.databaseIdAvailable($scope.newDb.id)) {
			var ver = /\d+$/.exec($scope.newDb.id);
			var nver = parseInt(ver) + 1;
			var root = (ver == null) ? $scope.newDb.id : $scope.newDb.id.substring(0,($scope.newDb.id.length-ver.length));
			$scope.newDb.id = (ver != null) ? root + nver : $scope.newDb.id + '1';
		}
		var db = angular.copy($scope.newDb);
		$scope.databases.push(db);
		$scope.newDb = {};
	};
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
	$scope.modelNameIsAvailable = function(name) {
		for(var i = 0; i < $scope.models.length; i++) {
			if($scope.models[i].name===name) {
				return false;
			}
		}
		return true;
	};
	$scope.modelAdd = function(type) {
		var t = type || 'DataMap';
		while(!$scope.modelNameIsAvailable($scope.newModel[t].name)) {
			var ver = /\d+$/.exec($scope.newModel[t].name);
			var nver = parseInt(ver) + 1;
			var root = (ver == null) ? $scope.newModel[t].name : $scope.newModel[t].name.substring(0,($scope.newModel[t].name.length-ver.length));
			$scope.newModel[t].name = (ver != null) ? root + nver : $scope.newModel[t].name + '1';
		}
		var r = angular.copy($scope.newModel[t]);
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
		{
			name: 'basicConfig',
			type: 'MySQL',
			settings: {
				assignedTable: 'contacts',
				database: 'someDb'
			}
		}
	];
	$scope.newConfig = {
		Hardcode: {name:'',type:'',settings:{}},
		Json: {name:'',type:'',settings:{}},
		MySQL: {name:'',type:'',settings:{}},
		SQLite: {name:'',type:'',settings:{}}
	}
	$scope.configNameIsAvailable = function(name) {
		for(var i = 0; i < $scope.configs.length; i++) {
			if($scope.configs[i].name===name) {
				return false;
			}
		}
		return true;
	}
	$scope.configAdd = function(type) {
		var t = type || 'Hardcode';
		while(!$scope.configNameIsAvailable($scope.newConfig[t].name)) {
			var ver = /\d+$/.exec($scope.newConfig[t].name);
			var nver = parseInt(ver) + 1;
			var root = (ver == null) ? $scope.newConfig[t].name : $scope.newConfig[t].name.substring(0,($scope.newConfig[t].name.length-ver.length));
			$scope.newConfig[t].name = (ver != null) ? root + nver : $scope.newConfig[t].name + '1';
		}
		var r = angular.copy($scope.newConfig[type]);
		r.type = t;
		$scope.configs.push(r);
		$scope.newConfig[type] = {name:'',type:'',settings:{}}
	};
	$scope.configDelete = function(item) {
		for(var i = 0; i < $scope.configs.length; i++) {
			if($scope.configs[i].name===item) {
				$scope.configs.splice(i, 1);
				break;
			}
		}
	};
	
	$scope.generateCode = function() {
		$scope.alerts = [];
		$scope.alertWarnings = 0;
		$scope.modLock = true;
		$scope.consoleAdd('Validating data...','info');
		if(!$scope.validate()) {
			$scope.consoleAdd('Validation failed. Please check this log for more details.','danger');
			$scope.modLock = false;
			return;
		}
		$scope.consoleAdd('Validation passed with '+$scope.alertWarnings+' warnings.','success');
		$scope.consoleAdd('Sending data to produce the .zip...','info');
		$http({
			method: 'post',
			url: 'builder/',
			data: $.param({
				'appData' : {
					className: $scope.className,
					appPath: $scope.appPath,
					plugins: $scope.getPlugins(),
					routes: $scope.routes,
					databases: $scope.databases,
					models: $scope.models,
					configs: $scope.configs
				}
			}),
			headers: {'Content-Type': 'application/x-www-form-urlencoded'},
		}).success(function(data) {
			console.log(data);
		});
		$scope.modLock = false;
	};

	$scope.validate = function() {
		if(!/^[A-Z]+[a-zA-Z0-9_]*$/.test($scope.className)) {
			// Strict doesn't match. Try to fix
			var cnfl = $scope.className.substr(0,1);
			if(!/[a-z]/.test(cnfl)) {
				$scope.consoleAdd('Class name "'+$scope.className+'" is not valid.','danger');
				return false;
			} else {
				var cn = cnfl.toUpperCase() + $scope.className.substr(1);
				$scope.className = cn;
				$scope.consoleAdd('Class name corrected to "'+$scope.className+'".','warning');
				$scope.alertWarnings++;
			}
		}
		// Class name valid
		if(!/^https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)$/.test($scope.appPath)) {
			// Invalid app root
			if(!/^https?:\/\/localhost(\:\d+)?(\/\w+)*$/.test($scope.appPath)) {
				$scope.consoleAdd('App root path "'+$scope.appPath+'" is not valid.','danger');
				return false;
			} else {
				$scope.consoleAdd('App root path "'+$scope.appPath+'" is at localhost. Please take that into account when deploying.','warning');
				$scope.alertWarnings++;
			}
		}
		// App path valid
		var plugs = $scope.getPlugins();
		for(var i=0; i<plugs.length; i++) {
			console.log(plugs[i].config);
			if(plugs[i].config!=''&&!/^array\(/.test(plugs[i].config)) {
				$scope.consoleAdd('Configs for plugin "'+plugs[i].name+'" does not start with "array(".','danger');
				return false;
			}
			if(plugs[i].config!=''&&!/\);$/.test(plugs[i].config)) {
				$scope.consoleAdd('Configs for plugin "'+plugs[i].name+'" does not end with ");".','danger');
				return false;
			}
		}
		return true;
	};
}]);
