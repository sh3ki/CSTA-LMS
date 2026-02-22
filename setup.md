# CSTA-LMS Setup Guide

## Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- MySQL
- Git

## Steps

### 1. Clone the Repository

```bash
git clone https://github.com/sh3ki/CSTA-LMS.git
cd CSTA-LMS/csta-lms
```

### 2. Install PHP Dependencies

```bash
composer install
```

### 3. Install JS Dependencies

```bash
npm install
```

### 4. Set Up Environment File

**Windows:**
```bash
copy .env.example .env
```

**Mac/Linux:**
```bash
cp .env.example .env
```

### 5. Generate Application Key

```bash
php artisan key:generate
```

### 6. Configure the `.env` File

Open `.env` and update the database credentials:

```env
DB_DATABASE=your_db_name
DB_USERNAME=your_db_user
DB_PASSWORD=your_db_password
```

### 7. Create the Database & Run Migrations

Make sure your MySQL server is running and the database exists, then:

```bash
php artisan migrate --seed
```

### 8. Link Storage

```bash
php artisan storage:link
```

### 9. Build Frontend Assets

**For production:**
```bash
npm run build
```

**For development (hot reload):**
```bash
npm run dev
```

### 10. Start the Development Server

```bash
php artisan serve
```

The app will be available at `http://127.0.0.1:8000`.

---

> **Note:** The `.env` file is not committed to Git. Each machine requires its own locally configured `.env` file.
