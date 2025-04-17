# 📚 Bookstore API

A RESTful API for managing books and authors with Laravel Sanctum authentication.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)

## ✨ Features

- 🔐 JWT Authentication with Laravel Sanctum
- 📖 CRUD operations for Books
- ✍️ CRUD operations for Authors
- 👑 Admin authorization middleware
- 🔢 Pagination and filtering
- 📄 API documentation with Postman

## 🚀 Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/ComibyteOrg/bookstoreapi.git
   cd bookstoreapi
   ```

2. Install dependencies:
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

3. Configure your database in `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bookstore
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```

5. Start the development server:
   ```bash
   php artisan serve
   ```

## 🌐 API Endpoints

### Authentication
| Method | Endpoint       | Description          |
|--------|----------------|----------------------|
| POST   | `/api/login`   | Login with credentials |
| POST   | `/api/logout`  | Logout current user   |

### Books
| Method | Endpoint       | Description          | Auth Required |
|--------|----------------|----------------------|---------------|
| GET    | `/api/books`   | List all books       | No            |
| POST   | `/api/books`   | Create new book      | Yes (Admin)   |
| GET    | `/api/books/{id}` | Get single book   | No            |
| PUT    | `/api/books/{id}` | Update book       | Yes (Admin)   |
| DELETE | `/api/books/{id}` | Delete book       | Yes (Admin)   |

### Authors
| Method | Endpoint         | Description          | Auth Required |
|--------|------------------|----------------------|---------------|
| GET    | `/api/authors`   | List all authors     | No            |
| POST   | `/api/authors`   | Create new author    | Yes (Admin)   |
| GET    | `/api/authors/{id}` | Get single author | No            |
| PUT    | `/api/authors/{id}` | Update author     | Yes (Admin)   |
| DELETE | `/api/authors/{id}` | Delete author     | Yes (Admin)   |

## 🔒 Authentication

1. First, login to get your token:
   ```bash
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@bookstore.com","password":"password","device_name":"postman"}'
   ```

2. Use the token in subsequent requests:
   ```bash
   curl -X GET http://localhost:8000/api/books \
     -H "Authorization: Bearer YOUR_TOKEN" \
     -H "Accept: application/json"
   ```

## 🧑‍💻 Development

- Run tests:
  ```bash
  php artisan test
  ```

- Generate API documentation:
  ```bash
  php artisan l5-swagger:generate
  ```

## 📝 License

This project is open-source and available under the [MIT License](LICENSE).

---

Made with ❤️ by [Oluwadimu Adedeji (Comibyte)] using Laravel
```

### Key Features of This README:

1. **Visual Badges** - Shows technologies used at a glance
2. **Clear Installation** - Step-by-step setup instructions
3. **API Documentation** - Table format for easy reference
4. **Authentication Guide** - Shows how to get and use tokens
5. **Development Notes** - Helpful commands for contributors
6. **Responsive Formatting** - Looks good on GitHub and mobile
