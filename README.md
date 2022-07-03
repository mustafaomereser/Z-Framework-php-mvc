### 0.1. Z Framework (V1.2.1)
### 0.2. Easiest, fastest PHP framework. (Simple)

### 0.3. Document
- [1. Route](#1-route)
  - [1.1. Form examples](#11-form-examples)
  - [1.2. Route Options](#12-route-options)
  - [1.3. Find Route's Url](#13-find-routes-url)
- [2. Model](#2-model)
  - [2.1. User](#21-user)
  - [2.2. Database Migrate](#22-database-migrate)
- [3. Date](#3-date)
- [4. Mail](#4-mail)
- [5. Controller](#5-controller)
- [6. View](#6-view)
- [7. zhelper](#7-zhelper)
- [8. Csrf](#8-csrf)
- [9. Language](#9-language)
- [10. Crypter](#10-crypter)
- [11. Config](#11-config)
- [12. Alerts](#12-alerts)
- [13. Validator](#13-validator)
- [14. Middleware](#14-middleware)
- [15. API](#15-api)
- [16. Development](#16-development)
- [17. Helper Methods](#17-helper-methods)
- [18. Run Project](#18-run-project)

## 1. Route
```php
    // Any METHOD Route
   Route::any('/', function() {
        return 'Method: ' . method();
   });
    
    // Get METHOD Route
   Route::get('/', function() {
        return 'Hi 👋';
   });
   
    // POST METHOD Route
   Route::post('/', function() {
        return 'You verified CSRF Token.';
   });
   
   // PATCH METHOD Route
   Route::patch('/', function() {
        return 'patch.';
   });
   
    // PUT METHOD Route
   Route::put('/', function() {
        return 'put.';
   });
   
   // DELETE METHOD Route
   Route::delete('/', function() {
        return 'delete.';
   });
   
   // if you create resource controller it's like that simple
   Route::resource('/', TestController::class, ['name' => 'home']);
   
   
    Resource Route list:
   
    |------------------------------------------------------------|
    | URL        | METHOD    | Callback Function | Route Name    |
    |------------|-----------|-----------------------------------|
    | /          | GET       | index()           | home.index    |
    | /          | POST      | store()           | home.store    |
    | /{id}      | GET       | show($id)         | home.show     |
    | /{id}/edit | GET       | edit($id)         | home.edit     |
    | /create    | GET       | create()          | home.create   |
    | /{id}      | PUT/PATCH | update($id)       | home.update   |
    | /{id}      | DELETE    | delete($id)       | home.delete   |
    |--------------------------------------------|---------------|


    # if you wanna simple use route names for resource
    Route::resource('/test', ResourceController::class);
    # result:
    test.index
    test.store
    test.show
    test.edit
    test.create
    test.update
    test.delete

    // two example for select name.
    Route::name('test.index'); // output: www.host.com/test
    Route::name('test.edit', ['id' => 1]); // output: www.host.com/test/1/edit

    // for preURL usage:
    Route::$preURL = '/admin';
    Route::resource('/', ResourceController::class);
    Route::name('admin.index'); // output www.host.com/admin

```
### 1.1. Form examples

```html
    You must use csrf token for POST methods. (if you not add "no-csrf" option.)


    <!-- for store() method -->
    <form method="POST">
        <?= Csrf::csrf() ?>
        <input type="submit">
    </form>

    <!-- for update() method -->
    <form action="/1" method="POST">
        <?= Csrf::csrf() ?>
        <?= inputMethod('PATCH') ?>
        <input type="submit">
    </form>

    <!-- for delete() method -->
    <form action="/1" method="POST">
        <?= Csrf::csrf() ?>
        <?= inputMethod('DELETE') ?>
        <input type="submit">
    </form>

    Also you can use `csrf()` method
    <form method="POST">
        <?= csrf() ?>
        ...
    </form>
```


Callback function can be a Controller class example:
```php
    // App\Controllers\TestController.php
    class ...{
        public function index() {
            return 'Hi 👋';
        }
    }
    // Route/web.php
    Route::get('/', [TestController::class, 'index']);
```
How i use parameters? (it's same for Controller's functions)
```php
    Route::get('/{id}', function($id) {
        return "ID: $id";
    })
```
ALSO you can normal query like /1?test=true

### 1.2. Route Options
```php                                                  
                                                        // Last array is Options
    Route::post('/store', [TestController::class, 'store'], [
        'name' => 'store',
        'no-csrf' => true,
        'middlewares' => [Auth::class]
    ]);

    // Other way for middleware (if you use that way you can not find route name.)
    Middleware::middleware([Auth::class, Guest::class], function ($declined) {
        if (count($declined)) return;
        
        Route::get('/test', function () {
            return "Hey 👋";
        }, [
            'name' => 'test' // if middleware not verify you can not find that name.
        ]);
    });

```
### 1.3. Find Route's Url
```php
    // Route/web.php
    Route::get('/test/{id}/{username}', function ($id, $username) {
        echo "$id - $username";
    }, [
        'name' => 'test'
    ]);

    // Usage:
    echo Route::name('test', ['id' => 1, 'username' => 'Admin']); // output: /test/1/Admin
```

## 2. Model
```php
    class User extends Model {
        use softDelete; // (optional) if you are need soft delete a table's row use this. that mean delete you can not seen but not delete in db.

        public $table = "users";
        public $db = "local"; // (optional) if you do not write that it's connect your first connection.
    
        public $primary = "name" // (optional) select table primary key it's default = id
        public $updated_at = "custom_updated_at_name" // (optional) if you use updated_at attribute it's default = updated_at
        public $created_at = "custom_created_at_name" // (optional) if you use created_at attribute it's default = created_at
        public $deleted_at = "custom_deleted_at_name" // (optional) if you use deleted_at attribute it's default = deleted_at
    }
    
    // if you wanna see your deleted_at items
    $users = new DB;
    $users = $users->table('users')->get(); // return with deleted_at items.

    // Usage:
    
    use App\Models\User;
    $user = new User;
    echo "<pre>";
    print_r([
        "get" => $user->get(),
        "first" => $user->where('id', '=', 1)->first(),
        "firstOrFail" => $user->where('id', '=', 1)->firstOrFail(), // If can not find a row abort 404
        "count" => $user->count(),
        "insert" => $user->insert([
            'username' => 'username',
            'password' => 'password',
            'email' => 'email@mail.com'
        ]),
        "update" => $user->where('id', '=', 1)->update([
            'email' => 'test@mail.com'
        ]),
        "delete" => $user->where('id', '>', 0)->delete()
    ]);

    // if you wanna get type class = ->get(true) | ->first(true);

    // Where example
    $user->where('id', '=', 1)->where('email', '=', 'test@mail.com', 'OR')->get();
    
    // Find example that is for first key my users table's first key is id
    $user->find(1, true|false);

    // Select example
    $user->select('id, username')->get();

    // OrderBy example
    $user->orderBy(['id' => 'ASC', 'username' => 'DESC'])->get();

    // GroupBy example
    $user->groupBy('username');
    
    // Limit example args: 10(startCount), 10(rowCount)
    $user->limit(5, 10)->get();

    // paginate example               for return class or array
    $user->paginate(20, 'request_id', true|false);

    // Joins example
    $user->join('LEFT|RIGHT|OUTER|FULL|NULL', 'table_name', ['table_name.id', '=', 'this_table.id'])->get();
                                                // You also set name
    $user->join('LEFT|RIGHT|OUTER|FULL|NULL', 'table_name as name', ['name.id', '=', 'this_table.id'])->get();


    // retrn class output
    $...->get(true);
    $...->first(true);
    $...->paginate(..., ..., true);
```

### 2.1. User
```php
    Auth::login($user) // login with $user->first()

    Auth::api_login($token) // login with api_token

    Auth::logout() // logout user
    
    Auth::check() // check is logged in?
    
    Auth::user() // (if logged in) get user

    Auth::attempt(array) // example ['username' => 'test', 'password' => 'test']
 
    Auth::id() // get user id
    
```

### 2.2. Database Migrate
```php
    // Folder path: database/migrations

    // Example: (that file is real)
    // (Folder path)/Users.php
    class Users
    {
        static $table = "users"; // create table name
        static $db = 'local'; // db key from database/connections.php

        public static function columns() // Insert columns
        {
            return [
                'id' => ['primary'],
                'username' => ['varchar:50', 'charset:utf8:general_ci'],
                'password' => ['varchar:50', 'charset:utf8:general_ci'],
                'email' => ['varchar:50', 'charset:utf8:general_ci', 'unique'],
                'api_token' => ['varchar:60', 'required', 'charset:utf8:general_ci'],
                'timestamps', // create updated_at, created_at columns
                'softDelete' // Use soft delete column
            ];
        }
    }

    // can use parameters:
    [
        'primary',
        'unique', 
        'text',
        'bigint', 
        'int', 
        'smallint', 
        'tinyint', 
        'decimal', 
        'float', 
        'varchar', // default 255
        'char', // default 50
        'char:numeric_length', 
        'varchar:numeric_length', 
        'date',
        'datetime',
        'time',
        'required', 
        'nullable', 
        'default', // default NULL 
        'default:default value', 
        'charset:utf8mb4:general_ci'
    ]

```

## 3. Date
```php
    Date::setLocale('Europe/Istanbul');
    Date::locale(); // return Europe/Istanbul
    Date::format(time()|date(), 'd.m.Y H:i');
    Date::now(); // d.m.Y H:i
    Date::timestamp(); // For mysql TIMESTAMP
```

## 4. Mail
```php
    // Config
    // edit: config/mail.php
    // if you wanna mail send you must be true sending parameter

    // Usage
    $mail = new Mail;
    $mail->send('mustafaomereser@gmail.com', [
        'subject' => 'test',
        'message' => 'test mesaj', // you can also use view('view_name', ['hash' => Str::rand()]) method and set veriables;
        'altbody' => 'Alt body',
        'attachements' => [
            'uploads/1.png',
            'uploads/2.png'
        ]
    ]);
```

## 5. Controller
```php
    class ... {
        public function __construct() {
            echo "Hi, this is __construct.";
            $this->user = new User;
        }
        
        public function index() {
            $hi = 'hey';                                    // resource/views/main.php template
            return View::view('home.index', compact('hi'), 'main');
        }
        
        public function show($id) {
            return View::view('home.user', ['user' => $this->user->first()], 'main');
            //
            return view('home.user', ['user' => $this->user->first()], 'main'); // also you can use that
        }
    }
```
## 6. View
```php
    // Use That
    view('home.index', ['hi' => 'hey'], 'main');
    
    // OR That
    use Core\View;                     // resource/views/main.php template
    echo View::view('home.index', ['hi' => 'hey'], 'main');

    // call in view. In home.index:
    <div>
        List:
        <?= view('home.list', $view_parameters); ?> // Output: echo $hi; = hey       // SAME
        <?= View::view('home.list', $view_parameters); ?> // Output: echo $hi; = hey // RESULT
    </div>
```
## 7. zhelper
```php
    ....
    C:\Users\...\Desktop\Project>php zhelper
    
    // Makes Usage:
    # Controller                // what are u want  // if u want get ready resource controller (Optional)
    > php zhelper make controller Test\TestController resource
    
    # Model                  // what are u want
    > php zhelper make model Test\Test
    
    # Middleware                  // what are u want
    > php zhelper make middleware Test\Test

    # Database Migration          // what are u want
    > php zhelper make migration Users


    # Database Migrator:
    php zhelper db migrate // output: just add/modify after changes columns.
    php zhelper db migrate fresh // output: reset table and write all columns.

    // Note: if you create first time tables you must do use fresh option.
```
## 8. Csrf
```php
    // Usage:
    Csrf::csrf(); // Output: ready csrf input
    Csrf::get(); // Output: random_csrf_string
    Csrf::set(); // Random/Renew set token
    Csrf::unset(); // Destroy csrf token
    Csrf::remainTimeOut(); // How much seconds left for change csrf token
```
## 9. Language
```php
    // Usage:
    
    // Dir tree:
    tr -> 
        lang.php // return array
        auth.php // return array
    en -> 
        lang.php // return array
        auth.php // return array

    // if you want change locale
    Lang::locale('tr');
    
    // if you wanna get a parameter
    Lang::get('lang.test', ['id' => 1, 'test' => 'hey']);
    Lang::get('auth.wrong-password');

    // How i select default lang? (if not exists in lang list browser language select default)
    config -> 
            app.php ->
                    lang => 'tr'

    // get lang list
    print_r(Lang::list());
```

## 10. Crypter
```php
    # Usage:

    $encode = Crypter::encode('test'); // result: {test_hashed_code}
    $decode = Crypter::decode($encode); // result: test

    $encodeArray = Crypter::encodeArray(['test', 'test2']);
    $decodeArray = Crypter::decodeArray($encodeArray);
```


## 11. Config
```php
    Config::get('app'); // return all config
    Config::get('app.title'); // return in app config title index's element
    Config::set('app', [
        'title' => 'test'
    ]); // update config
```

## 12. Alerts
```php
    // Alerts is show just one time, when you refresh your page Alerts is gone.

    # Usage:
    Alerts::danger('text');
    Alerts::success('text');
    Alerts::info('text');
    Alerts::warning('text');
    
    // if you wanna use like chain
    Alerts::danger('text')::success('text')::info('text')::warning('text');

    // get alerts
    Alerts::get(); // output: Array ([0] => ('success', 'text'), [1] => ('danger', 'text'))

    // unset alerts
    Alerts::unset();
```

```html
    <!-- shown alerts example bootstrap -->
    <?php foreach(Alerts::get() as $alert): ?>
        <div class="alert alert-<?= $alert[0] ?>">
            <?= $alert[0] ?>: <?= $alert[1] ?>
        </div>
    <?php endforeach; ?>
```

## 13. Validator
```php
    // In array validate values.
    // Current: type, required, max, min, same, email, unique, exists.
    
    // Unique ussage: 
    # unique:table_name cl=column_name,db=database // cl and db parameters is optional, if you not add cl parameter get request key name, if you not add db parameter get first in array connection.

    # exists:table_name cl=column_name,db=database // cl and db parameters is optional, if you not add cl parameter get request key name, if you not add db parameter get first in array connection.
    
    // Unique Example: 'email' => ["unique:users cl=email,db=local"]
    // Exists Example: 'email' => ["exists:users cl=email,db=local"]

    Validator::validate($_REQUEST, [
        'test1' => ['type:string', 'required', 'max:10', 'min:5', 'same:test2'],
        'test2' => ['same:test1'],
    ]);
```
##  14. Middleware
```php
    # App\Middlewares\Auth.php
    # Validate first and go on.
    
    namespace App\Middlewares;
    class Auth
    {
        public function __construct()
        {
            if (@$_SESSION['user_id']) return true;
        }
    
        public function error()
        {
            abort(401);
        }
    }

    // Usage:
    Middleware::middleware([Auth::class, Guest::class]); // output: false
    Middleware::middleware([Auth::class]); // if you are logged in      # output: true 
    Middleware::middleware([Guest::class]); // if you are not logged in # output: true 
    

    Middleware::middleware([Auth::class, Guest::class], function($declined) {
        print_r($declined);
    }); // if you are logged in     # output: Array ('Guest::class')
        // if you are not logged in # output: Array ('Auth::class')
```

## 15. API
```php
    # route/api.php
    Route::get('/test', function () {
        echo "API Page / user_id: " . Auth::id();
    });
    // example: http://localhost/api/test?user_token=12345678 (user logged in.)
```

## 16. Development
```php
    // Database connections
    # Folder: database/connections.php
    
    # before
    $databases = [
        'local' => ['mysql:host=localhost;dbname=test;charset=utf8mb4', 'root', '123123'],
    ];

    # add a database
    $databases = [
        'local' => ['mysql:host=localhost;dbname=test;charset=utf8mb4', 'root', '123123'],
        'custom_db_name' => ['mysql:host=localhost;dbname=test_2;charset=utf8mb4', 'root', '123123'],
    ];

    # result database two connection.


    // Usage Crypter
    # Folder config/app.php
    'key' => 'cryptkey',
    'salt' => 'ThisSaltIsSecret',

    # if you change that your hash encode's will change, and all hash need be unique for security.
    # example
    'key' => '82FDFE2976AC2C8B8EBD5A5737118',
    'salt' => '4ljd5AyZc9',

    # it is so secure. crypter make passwords or etc.
```

## 17. Helper Methods
```php
    // main base path
    base_path("optional url add");
    
    // Public path
    public_path("optional url add");

    // Show host name
    host();

    // Redirect
    redirect("URL");

    // Redirect to REFERER
    back();

    // Show current uri
    uri();

    // get current request method
    method();

    // show input method
    inputMethod('GET|POST|PATCH|PUT|DELETE');

    // Get Client IP
    ip();

    // Set http response code 200 to 500 and optional message.
    abort(200, 'OK');

    // get request
    request('name');

    // Response for Controllers or routes callbacks
    Response::json([
        'test' => 1
    ]);

    // show csrf input
    csrf();

    // Call view method easy way, it's same View::view() 
    view(...., ....., ....);

    // shortcut for Lang::get()
    _l(...);

    // shortcut for Config::get()
    config(...);

    // File
    
    # Usage:
    File::save('/uploads', 'http://images.com/image.jpg'); // uploads/**********.jpg

    // For one
    File::upload('/uploads', $_FILES['file'], [ // settings is optional
        'accept' => ['jpg', 'jpeg', 'png'],
        'size' => 300000 # byte
    ]); // return /uploads/image.ext

    // For multip
    File::upload('/uploads', $_FILES['files'], [ // settings is optional
        'accept' => ['jpg', 'jpeg', 'png'],
        'size' => 300000 # byte
    ]); // return array('/uploads/image.ext', '/uploads/image.ext');

                                # width, height
    File::resizeImage('file_path', 50, 50);
```

## 18. Run Project
```php
    ....
    // Default run host's ip and 1000 port
    C:\Users\...\Desktop\Project>php run (press enter)
    
    // with custom ip and port
    C:\Users\...\Desktop\Project>php run 127.0.0.1 2000 (press enter)
```