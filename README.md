# Current version
Current version is **1.0.0**. Ready!

<<<<<<< HEAD
## Current version
The current version is **0.1.1**. Not recommended for production sites.
=======
# What's Crinoline

## *Structure underneath those garmets*
>>>>>>> dev

Crinoline is a PHP MVP (Model View Presenter) Framework for quick and easy webapp deployment. The main objective is to provide a well planned structure (thus, the name) for the developer. You can focus on the business logic, the models and the views, but all the routing, the session management, the database handling and query construction is already there for you to use whenever you want.

# How to use

## Crinoline layers

Crinoline is a MVP Framework. This means that there are 3 layers of functionality.

The **Model** layer represents the DataMaps and Collections that you can use to bind your data, also the ConfigDrivers to save soft configurations.

The **View** layer represents the Laces Template Engine and its plugin. The view layer is passive, so once the view is rendered, there is no interaction back to the server until a new request is made. Although you can use Backbone.js or Angular.js with Crinoline to transform the whole application into MVC.

Finally, the **Presenter** layer is represented by the Router and the Presenter classes themselves.

Everything is tied together with the use of an **App** class to handle initialisation and to be the global recipient of events.

## Config file
In your root path you will find the `config.inc.php` file.  This is where you define some paths and hardcoded configurations. This is the first place to start tweaking a new installation of Crinoline. Inside the file you will find the description of every configuration.

Please note that `$config['altDirs']` contains an array of directories to look for the different classes and it's not recursive, so you must add all your tree.

## Plugins
Plugins are set up in an array of arrays assigned to `$config['plugins']` inside `config.inc.php`. Each array contains information on how to instantiate the plugin and a small configuration.

Eg:

	$config['plugins'] = array(
		array(
            'className' => 'CRSession',
            'path'      => CRINOLINE_CORE . 'plugins/CRSession/',
            'params'    => array(
                'sessionName' => 'CrinolineExample'
            ),
        ),
	);

This would activate the CRSession core plugin taken from `CRINOLINE_CORE/plugins/CRSession` and instruct it to use  "CrinolineExample" as the session name.

Every plugin has its own parameters so check the plugin documentation for more details.

Inside the code, you can access the plugin with a shorthand code:

	plg('PluginName')->pluginMethods();

For example, you can use the CRSession core plugin like this after your authentication process to grant access to a user:

	plg('CRSession')->grantKey();
	plg('CRSession')->setData('user', 'foo');

And check his credentials with:

	if(!plg('CRSession')->hasKey()) relocate(approot());

If CRSession hasn't granted a key to the user, it would relocate the request to the app root.

## App class
The App class binds everything together and gives to the bootstrap an entry point. This is named by you and it sits in the root of the application.

    class MyApp extends App {
        ...
    }

Your App class must extend from `App` and have an `init()` public method. Here you will set everything up to handle the request. After this, the Router will take control again.

    class MyApp extends App {
        public function init() {
	        ... do some init ...
        }
    }

## Routes
Inside your `init()` method, you must declare your routes. This is done with the `addRoute($route, $presenter, $action)` method:

	$this->addRoute('ALL:/', 'HomePresenter', 'main');

Routes are special strings that have the form `METHOD:/path/%arg1%/%argN%/`

METHOD can be GET, POST, PUT, DELETE or ALL.

The path must be defined from the first slash `/` before root, so home will be just `/`.

## Events
All Crinoline objects have a common ancestor called `EventTrigger` that provides a way to listen and trigger events. This is the way of binding different objects together.

For example, the way to **handle 404 errors** is by binding a callback to the `NOTFOUND` event in the App object. This is usually done during `init()`.

	class MyApp extends App {
		...
		public function init() {
			...
			$this->bindEvent('NOTFOUND', array($this, 'hnd_404')); // Handle 404 errors
		}
		...
		public function hnd_404($evt) {
			die('Error. Not found.');
		}
	}

The three main methods of an `EventTrigger` are:

 - `bindEvent($eventName, $callback)`
 - `unbindEvent($eventName, $callback)`
 - `triggerEvent($eventName, $args)`

Every object triggers different events, so check in detail the documentation for more information about it.

## Presenters
Presenters are classes that extend `Presenter` class. They contain the business logic and are called by the router when a routing rule matches.

The *actions* are public methods inside the Presenter with a single argument (containing an Assoc Array). Actions are called with arguments gathered in various points of the request. For example, if a route has an argument like `/books/%id%`, the `id` argument will be passed; if is requested with the POST method and it has form data in the request body, it will be available in the arguments too.

Eg:

Request: GET: /books/15

This request will match `addRequest( 'GET:/books/%id%' , 'BooksPresenter' , 'getSingle');` and Router will instantiate `BooksPresenter`, then call `getSingle()` from that instance.

	class BooksPresenter extends Presenter {
		public function getSingle($args) {
			echo 'You are trying to get book id: ' . $args['id'];
		}
	}

So the request will return an array saying "You are trying to get book id: 15".

## Models
Models are the special objects that represent the data of your application. You want to extend the basic objects with your own methods, but Crinoline provides some basic functionality.

They extend from `EventTrigger`, so you can trigger and listen for events within your models.

### Data Maps
This object maps properties inside an Assoc Array to handle data more easily.

The main methods are `fromArray()` and `toArray()`.

