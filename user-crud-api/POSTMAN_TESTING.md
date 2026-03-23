# Postman Testing Guide

Base URL: `http://127.0.0.1:8000`

For every request that has a body: click **Body** → **raw** → set dropdown to **JSON**.

---

## Test 1 — Create a User (POST)

```
POST http://127.0.0.1:8000/api/users
```

**Body:**
```json
{
  "username": "johndoe",
  "fullName": "John Doe",
  "email": "john@example.com",
  "password": "secret123"
}
```

**Expected: 201 Created**
```json
{
  "success": true,
  "message": "User created successfully.",
  "data": {
    "id": 1,
    "username": "johndoe",
    "fullName": "John Doe",
    "email": "john@example.com",
    "roles": ["ROLE_USER"]
  }
}
```

> Password is intentionally not returned in the response.

---

## Test 2 — Get All Users (GET)

```
GET http://127.0.0.1:8000/api/users
```

No body required.

**Expected: 200 OK** — returns an array of all users.

---

## Test 3 — Get Single User (GET)

```
GET http://127.0.0.1:8000/api/users/1
```

No body required.

**Expected: 200 OK** — returns one user by ID.

---

## Test 4 — Partial Update / Change Password (PATCH)

Only the fields you send will be updated. Everything else stays the same.

```
PATCH http://127.0.0.1:8000/api/users/1
```

**Body:**
```json
{
  "password": "newpassword456"
}
```

**Expected: 200 OK**

---

## Test 5 — Full Update (PUT)

All fields are required.

```
PUT http://127.0.0.1:8000/api/users/1
```

**Body:**
```json
{
  "username": "johndoe_v2",
  "fullName": "John Doe Updated",
  "email": "john.updated@example.com",
  "password": "brandnewpassword"
}
```

**Expected: 200 OK**

---

## Test 6 — Delete User (DELETE)

```
DELETE http://127.0.0.1:8000/api/users/1
```

No body required.

**Expected: 200 OK**
```json
{
  "success": true,
  "message": "User 'johndoe' deleted successfully."
}
```

---

## Test 7 — Validation Error (POST)

Send bad data to test error handling.

```
POST http://127.0.0.1:8000/api/users
```

**Body:**
```json
{
  "username": "test",
  "fullName": "Test User",
  "email": "not-a-valid-email",
  "password": "123"
}
```

**Expected: 422 Unprocessable Entity**
```json
{
  "success": false,
  "message": "Please provide a valid email address."
}
```

---

## Test 8 — Duplicate Email (POST)

Try creating a user with an email that already exists.

**Expected: 409 Conflict**
```json
{
  "success": false,
  "message": "Email 'john@example.com' is already registered."
}
```

---

## Test 9 — User Not Found (GET)

```
GET http://127.0.0.1:8000/api/users/99999
```

**Expected: 404 Not Found**
```json
{
  "success": false,
  "message": "User with ID 99999 not found."
}
```

---

## Verify Password Hashing

After creating a user, open `var/database.db` in **DB Browser for SQLite**:

1. Download: https://sqlitebrowser.org/dl/
2. Open the file: `user-crud-api/var/database.db`
3. Click **Browse Data** → select table `user`
4. Look at the `password` column

You will see something like `$2y$13$xK9mP...` instead of `secret123`. That is bcrypt hashing — the original password is gone.
