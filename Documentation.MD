# Building a RESTful API for a Bookstore with Laravel

## Prerequisites

- PHP (8.0 or higher)
- Composer
- MySQL or other database
- Laravel installed globally or use Composer to create project

## Step 1: Set Up the Laravel Project

1. **Create a new Laravel project**:
   ```bash
   composer create-project laravel/laravel bookstore-api
   cd bookstore-api
   ```

2. **Set up database configuration**:
   - Edit `.env` file with your database credentials:
     ```
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=bookstore
     DB_USERNAME=root
     DB_PASSWORD=
     ```

## Step 2: Install Laravel Sanctum for API Authentication
1. Install Sanctum via Composer:
   ```bash
   php artisan install:api
   ```

2. Publish the Sanctum configuration and migration files:
   ```bash
   php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
   ```

3. Run the migrations:
   ```bash
   php artisan migrate
   ```

## Step 3: Create Models and Migrations

1. **Create Author model and migration**:
   ```bash
   php artisan make:model Author -m
   ```

   Edit the migration file (`database/migrations/xxxx_create_authors_table.php`):
   ```php
   public function up()
   {
       Schema::create('authors', function (Blueprint $table) {
           $table->id();
           $table->string('name');
           $table->text('bio')->nullable();
           $table->timestamps();
       });
   }
   ```

2. **Create Book model and migration**:
   ```bash
   php artisan make:model Book -m
   ```

   Edit the migration file (`database/migrations/xxxx_create_books_table.php`):
   ```php
   public function up()
   {
       Schema::create('books', function (Blueprint $table) {
           $table->id();
           $table->string('title');
           $table->string('isbn')->unique();
           $table->foreignId('author_id')->constrained()->onDelete('cascade');
           $table->string('genre');
           $table->timestamps();
       });
   }
   ```

3. Run the migrations:
   ```bash
   php artisan migrate
   ```

## Step 4: Set Up Model Relationships

1. Edit `app/Models/Author.php`:
   ```php
   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;

   class Author extends Model
   {
       use HasFactory;

       protected $fillable = ['name', 'bio'];

       public function books()
       {
           return $this->hasMany(Book::class);
       }
   }
   ```

2. Edit `app/Models/Book.php`:
   ```php
   namespace App\Models;

   use Illuminate\Database\Eloquent\Factories\HasFactory;
   use Illuminate\Database\Eloquent\Model;

   class Book extends Model
   {
       use HasFactory;

       protected $fillable = ['title', 'isbn', 'author_id', 'genre'];

       public function author()
       {
           return $this->belongsTo(Author::class);
       }
   }
   ```

## Step 5: Create Controllers

1. **Create AuthorController**:
   ```bash
   php artisan make:controller AuthorController --api
   ```

2. **Create BookController**:
   ```bash
   php artisan make:controller BookController --api
   ```

3. **Create AuthController for authentication**:
   ```bash
   php artisan make:controller AuthController
   ```

## Step 6: Implement Authentication

Edit `app/Http/Controllers/AuthController.php`:
```php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return $user->createToken($request->device_name)->plainTextToken;
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json(['message' => 'Logged out successfully']);
    }
}
```

## Step 7: Implement BookController

Edit `app/Http/Controllers/BookController.php`:
```php
namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = Book::with('author');
        
        // Filter by author if author_id is provided
        if ($request->has('author_id')) {
            $query->where('author_id', $request->author_id);
        }
        
        // Filter by genre if genre is provided
        if ($request->has('genre')) {
            $query->where('genre', $request->genre);
        }
        
        // Paginate results
        return $query->paginate(10);
    }

    public function store(Request $request)
    {
        // Only admin can add books
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isbn' => 'required|string|unique:books|max:255',
            'author_id' => 'required|exists:authors,id',
            'genre' => 'required|string|max:255',
        ]);

        $book = Book::create($validated);

        return response()->json($book, 201);
    }

    public function show(Book $book)
    {
        return $book->load('author');
    }

    public function update(Request $request, Book $book)
    {
        // Only admin can update books
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'isbn' => 'sometimes|string|unique:books,isbn,'.$book->id.'|max:255',
            'author_id' => 'sometimes|exists:authors,id',
            'genre' => 'sometimes|string|max:255',
        ]);

        $book->update($validated);

        return response()->json($book);
    }

    public function destroy(Request $request, Book $book)
    {
        // Only admin can delete books
        if (!$request->user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $book->delete();

        return response()->json(null, 204);
    }
}
```

## Step 8: Implement AuthorController

Edit `app/Http/Controllers/AuthorController.php`:
```php
namespace App\Http\Controllers;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        return Author::paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $author = Author::create($validated);

        return response()->json($author, 201);
    }

    public function show(Author $author)
    {
        return $author->load('books');
    }

    public function update(Request $request, Author $author)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'bio' => 'nullable|string',
        ]);

        $author->update($validated);

        return response()->json($author);
    }

    public function destroy(Author $author)
    {
        $author->delete();

        return response()->json(null, 204);
    }
}
```

## Step 9: Set Up API Routes

Edit `routes/api.php`:
```php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('authors', AuthorController::class);
Route::apiResource('books', BookController::class);
```

## Step 10: Create Admin User

1. Create a new migration for adding is_admin to users:
   ```bash
   php artisan make:migration add_is_admin_to_users_table --table=users
   ```

2. Edit the migration file:
   ```php
   public function up()
   {
       Schema::table('users', function (Blueprint $table) {
           $table->boolean('is_admin')->default(false);
       });
   }
   ```

3. Run the migration:
   ```bash
   php artisan migrate
   ```

4. Create a seeder for admin user:
   ```bash
   php artisan make:seeder AdminUserSeeder
   ```

5. Edit `database/seeders/AdminUserSeeder.php`:
   ```php
   use App\Models\User;
   use Illuminate\Database\Seeder;

   class AdminUserSeeder extends Seeder
   {
       public function run()
       {
           User::create([
               'name' => 'Admin',
               'email' => 'admin@bookstore.com',
               'password' => bcrypt('password'),
               'is_admin' => true,
           ]);
       }
   }
   ```

6. Run the seeder:
   ```bash
   php artisan db:seed --class=AdminUserSeeder
   ```

## Step 11: Test the API

You can now test your API using Postman or any API testing tool. Here are some example requests:

1. **Login to get token**:
   ```
   POST /api/login
   Body: {
       "email": "admin@bookstore.com",
       "password": "password",
       "device_name": "postman"
   }
   ```

2. **Create a new author**:
   ```
   POST /api/authors
   Headers: {
       Authorization: Bearer [your_token]
   }
   Body: {
       "name": "J.K. Rowling",
       "bio": "British author best known for the Harry Potter series"
   }
   ```

3. **Create a new book**:
   ```
   POST /api/books
   Headers: {
       Authorization: Bearer [your_token]
   }
   Body: {
       "title": "Harry Potter and the Philosopher's Stone",
       "isbn": "9780747532743",
       "author_id": 1,
       "genre": "Fantasy"
   }
   ```

4. **Get all books**:
   ```
   GET /api/books
   ```

5. **Filter books by genre**:
   ```
   GET /api/books?genre=Fantasy
   ```

6. **Filter books by author**:
   ```
   GET /api/books?author_id=1
   ```
