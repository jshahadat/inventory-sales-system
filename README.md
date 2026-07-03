# Inventory & Sales Management System (Yii2 + MySQL)

Enterprise-style CRUD app demonstrating relational schema design, RBAC, GridView
search/filter/pagination, PDF invoice export, and MySQL optimization (indexes,
normalization, stored procedures).

## Tech stack
- Yii2 Basic Template (PHP >= 7.4)
- MySQL 8 / MariaDB (InnoDB)
- Bootstrap 5 (yii2-bootstrap5)
- mPDF (PDF invoice export)

## File map (this delivery)
This is a **drop-in overlay** for a standard `yii2-app-basic` skeleton — copy
these files into the corresponding folders of a fresh Yii2 Basic project.

```
database/schema.sql        MySQL schema: tables, FKs, indexes, 2 stored procedures
config/web.php              App config incl. RBAC (DbManager) + urlManager rules
config/db.php                Database connection
config/params.php            App-level params (company name/address for PDF header)
commands/RbacController.php  Console command: php yii rbac/init / rbac/assign
models/                      User, Category, Supplier, Customer, Product,
                              SalesInvoice, SalesInvoiceItem + *Search models
controllers/                 Product, Category, Supplier, Customer,
                              SalesInvoice, Site controllers (RBAC-protected)
views/                       GridView index pages, forms, DetailView pages,
                              dynamic line-item invoice form, mPDF template
assets/AppAsset.php          CSS/JS asset bundle
web/index.php                Front controller entry point
```

## Setup

1. **Create a Yii2 Basic project** (requires internet + Composer, run locally):
   ```bash
   composer create-project --prefer-dist yiisoft/yii2-app-basic inventory-sales
   cd inventory-sales
   composer require mpdf/mpdf yiisoft/yii2-bootstrap5
   ```

2. **Copy the files from this delivery** into the new project, overwriting
   `config/web.php`, `config/db.php`, `config/params.php`, and adding the
   `commands/`, `models/`, `controllers/`, `views/`, `assets/` files.

3. **Create the database & schema:**
   ```bash
   mysql -u root -p -e "SOURCE database/schema.sql"
   ```
   Update `config/db.php` with your real DB credentials.

4. **Generate cookie validation key** in `config/web.php`
   (`request.cookieValidationKey`) — use a long random string.

5. **Create an admin user** (via Yii console or direct SQL). Example console
   snippet — run inside `php yii shell` or a one-off script:
   ```php
   $user = new app\models\User();
   $user->username = 'admin';
   $user->email = 'admin@example.com';
   $user->setPassword('admin123');
   $user->generateAuthKey();
   $user->status = 10;
   $user->save();
   ```

6. **Initialize RBAC roles & assign admin:**
   ```bash
   php yii rbac/init
   php yii rbac/assign admin 1
   ```
   For staff accounts: `php yii rbac/assign staff <userId>`

7. **Run the dev server:**
   ```bash
   php yii serve
   ```
   Visit `http://localhost:8080`, log in with the admin account.

## RBAC summary

| Role  | Can do |
|-------|--------|
| admin | Everything: manage products/categories/suppliers, manage users, manage invoices, view reports |
| staff | View products, create/manage sales invoices, view invoices |

Permissions are checked in each controller's `behaviors()` via `AccessControl`,
e.g. `ProductController` restricts create/update/delete to `manageProduct`
(admin only), while `index`/`view` only require `viewProduct` (admin + staff).

## Database optimization notes

- **Normalization**: Product references Category/Supplier by FK (3NF — no
  repeated category/supplier text in the product table). SalesInvoice header
  and SalesInvoiceItem line items are split into two tables to avoid repeating
  group data.
- **Indexes**: unique indexes on `sku`, `invoice_no`, `username`, `email`;
  secondary indexes on FK columns and frequently filtered columns
  (`invoice_date`, `status`, product/customer `name`) to keep GridView
  filtering fast as data grows.
- **Stored procedures**:
  - `sp_create_invoice(...)` — creates an invoice + all line items and
    decrements stock in a single atomic transaction (used as a reference
    example; the PHP controller uses an equivalent ActiveRecord transaction
    for tighter Yii2 validation integration).
  - `sp_low_stock_report()` — pre-joined low-stock query for dashboards/reports.

## PDF export

`SalesInvoiceController::actionPdf()` renders `views/sales-invoice/pdf.php`
(plain HTML/CSS) through **mPDF** and streams it inline. Reachable at:
```
/sales-invoice/<id>/pdf
```
(URL rule is defined in `config/web.php`).
