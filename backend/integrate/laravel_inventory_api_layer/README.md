# Inventory API Layer

## Directory structure

Copy:

- `app/Http/Requests/Inventory/*` -> `app/Http/Requests/Inventory/`
- `app/Http/Controllers/Api/Inventory/*` -> `app/Http/Controllers/Api/Inventory/`
- `routes/inventory.php` -> `routes/inventory.php`

## Registering routes

If your Laravel application loads a custom route file, include `routes/inventory.php`.

For a normal Laravel application, add this to `routes/api.php`:

```php
require base_path('routes/inventory.php');
```

Then the endpoints are under:

`/api/inventory/...`

If you prefer to keep everything in `api.php`, copy the contents of `routes/inventory.php` into `routes/api.php`.

## Authentication

The generated routes do not force a specific authentication package because your existing payroll system may already use Sanctum, Passport, JWT, or another guard.

For Sanctum, wrap the group with:

```php
Route::middleware('auth:sanctum')->group(function () {
    require base_path('routes/inventory.php');
});
```

Do not rely on the Form Request `authorize()` method for warehouse-level security. Add policies/permissions next.

## Important authentication assumption

The controllers use:

```php
$request->user()?->emp_auto_id
```

Therefore your authenticated User model must expose `emp_auto_id`, or you should change this to match your existing authentication user/employee relationship.

## API workflow

1. POST material-requests
2. POST material-requests/{id}/approve
3. POST purchase-orders
4. Create a purchase receipt in your receiving UI/process
5. POST purchase-receipts/{id}/receive
6. POST transfers
7. POST transfers/{id}/approve
8. POST transfers/{id}/dispatch
9. POST transfers/{id}/receive
10. POST employee-inventory/issue
11. POST employee-inventory/{id}/return
12. POST assets/{id}/assign
13. POST asset-assignments/{id}/return
14. POST disposals
15. POST disposals/{id}/approve
16. POST disposals/{id}/dispose

## Security

Before production, implement:
- Inventory permissions
- Warehouse access policies
- Project access policies
- Approval permissions
- Separation of requester/approver/receiver where required
- Rate limiting
- API authentication
