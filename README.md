# Mini Amazon — Shoe Store
This is a full-stack e-commerce web application dedicated to shoe shopping. This project features a highly secure, scalable Laravel API on the backend and a fast, dynamic React single-page application on the frontend.

It includes user authentication, automated email verification, role-based product management (Admin, Manager, Customer), and smart product filtering.

 # Project Structure
To keep things modular and clean, the project is divided into two completely independent directories:



mini-amazon/

├── backend/ 
 Laravel 13 API (Models, Migrations, Controllers)
└── frontend/    # React SPA (Vite, React Router, Axios, Inspinia UI)

 Key Features
Complete Auth Flow: Secure registration and login using Laravel Sanctum tokens.

Email Verification: Integration with Mailtrap to send and verify secure numeric codes upon registration.

Role-Based Access Control (RBAC): * Admin: Full control over users, roles, and inventory.

Manager: Authorized to manage inventory and simple worker accounts.

Customer: Can browse products, filter by price/gender, and shop.

Smart Catalog: Product pagination paired with server-side search, gender category filters, and maximum price thresholds.

File Storage: Automated product and profile image uploads with automatic server cleanup when files are updated or deleted.

 ## Tech Stack
### Backend
Framework: Laravel

Authentication: Laravel Sanctum (Bearer Tokens)

Database: MySQL / PostgreSQL

Tools: Mailtrap (Email testing), Carbon (Date manipulations)

### Frontend
Build Tool: Vite + React

Routing: React Router DOM

HTTP Client: Axios (configured with a central interceptor for automatic Token handling)

UI Layout: Inspinia Framework (Bootstrap powered)

 Getting Started
To run this project locally, you will need two separate terminal windows open.

# 1. Backend Setup (Laravel)
Bash
#### Navigate to backend folder
cd e_commerse_api

#### Install PHP dependencies
composer install

#### Set up your environment file (.env) and configure your DB & Mailtrap credentials
cp .env.example .env

#### Generate application key
php artisan key:generate

#### Run migrations to build the database tables
php artisan migrate

####Link the storage folder so React can display product images
php artisan storage:link

#### Start the local development server
php artisan serve
Your backend will be live at: http://127.0.0.1:8000

# 2. Frontend Setup (React)
Bash
#### Navigate to frontend folder
cd ../frontend-react

##### Install JavaScript dependencies
npm install

####Install routing capabilities if not already installed
npm install react-router-dom

#### Launch the React dev server via Vite
npm run dev
Your frontend will be live at: http://localhost:5173

 # API Communication
All frontend API calls are centralized inside src/api.js. This instance automatically injects the active user's Sanctum token from localStorage into the headers of every outgoing request, ensuring seamless and secure protected route access.

JavaScript
// Example endpoints used:
POST /api/register        # Register a new user
POST /api/verify-email    # Verify registration token
POST /api/login           # Authenticate and receive token
GET  /api/products        # Fetch products (Supports ?search=, ?gender=, ?max_price=)
