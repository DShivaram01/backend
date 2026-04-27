# 📋 Anonymous Survey System

A web-based anonymous survey system built with **Symfony 5.4** and **MySQL (XAMPP)**. Admins upload CSV files to generate questionnaires with unique URLs. Participants answer without login. Admins view and download results.

---

## 🔍 What This Project Does

- Admin uploads a CSV file → system creates a survey with a unique URL
- Each survey gets a unique link like `/survey/a1b2c3d4`
- Admin can turn survey ON or OFF from the dashboard
- Users visit the URL and submit answers anonymously
- Admin views all responses and downloads them as CSV
- No login required for users — only admins need to log in

---

## 🧱 Tech Stack

| Layer | Technology |
|---|---|
| Framework | Symfony 5.4 |
| Language | PHP 8.x |
| Database | MySQL (XAMPP) |
| Templates | Twig |
| Server | Symfony CLI |

---

## 📁 Project Structure

```
survey-system/
├── src/
│   ├── Controller/
│   │   ├── SecurityController.php   ← Login/Logout
│   │   ├── AdminController.php      ← Dashboard, Upload, Toggle, Results
│   │   └── SurveyController.php     ← Public survey page, Submit
│   ├── Entity/
│   │   ├── Admin.php                ← Admin user
│   │   ├── Survey.php               ← Survey name, token, active status
│   │   ├── Question.php             ← Question, correct answer, wrong options
│   │   └── Response.php             ← User submitted answers
│   └── Command/
│       └── CreateAdminCommand.php   ← Creates admin account
├── templates/
│   ├── security/login.html.twig
│   ├── admin/dashboard.html.twig
│   ├── admin/results.html.twig
│   └── survey/
│       ├── show.html.twig
│       ├── inactive.html.twig
│       └── thankyou.html.twig
├── config/
│   └── packages/security.yaml
└── .env                             ← Database URL config
```

---

## 📦 Requirements

| Tool | Version |
|---|---|
| PHP | 8.0+ |
| Composer | 2.x |
| Symfony CLI | 5.x |
| XAMPP | Any (Apache + MySQL) |

---

## 🚀 Installation on a New Machine

### Step 1 — Install Tools
- PHP via XAMPP: https://www.apachefriends.org/
- Composer: https://getcomposer.org/Composer-Setup.exe
- Symfony CLI via Scoop:
```bash
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser
Invoke-RestMethod -Uri https://get.scoop.sh | Invoke-Expression
scoop install symfony-cli
```

### Step 2 — Clone the Repository
```bash
cd C:\xampp\htdocs
git clone https://github.com/DShivaram01/backend.git
cd backend/survey-system
```

### Step 3 — Install PHP Dependencies
```bash
composer install
```

### Step 4 — Start XAMPP
- Open XAMPP Control Panel
- Start Apache and MySQL
- Open phpMyAdmin: http://localhost/phpmyadmin
- Create a new database called `survey_system`

### Step 5 — Configure Database
Open `.env` and set:
```
DATABASE_URL="mysql://root:@127.0.0.1:3306/survey_system?serverVersion=8.0&charset=utf8mb4"
```

### Step 6 — Create Database Tables
```bash
php bin/console doctrine:schema:update --force
```

### Step 7 — Create Admin Account
```bash
php bin/console app:create-admin
```
Username: admin / Password: admin123

### Step 8 — Start the Server
```bash
symfony server:start
```

### Step 9 — Open the App
- Admin login: http://127.0.0.1:8000/login
- Admin dashboard: http://127.0.0.1:8000/admin/dashboard

---

## 📄 CSV Format

Each row: Question, CorrectAnswer, WrongOption1, WrongOption2...

```csv
What is the capital of France?,Paris,London,Berlin,Madrid
How many days in a week?,7,5,6,8
What color is the sky?,Blue,Red,Green,Yellow
```

---

## 🔗 Routes

| URL | Who | What |
|---|---|---|
| `/login` | Admin | Login page |
| `/logout` | Admin | Logout |
| `/admin/dashboard` | Admin | View all surveys, upload CSV |
| `/admin/results/{id}` | Admin | View responses for a survey |
| `/admin/download/{id}` | Admin | Download responses as CSV |
| `/admin/toggle/{id}` | Admin | Turn survey ON or OFF |
| `/survey/{token}` | User | Answer the survey |
| `/survey/{token}/submit` | User | Submit answers |

---

## ➕ How to Add a New Survey

1. Prepare a CSV file in the format: Question, CorrectAnswer, WrongOption1, WrongOption2
2. Log in to admin dashboard: http://127.0.0.1:8000/admin/dashboard
3. Enter a survey name and choose the CSV file
4. Click Upload & Create
5. The new survey appears in the list with its unique URL
6. Share the URL with participants


---

## 👤 Admin Credentials (Development)

| Field | Value |
|---|---|
| Username | admin |
| Password | admin123 |
