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
2. **Customer scans a table QR code.** The QR opens the customer menu, identifies the table, and starts a temporary guest session. Customers do not need an account.
3. **Customer places an order.** The order starts as `pending`. Customers can place more orders during the visit using the same open page.
5. **Waiter handles the request.** The waiter confirms or cancels a pending order.
6. **Kitchen prepares the food.** The kitchen moves a confirmed order to `preparing`, then to `ready`.
7. **Waiter serves it.** The waiter changes a ready order to `served`.
8. **Customer requests one table bill.** They can request it once all unpaid orders at the table have been served.
9. **Staff records payment.** A waiter, cashier, manager, or admin processes payment for the table bill. When all open orders are paid, the app closes the temporary guest session and marks the table available. If guests leave without ordering, they can finish the visit from the customer page. Staff can also clear the table manually if the customer closes the page without finishing.
10. **Customer leaves feedback.** After payment, the customer can rate the restaurant and leave a comment. Admins and managers can review feedback.

Each table can have one active QR guest session at a time. Starting a visit marks the table occupied and closes QR access for additional visitors. Its private token expires after two hours. Polling existing order statuses extends the session when it is close to expiry, and resuming in the same browser tab resets the window. Completing payment or finishing a no-order visit closes the session. The old URL stays locked until staff enables QR ordering for the next guest from the Tables page. The printed QR code stays the same, but copied links cannot start visits while QR access is closed. The customer and staff screens refresh automatically while open, so order and table changes usually appear within five seconds. This is periodic API refresh, not WebSocket push.

Table statuses are **available** (shown as Empty), **occupied** (an active QR visit or open order), and **reserved** (staff is holding the table for a booking; booking times are not tracked). After a QR visit, staff opens the table details and chooses **Enable QR for next guest**. The API only allows this for an available table with no active visit or open orders.

The dashboard's daily revenue counts payments processed today in the restaurant's configured timezone, minus change given for cash payments.

For table bills, the customer chooses cash, card, or QRIS. Staff cannot change that choice. For cash, the waiter enters the amount received and the app calculates change. For card or QRIS, the waiter completes payment on the restaurant's terminal/QR and confirms it in the app; the bill total is recorded automatically. Automatic card charging requires a payment gateway and merchant credentials, which are not configured here.

## Roles

- **Admin:** Manage staff accounts and all restaurant settings.
- **Manager:** Manage menu, categories, tables, dashboard, and feedback.
- **Waiter:** Open tables for seated guests, confirm or cancel orders, serve ready orders, and process payments.
- **Kitchen:** Start preparing confirmed orders and mark them ready.
- **Cashier:** View orders and process payments.

The backend checks access as well as the frontend hiding pages. Customer QR ordering uses a temporary table-scoped token that expires after two hours and is closed after payment. Staff must re-enable the table QR before another guest can use the same printed code.

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

API routes start with `/api/v1`. Open `restaurant-api/bruno/` in Bruno and select an environment. The local environment uses `http://127.0.0.1:8000`; authenticated requests need a staff bearer token. For the QR workflow, run **Start QR Guest Session** before creating an order, requesting a bill, or polling order statuses. It saves the temporary token to the active Bruno environment.


## php artisan serve --host=0.0.0.0
## npm run dev -- --host 0.0.0.0
