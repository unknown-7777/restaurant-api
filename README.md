# Restaurant Ordering System

A restaurant ordering app with a Laravel API and a Vue website. Staff manage the restaurant from staff pages. Customers scan a table QR code, order without making an account, follow their orders, request one bill for the table, and leave feedback after payment.

## Project parts

- **`restaurant-api/`** — Laravel API, database, authentication, order rules, payments, and image uploads.
- **`restaurant-frontend/`** — Vue website for customers, waiters, kitchen staff, and managers.
- **`restaurant-api/bruno/`** — Bruno collection for making API requests by hand.

## Start the project locally

### 1. Start the API

From `restaurant-api/`:

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

The API runs at `http://127.0.0.1:8000`. The example environment uses SQLite. The seed command creates demo data and staff accounts; their demo credentials are printed by the seeder. Change those passwords before real use.

### 2. Start the website

In another terminal, from `restaurant-frontend/`:

```sh
npm install
npm run dev
```

Open the address printed by Vite, usually `http://localhost:5173`. The frontend API address is configured in `restaurant-frontend/.env` (see `.env.example` if present). For phones, the public website URL used in table QR codes must be reachable from the phone, and the API must allow that website origin.

## How the order workflow works

1. **Staff prepare the restaurant.** Managers create categories, menu items, and tables. Each table has its own QR code.
2. **Customer scans a table QR code.** The QR opens the customer menu and identifies the table. Customers do not need an account.
3. **Customer places an order.** The order starts as `pending`. Customers can return to the same menu and place more orders at that table.
4. **Waiter handles the request.** The waiter confirms or cancels a pending order.
5. **Kitchen prepares the food.** The kitchen moves a confirmed order to `preparing`, then to `ready`.
6. **Waiter serves it.** The waiter changes a ready order to `served`.
7. **Customer requests one table bill.** They can request it once all unpaid orders at the table have been served.
8. **Staff records payment.** A waiter, cashier, manager, or admin processes payment for the table bill. The app records the payment and updates the orders and table status.
9. **Customer leaves feedback.** After payment, the customer can rate the restaurant and leave a comment. Admins and managers can review feedback.

The customer and staff screens refresh automatically while open, so order and table changes usually appear within five seconds. This is periodic API refresh, not WebSocket push.

## Roles

- **Admin:** Manage staff accounts and all restaurant settings.
- **Manager:** Manage menu, categories, tables, dashboard, and feedback.
- **Waiter:** Confirm or cancel orders, serve ready orders, and process payments.
- **Kitchen:** Start preparing confirmed orders and mark them ready.
- **Cashier:** View orders and process payments.

The backend checks access as well as the frontend hiding pages. Customer QR routes are public and scoped to the table QR code; customers do not sign up or log in to order.

## Where to find things

### Laravel API (`restaurant-api/`)

- **`routes/api.php`** — API URLs and which routes need staff login or a specific role.
- **`app/Http/Controllers/Api/V1/`** — handles each API action, such as creating orders, changing statuses, and processing payments.
- **`app/Http/Requests/`** — validates incoming data for orders, menu items, tables, users, and payments.
- **`app/Http/Resources/`** — shapes API responses sent to the website.
- **`app/Models/`** — database records and their relationships.
- **`app/Services/OrderService.php`** — creates orders, adds/removes items, enforces valid status changes, and keeps table occupancy in sync.
- **`app/Services/ImageService.php`** — handles image storage and processing.
- **`app/Http/Middleware/EnsureUserHasRole.php`** — checks staff role access.
- **`database/migrations/`** — database table definitions and later schema changes.
- **`database/seeders/`** — demo staff, categories, menu items, tables, and orders.
- **`bruno/`** — saved API requests and local/production environments for Bruno.

### Vue website (`restaurant-frontend/src/`)

- **`api/`** — functions that call backend endpoints.
- **`router/index.js`** — page URLs and role-based page access.
- **`stores/`** — shared login and shopping cart state.
- **`pages/customer/MenuPage.vue`** — QR menu, additional orders, order status, bill request, and feedback.
- **`pages/waiter/OrdersPage.vue`** — waiter order list, order actions, and table payment.
- **`pages/kitchen/KitchenPage.vue`** — kitchen queue and preparation status actions.
- **`pages/admin/`** — dashboard, menu, table, user, and feedback pages.
- **`components/`** — shared layout and UI pieces.
- **`utils/liveRefresh.js`** — refreshes open screens periodically and when a user returns to a browser tab.

## API and testing requests

API routes start with `/api/v1`. Open `restaurant-api/bruno/` in Bruno and select an environment. The local environment uses `http://127.0.0.1:8000`; authenticated requests need a staff bearer token. QR requests use a table QR code and do not need a customer token.
