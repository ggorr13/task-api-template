# 📝 Collaborative Task Management API

A robust, scalable REST API built with **Laravel 11**, designed using clean architecture principles, SOLID design patterns, and optimized for high performance.

This project was developed as part of a **Senior Backend Developer assessment**, focusing on maintainability, scalability, and real-world engineering practices.

---

# 🚀 Features

* Authentication with Laravel Sanctum
* Project & Task management
* Nested comments system
* Asynchronous notifications (Observers + Queues)
* Redis caching strategy
* Rate limiting for critical endpoints
* Fully Dockerized environment
* Clean Architecture (Layered)

---

# 🧱 Architecture Overview

This project follows a **layered architecture** to ensure separation of concerns and testability.

### Layers

#### 1. Controllers

* Handle HTTP requests & responses
* No business logic
* Delegate to Services

#### 2. Services

* Core business logic layer
* Coordinates repositories
* Handles caching, transactions, and orchestration

#### 3. Repositories

* Abstract data access layer
* Uses interfaces (Dependency Inversion)
* Easily swappable (e.g., DB → ElasticSearch)

#### 4. DTOs (Data Transfer Objects)

* Immutable data carriers
* Prevent leaking request structures into business logic

---

# 🧠 Design Patterns Used

### ✅ Repository Pattern

Decouples database logic from business logic.

👉 Why:
Allows switching data sources without changing Services.

---

### ✅ Observer Pattern

Used for:

* Notifications
* Cache invalidation

👉 Why:
Keeps side effects out of business logic.

---

### ✅ Dependency Inversion (SOLID)

Services depend on interfaces, not implementations.

👉 Why:
Improves testability and flexibility.

---

### ✅ Service Container (IoC)

Laravel container is used for binding interfaces to implementations.

---

### ✅ Rate Limiting Strategy

Critical endpoints (update/delete) are throttled.

👉 Why:
Prevents abuse and improves system stability under load.

---

# ⚖️ Trade-offs & Decisions

### ❗ Why NOT Fat Controllers / Models

* Hard to maintain
* Hard to test
* Tight coupling

---

### ❗ Why Repositories (even with Eloquent)

Trade-off:

* Adds complexity ❌
* Improves scalability & testability ✅

---

### ❗ DTOs vs Request Arrays

Trade-off:

* More boilerplate ❌
* Strong typing & safety ✅

---

### ❗ Nested Resources (Tasks → Comments)

Trade-off:

* Slight routing complexity ❌
* Better domain modeling ✅

---

# 🛠️ Tech Stack

* PHP 8.2+
* Laravel 11
* MySQL 8
* Redis (Cache, Queue, Sessions)
* Docker & Docker Compose
* Pest PHP (Testing)

---

# ⚙️ Setup Instructions

## 1. Clone repository

```bash
git clone git@github.com:ggorr13/task-api-template.git
cd task-api-template
```

---

## 2. Environment setup

```bash
cp .env.example .env
```

---

## 3. Run containers

```bash
docker-compose up -d --build
```

---

## 4. Install dependencies

```bash
docker-compose exec app composer install
```

---

## 5. Generate app key

```bash
docker-compose exec app php artisan key:generate
```

---

## 6. Run migrations & seeders

```bash
docker-compose exec app php artisan migrate --seed
```

---

## 7. Run tests

```bash
docker-compose exec app php artisan test
```

---

# 🔐 Authentication

Uses **Laravel Sanctum**

### Register

```bash
curl -X POST http://localhost/api/auth/register \
-H "Content-Type: application/json" \
-d '{
  "name": "Gor",
  "email": "gor@example.com",
  "password": "password",
  "password_confirmation": "password"
}'
```

---

### Login

```bash
curl -X POST http://localhost/api/auth/login \
-H "Content-Type: application/json" \
-d '{
  "email": "gor@example.com",
  "password": "password"
}'
```

---

# 📡 API Examples

## Create Project

```bash
curl -X POST http://localhost/api/projects \
-H "Authorization: Bearer {token}" \
-H "Content-Type: application/json" \
-d '{
  "name": "New Project"
}'
```

---

## Create Task

```bash
curl -X POST http://localhost/api/tasks \
-H "Authorization: Bearer {token}" \
-H "Content-Type: application/json" \
-d '{
  "title": "New Task",
  "project_id": 1
}'
```

---

## Add Comment

```bash
curl -X POST http://localhost/api/tasks/1/comments \
-H "Authorization: Bearer {token}" \
-H "Content-Type: application/json" \
-d '{
  "content": "This is a comment"
}'
```

---

## Get Unseen Notifications

```bash
curl -X GET http://localhost/api/notifications/unseen \
-H "Authorization: Bearer {token}"
```

---

# 🧪 Testing

```bash
docker-compose exec app php artisan test
```

Uses **Pest PHP** for modern, expressive testing.

---

# 📦 Future Improvements

* API Versioning (`/api/v1`)
* Swagger / OpenAPI documentation
* WebSockets for real-time notifications
* Advanced caching strategies (tag-based cache)

---

# 👨‍💻 Author

**Gor Tamazyan**
Senior PHP / Laravel Developer
