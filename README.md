# Z Framework (V1.0.1)
### Easiest, fastest PHP framework. (Simple)

## Features

  - [1. Route](#1-route)
    - [1.1. Form examples](#11-form-examples)
    - [1.2. Route Options](#12-route-options)
  - [2. Model](#2-model)
  - [3. Controller](#3-controller)
  - [4. View](#4-view)
  - [5. zhelper](#5-zhelper)
  - [6. Csrf](#6-csrf)
  - [7. Validator](#7-validator)
  - [8. Middleware](#8-middleware)
  - [9. API](#9-api)
  - [## 10. Development](#10-development)
  - [11. Run Project](#11-run-project)

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
        return 'patch.';
   });
   
   // DELETE METHOD Route
   Route::delete('/', function() {
        return 'delete.';
   });
   
   // if you create resource controller it's like that simple
   Route::resource('/', TestController::class);
   
   
    Resource Route list:
   
    |--------------------------------------------|
    | URL        | METHOD    | Callback Function |
    |------------|-----------|-------------------|
    | /          | GET       | index()           |
    | /          | POST      | store()           |
    | /{id}      | GET       | show($id)         |
    | /{id}/edit | GET       | edit($id)         |
    | /create    | GET       | create()          |
    | /{id}      | PUT/PATCH | update($id)       |
    | /{id}      | DELETE    | delete($id)       |
    |--------------------------------------------|
```
### 1.1. Form examples

```html
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
```
## 2. Model
```php
    class User extends Model {
        public $table = "users";
        public $db = "local"; // (optional) if you do not write that it's connect your first connection.
    }
    
    // Usage:
    
    use App\Models\User;
    $user = new User;
    echo "<pre>";
    print_r([
        "get" => $user->get(),
        "first" => $user->where('id', '=', 1)->first(),
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

    // Select example
    $user->select('id, username')->get();

    // OrderBy example
    $user->orderBy(['id' => 'ASC', 'username' => 'DESC'])->get();
    
    // Limit example args: 10(startCount), 10(rowCount)
    $user->limit(5, 10)->get();

    // Joins example
    $user->join('LEFT|RIGHT|OUTER|FULL|NULL', 'table_name', ['table_name.id', '=', 'this_table.id'])->get();

```
## 3. Controller
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
        }
    }
```
## 4. View
```php
    use Core\View;                     // resource/views/main.php template
    echo View::view('home.index', ['hi' => 'hey'], 'main');
    
    // in home.index:
    <div>
        List:
        <?= View::view('home.list', $view_parameters); ?> // Output: echo $hi; = hey
    </div>
```
## 5. zhelper
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
```
## 6. Csrf
```php
    // Usage:
    Csrf::get(); // Output: random_csrf_string
    Csrf::set(); // Random/Renew set token
    Csrf::unset(); // Destroy csrf token
    Csrf::remainTimeOut(); // How much seconds left for change csrf token
```
## 7. Validator
```php
    // In array validate values.
    // Current: type, required, max, min, same.
    
    Validator::validate($_REQUEST, [
        'test1' => ['type:string', 'required', 'max:10', 'min:5', 'same:test2'],
        'test2' => ['same:test1'],
    ]);
```
##  8. Middleware
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

## 9. API
```php
    # route/api.php
    Route::get('/test', function () {
        echo "API Page / user_id: " . Auth::id();
    });
    // example: http://localhost/api/test?user_token=12345678 (user logged in.)
```

## 10. Development
--

## 11. Run Project
```php
    ....
    C:\Users\...\Desktop\Project>php run (press enter)
```