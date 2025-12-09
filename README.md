# PHP Mini MVC Framework

A minimal, easy-to-read PHP MVC framework intended for learning and small projects.
This README shows the main concepts and common tasks: routing, validation,
models, views, controllers, migrations, middleware, service container, helper
functions, seeders, and CLI commands.

---

## Quick Start

1. Clone the repo:
   ```
   git clone https://github.com/anwar7736/PHP-Mini-MVC-Framework.git
   cd PHP-Mini-MVC-Framework
   ```

2. Install dependencies (if using Composer):
   ```
   composer install
   ```

3. Copy environment file and set config:
   ```
   cp .env.example .env
   # Update DB and other credentials in .env
   ```

4. Run migrations and seeders:
   ```
   php artisan migrate --seed
   ```

---

## Project Structure

- app/
    - helpers/         # Global helper functions
    - Http/
       - Controllers/  # Request handlers
       - Middleware/   # Request middleware
       - Request       # Request class
    - Models/          # Database models / ORM
- config/              # All config files and classes
- database
    - migrations/      # Schema migrations
    - seeders/         # Development data seeders
- public/              # Document root (index.php)
- resources/views/     # PHP templates
- routes/web.php       # Route definitions
- storage/logs/        # Application all log files
- stubs/               # Command templates (controller,middleware,migration,model,seeder)
- vendor/              # All packages are here (composer,vlucas etc.)
- artisan              # Command-line entry (migrate, seed, make:*)
- README.md            # Application documentation

---

## Routing

Routes map HTTP methods and URIs to controllers.

Example:
```php
Route::get('/', [App\Controllers\HomeController::class, 'index']);
Route::post('/users', [App\Controllers\UserController::class, 'store']);
Route::get('/users/{id}', [App\Controllers\UserController::class, 'show']);
```

- Path parameters like `{id}` are injected into the controller method.
- You can attach middleware per-route: `->middleware('auth')`.

---

## Controllers

Controllers handle requests, orchestrate services/models, and return responses.

Example:
```php
namespace App\Controllers;

use App\Models\User;

class UserController
{
    public function show($id)
    {
        $user = User::find($id);
        return view('users.show', ['user' => $user]);
    }

    public function store(Request $request)
    {
        // validate, create, redirect
    }
}
```

Keep controllers thin — move business logic into services or models.

---

## Models

Models provide simple CRUD and query helpers. This framework aims for a tiny ActiveRecord-style API.

Example:
```php
$user = User::find(1);
$users = User::where('active', 1)->get();
User::create(['name' => 'Alex', 'email' => 'a@example.com']);
```

Recommended methods: find, all, where, create, update, delete.

---

## Views

Views are plain PHP templates stored in `app/Views/`.

Render a view with:
```php
return view('users.index', compact('users'));
```

Within `resources/views/users/index.php`:
```php
<?php foreach ($users as $user): ?>
  <p><?= htmlspecialchars($user->name) ?></p>
<?php endforeach; ?>
```

---

## Validation

Use a Validator helper to declare rules and check input.

Example:
```php
$rules = [
  'name'  => 'required|string', // or ['required', 'string'],
  'email' => 'required|email|unique:users',
];

$validator = Validator::make($request->all(), $rules);

if ($validator->fails()) {
        return response([
        'success' => false, 
        'errors'  => $validator->errors()
    ]);
}

// proceed when valid
```

Support common rules: required, string, numeric, email, min, max, in, unique, confirmed.

---

## Middleware

Middleware run before/after controller actions for tasks like authentication, CORS, and logging.

Example:
```php
class AuthMiddleware
{
    public function handle($request, $next)
    {
        if (!session('user_id')) {
            return redirect('/login');
        }
        return $next($request);
    }
}
```

Register globally or attach to routes.

---

## Migrations

Migrations manage schema changes via up() and down() methods.

Example:
```php
class CreateUsersTable
{
    public function up()
    {
        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
```

Run migrations with:
```
php artisan migrate
php artisan migrate:rollback
```

---

## Seeders

Seeders populate development data.

Example:
```php
class UsersTableSeeder
{
    public function run()
    {
        User::insert([
                ['name' => 'Alice', 'phone' => '01700000000', 'email' => 'alice@example.com'],
                ['name' => 'Bob', 'phone' => '01700000000', 'email' => 'bob@example.com']
            ]);
    }
}
```

Run seeders:
```
php artisan db:seed --seeder=UsersTableSeeder
```

---

## Service Container

A lightweight container resolves dependencies and singletons.

Binding:
```php
$container->bind(\App\Contracts\MailInterface::class, \App\Services\SmtpMailer::class);
```

Resolving:
```php
$mailer = $container->get(\App\Contracts\MailInterface::class);
```

Controllers and services may request dependencies via constructor injection if the router/container supports it.

---

## Helper Functions

Common helpers in `helpers.php`:

- env($key, $default = null)
- config($key, $default = null)
- view($name, $data = [])
- route($name, $params = [])
- redirect($url)
- asset($path)
- dd(...$vars)

Keep helpers minimal and focused.

---

## CLI Commands

Provided CLI (in `cli`) for common tasks:

- php artisan make:controller Name
- php artisan make:model Name -mcs //migration, controller & seeder
- php artisan make:migration CreateUsersTable --create=users
- php artisan migrate
- php artisan migrate:fresh --seed
- php artisan migrate:refresh --step=3
- php artisan migrate:rollback --step=5
- php artisan db:seed

Example: generate a controller
```
php artisan make:controller UserController
```

---

## Example Flow: Create & Show User

1. Route:
```php
// //PostController
Route::get('/', [PostController::class, 'index'])->name('home');
Route::get('/my-post', [PostController::class, 'myPost'])->name('my-post')->middleware('auth');
Route::get('/post-create', [PostController::class, 'create'])->name('post.create')->middleware('auth');
Route::post('/post-store', [PostController::class, 'store'])->name('post.store')->middleware('auth');
Route::get('/post-show/{id}', [PostController::class, 'show'])->name('post.show');
Route::get('/post-edit/{id}', [PostController::class, 'edit'])->name('post.edit')->middleware('auth');
Route::put('/post-update/{id}', [PostController::class, 'update'])->name('post.update')->middleware('auth');
Route::delete('/post-destroy/{id}', [PostController::class, 'destroy'])->name('post.destroy')->middleware('auth');
```

2. Controller:
```php
public function show($id)
{
    $user = User::find($id);
    return view('users.show', compact('user'));
}
```

3. View: `resources/views/users/show.php`
```php
<h1><?= htmlspecialchars($user->name) ?></h1>
<p><?= htmlspecialchars($user->email) ?></p>
```

---

## Best Practices

- Keep controllers thin; place business logic in services or models.
- Validate input at the controller boundary.
- Use migrations and seeders to manage schema and sample data.
- Make middleware single-purpose and composable.
- Use the service container for external dependencies and testability.

---

## Contributing

1. Fork the repo.
2. Create a feature branch.
3. Follow PSR-12 coding style.
4. Submit a pull request with a clear description.

---

## License

MIT License — see LICENSE file for details.
```