### Data Map Collections
This is a group of Data Maps for easy iteration and manipulation. The base class name must be set for the functions to work.

You can append, create, remove, retrieve, execute functions for each element and filter.

Please check the documentation for more details.

Eg:

	// BookMap.class.php
	class BookMap extends DataMap {
		...
	}
	
	// BooksCollection.class.php
	class BooksCollection extends DataMapCollection {
		protected $baseClass = 'BookMap';
	}

	// In some action of some presenter...
	$books = new BooksCollection();
	$books->create(array(
		'title' => 'Crinoline for dummies',
		'author' => 'Alexys Hegmann'
	));

### DB bound Data Map
DBDataMap extends from DataMap and it binds the data it stores to a MySQL database. It uses the core Database class to construct the queries, so remember it is transactional by default.

For this class to work, the database information must be set:

Eg:

	// BookMap.class.php
	class BookMap extends DBDataMap {
		public function __construct($values=array()) {
			$this->primaryKey = 'idBook';
			$this->assignedTable = 'tblBooks';
			$this->sanitizeKeys = array(
				'title',
				'author'
			);
		}
	}

	// In some action of some presenter...
	$book = new BookMap();
	$db = new Database('localhost', 'user', 'pass', 'myTestDatabase');
	$db->connect();
	// Load book 15 using Database $db.
	$book->load($db, 15);
	// Print book author
	echo $book->author;
	
### DB bound Data Map Collections
It extends from DataMapCollection and it provides functions to bind a collection to a MySQL table.

The binding info must be set inside the class:

	// BooksCollection.class.php
	class BooksCollection extends DBDataMapCollection {
		public function __construct() {
			$this->baseClass = 'BookMap';
			$this->assignedTable = 'tblBooks';
		}
	}
	
	// In some action of some presenter...
	$books = new BooksCollection();
	$db = new Database('localhost', 'user', 'pass', 'myTestDatabase');
	$db->connect();
	// Fetch all books from tblBooks using $db
	$books->load($db);
	// Print title of book 5 from collection
	echo $books->at(5)->title;

### Soft configurations
When you want to add configurability to your app, you can use DataMaps and Collections to persist data, but Crinoline provides four special objects for registry-like configurations called ConfigDrivers.

Config Drivers implement IConfigDriver interface, so all four objects implements this methods:

* `get($key, $default='')` : Retrieves the data assigned to a key. If it doesn't exist, $default is returned.
* `set($key, $value)` : Assigns a value to a key. It serializes the value, so it can hold any unserializable data.
* `exists($key)` : Returns true if $key is set, false otherwise.
* `fetch()` : Reads the configuration container and creates the internal structure.
* `update()` : Updates the container with the internal data.

The four Config Drivers are: 
* `ConfigDriverHardcode` : Data is stored in an array. Ignores Fetching and Updating.
* `ConfigDriverJson` : Data is read and written to a JSON file. Fetch reads and parses the file; update writes back the file.
* `ConfigDriverMySQL` : Data has a MySQL table as a container.
* `ConfigDriverSQLite` : Data is bound to a SQLite database.

Eg:

	$conf = new ConfigDriverSQLite('myconfigs.db', 'configs');
	echo $conf->get('BooksPerPage', 10);

## View
This layer represents the Laces templates. You can use pure PHP templates here with includes, but Crinoline provides a plugin for Laces.

You can read the documentation for Laces [here](https://github.com/yagarasu/laces/).

The plugin must be set in the config file and it is available with the following construction: `plg('CRLaces')`.

The most basic function of Laces is load and render a template file.

	plg('CRLaces')->loadAndRender('templates/home.ltp');

But to be useful and render dynamic content, the context must be set:

	plg('CRLaces')->setIntoContext('$name','Fooo');
	plg('CRLaces')->loadAndRender('template.ltp');

	// template.ltp
	{{{ LacesTemplate language="en" author="Crinoline Team" }}}
	Hello, ~{{ $name }}~!

This will render "Hello, Fooo".

Please check Laces documentation for more details on how to use it.

# Version history
* 1.0.0
  * First public release.
  * Dev proccess created (no more anarchy).
  * App flow refactored.
  * Plugin manager created. This handles the interaction between app and plugins and between plugins and plugins.
  * Trivial functions split into plugins.
  * Soft Configuration Drivers created. This allows the programmer to create register like configurations easily.
* 0.1.1
  * Fixed some bugs.
  * Deleted the unnecesary echos.
* 0.1.0.alpha
  * Testing and binding everything to be easy to implement. 
  * Some performance issues to be solved. 
  * Somehow buggy. Not recomended for production sites.

# A little bit of history
Crinoline began as multiple wrappers I made for myself (Nekomata toolkit) and the core of a project with multiple user-roles (Lilium). I wanted to understand the MVP pattern so I made my research and came up with a flow that worked on almost any webapp. My team at work and I used it later when we built a CRM and a job exchange system from scratch. This period was very important for what would become Crinoline because we could test the database wrapper, the session wrapper and the main flow.
The contribution of [RZEROSTERN](https://github.com/RZEROSTERN/) was vital for this project and even though we forked in separate ways Nekomata and the core of Lilium to create our own projects, a lot of the creative, analytical and design process was made as a team.

# License
GNU LGPL v3
Read LICENSE for details.
