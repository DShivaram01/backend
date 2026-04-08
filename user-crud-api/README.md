# Symfony 5.4 — User Management REST API

A REST API built with Symfony 5.4 that demonstrates CRUD operations on a User resource with password hashing.

---

## Requirements

| Tool | Version |
|---|---|
| PHP | 8.0+ |
| Composer | 2.x |
| Symfony CLI | 5.x |
| XAMPP | Any (for PHP on Windows) |

---

## Installation

### Step 1 — Install Symfony CLI (Windows via Scoop)

Allow scripts and install Scoop:
```bash
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
Invoke-RestMethod -Uri https://get.scoop.sh | Invoke-Expression
```

Install Symfony CLI:
```bash
scoop install symfony-cli
```

Verify:
```bash
symfony version
```

---

### Step 2 — Install Composer Globally

Download and run the installer: https://getcomposer.org/Composer-Setup.exe

When prompted, point it to your PHP executable:
```
C:\Users\YOUR_NAME\Desktop\xampp\php\php.exe
```

Verify:
```bash
composer --version
```

---

### Step 3 — Create the Project

```bash
cd C:\xampp\htdocs
symfony new user-crud-api --version="5.4.*"
cd user-crud-api
code .
```

---

### Step 4 — Install Required Packages

```bash
composer require symfony/orm-pack
composer require symfony/maker-bundle --dev
composer require symfony/security-bundle
```

Verify packages installed correctly:
```bash
php bin/console list make
```

---

### Step 5 — Configure the Database

Open `.env` and replace the `DATABASE_URL` line with:
```
DATABASE_URL="sqlite:///%kernel.project_dir%/var/database.db"
```

---

### Step 6 — Create the Database

```bash
php bin/console doctrine:database:create
```

You will see `var/database.db` appear in your project folder.

---

### Step 7 — Generate the User Entity

```bash
php bin/console make:user
```

Answer the prompts:
```
Name of the security user class? → User
Store user data in the database? → yes
Unique display name property?    → email
App needs to hash passwords?     → yes
```

Then add `username` and `fullName` fields:
```bash
php bin/console make:entity User
```

Add these fields when prompted:

| Property | Type | Length | Nullable |
|---|---|---|---|
| username | string | 100 | no |
| fullName | string | 255 | no |

Press Enter (empty) to stop adding fields.

---

### Step 8 — Create the Migration

Symfony reads your entity and writes the SQL automatically:
```bash
php bin/console make:migration
```

---

### Step 9 — Run the Migration

Applies the SQL and creates the `user` table in the database:
```bash
php bin/console doctrine:migrations:migrate
```

Type `yes` when prompted.

---

### Step 10 — Generate the Controller

```bash
php bin/console make:controller UserController
```

Open `src/Controller/UserController.php`, select all, delete, and paste the controller code from `src/Controller/UserController.php` in this repository.

---

### Step 11 — Verify Routes and Start the Server

Confirm all 6 endpoints are registered:
```bash
php bin/console debug:router | findstr api
```

Expected output:
```
api_users_index     GET    /api/users
api_users_create    POST   /api/users
api_users_show      GET    /api/users/{id}
api_users_update    PUT    /api/users/{id}
api_users_patch     PATCH  /api/users/{id}
api_users_delete    DELETE /api/users/{id}
```

Start the server:
```bash
symfony server:start
```

Your API is live at: **http://127.0.0.1:8000**

---

## API Endpoints

| Method | Endpoint | Action |
|---|---|---|
| GET | `/api/users` | List all users |
| POST | `/api/users` | Create a new user |
| GET | `/api/users/{id}` | Get one user |
| PUT | `/api/users/{id}` | Full update |
| PATCH | `/api/users/{id}` | Partial update |
| DELETE | `/api/users/{id}` | Delete a user |

---

## Password Hashing

Passwords are hashed using bcrypt via Symfony's Security Bundle. Plain text passwords are never stored in the database.

See [POSTMAN_TESTING.md](POSTMAN_TESTING.md) to test all endpoints.
