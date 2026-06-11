# Implement Activity Log & Audit Trail

Implement activity logging (audit trail) on the `User` model and create a read-only Filament resource `ActivityResource` for monitoring logs, accessible only by Super Admins.

## Proposed Changes

We will modify/create the following files in the project:

### 1. User Model Audit Trail
- **[MODIFY] [User.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Models/User.php)**:
  - Import `Spatie\Activitylog\Traits\LogsActivity` and `Spatie\Activitylog\LogOptions`.
  - Use the `LogsActivity` trait.
  - Implement `getActivitylogOptions(): LogOptions` with logFillable, logOnlyDirty, and dontSubmitEmptyLogs options.

### 2. Filament Activity Resource
- **[NEW] [ActivityResource.php](file:///wsl.localhost/Ubuntu-24.04/home/ediwe/sig/app/Filament/Resources/ActivityResource.php)**:
  - Create the `ActivityResource` using `Spatie\Activitylog\Models\Activity`.
  - Restrict access in `canAccess()` to the `super_admin` role.
  - Remove standard create, edit, and delete permissions (`canCreate()`, `canEdit()`, `canDelete()`, etc. returning `false`).
  - Table Schema:
    - `log_name` (TextColumn, badge)
    - `description` (TextColumn, search)
    - `subject_type` (TextColumn, label: 'Model', custom format removing `App\Models\`)
    - `causer.name` (TextColumn, label: 'User')
    - `created_at` (TextColumn, dateTime, sortable)

---

## Verification Plan

### Automated Tests
- Perform tinker command to create a user and check if activity log record is created in the `activity_log` table.
- Verify `User` model methods and `ActivityResource` compilation.

### Manual Verification
- Boot up development server.
- Log in as Super Admin (`admin@sig.test`).
- Go to `/admin/activities` and verify the Audit Trail list loads and correctly reflects changes.
