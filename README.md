# Z Framework (V1.0.1)
### Easiest, fastest PHP framework. (Simple)

## Features

- 0.1 [Route](#route-doc)
- 0.2 [Model](#model-doc)
- 0.3 [Controller](#controller-doc)
- 0.4 [View](#view-doc)
- 0.5 [zhelper](#zhelper-doc)
- 0.6 [Csrf](#csrf-doc)
- 0.7 [Validator](#view-doc)
- 0.8 [Middleware](#middleware-doc)
- 0.9 [API](#api-doc)
- 1.0 [Development](#development-doc)
- 1.1 [Run Project](#run-doc)

#route doc
## 0.1 - Route
```php
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
   |-------------------------------------------|
   |    URL   | METHOD |  Callback Function    |
   |-------------------------------------------|
   |    /         |    GET    | index()        |
   |    /         |    POST   | store()        |
   |    /{id}     |    GET    | show($id)      |
   |  /{id}/edit  |    GET    | edit($id)      | 
   |   /create    |    GET    | create()       |
   |   /{id}      | PUT/PATCH | update($id)    |
   |   /{id}      |   DELETE  | delete($id)    |
   |-------------------------------------------|
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

#model-doc
## 0.2 - Model
```php
    class User extends Model {
        public $table = "users";
        public $db = "local"; // (optional) if you do not write that it's connect your first connection.
    }
    
    // Usage:
    $user = new User;
    print_r(
        $user->where('id', '=', 1)->get(),
        $user->where('id', '=', 1)->first(),
        $user->count(),
        $user->insert([
            'username' => 'username',
            'password' => 'password',
            'email' => 'email@mail.com'
        ]),
        $user->update([
            'email' => 'changed@mail.com'
        ], ['id' => 1, 'email' => 'email@mail.com']),
    );
```
#controller-doc
## 0.3 - Controller
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
#view-doc
## 0.4 - View
```php
    use Core\View;                     // resource/views/main.php template
    echo View::view('home.index', ['hi' => 'hey'], 'main');
    
    // in home.index:
    <div>
        List:
        <?= View::view('home.list', $view_parameters); ?> // Output: echo $hi; = hey
    </div>
```
#zhelper-doc
## 0.5 - zhelper
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
```
#csrf-doc
## 0.6 - Csrf
```php
    // Usage:
    Csrf::get(); // Output: random_csrf_string
    Csrf::set(); // Random/Renew set token
    Csrf::unset(); // Destroy csrf token
    Csrf::remainTimeOut(); // How much seconds left for change csrf token
```
#validator-doc
## 0.7 - Validator
```php
    // In array validate values.
    // Current: type, required, max, min, same.
    
    Validator::validate($_REQUEST, [
        'test1' => ['type:string', 'required', 'max:10', 'min:5', 'same:test2'],
        'test2' => ['same:test1'],
    ]);
```
#middleware-doc
## 0.8 - Middleware
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
```
#api-doc
## 0.9 - API
```php
    # route/api.php
    Route::get('/test', function () {
        echo "API page user_id: ".Auth::user()['id'];
    });
    
    // example: http://localhost/api/test?user_token=12345678 (user logged in.)
```

#development-doc
## 1.0 - Development
--

#run-doc
## 1.1 - Run Project
```php
    ....
    C:\Users\...\Desktop\Project>run (press enter)
```