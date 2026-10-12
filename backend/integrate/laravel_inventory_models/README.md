# Laravel Inventory Eloquent Models

Place all PHP files in `app/Models`.

Existing models expected:
- `App\Models\EmployeeInfo` -> `employee_infos`, primary key `emp_auto_id`
- `App\Models\ProjectInfo` -> `project_infos`, primary key `proj_id`

If your existing model class names differ, change the relationship class names.

All inventory relationships explicitly specify foreign and owner keys where Laravel cannot infer them. The stock-changing workflow should use a service layer with `DB::transaction()` so stock snapshots, serial custody/location, employee assignments, and the inventory ledger remain consistent.
