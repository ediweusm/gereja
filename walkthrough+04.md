# Eloquent Models & Relations Setup Walkthrough: Core SIG Modules

We have successfully created and configured all 10 Eloquent model files under the `app/Models` directory, satisfying all of your specifications (guarded id, HasFactory, explicit return types on relations, scopes, casts, and accessors).

---

## 1. Created & Updated Model Classes

1. **`Rayon.php`**: Includes `families()` `HasMany` relation.
2. **`DataDictionary.php`**: Includes `is_active` boolean cast, and query scopes `active()` and `category($category)`.
3. **`Family.php`**: Includes `rayon()`, `houseCategory()`, and `houseStatus()` `BelongsTo` relations, plus `members()` `HasMany` relation.
4. **`Member.php`**: Includes personal data/sacramental fields, date/boolean/decimal casts, `fullName` accessor, and 7 `BelongsTo` relations + 2 `HasMany` relations.
5. **`MemberMutation.php`**: Includes `member()`, `oldRayon()`, and `newRayon()` `BelongsTo` relations and `mutation_date` cast.
6. **`Account.php`**: Overwritten to follow strict settings. Includes self-referencing `parent()` and `children()` relations, `journalItems()` relation, `active()` scope, `is_active` cast, and `fullName` accessor (`code - name`).
7. **`Journal.php`**: Includes `createdBy()`, `items()`, and `contributions()` relations, transaction casts, and a booted hook that auto-generates `transaction_number` as `JRN-YYYYMM-XXXX`.
8. **`JournalItem.php`**: Includes `journal()` and `account()` relations and decimal casts.
9. **`MemberContribution.php`**: Includes `journal()`, `member()`, and `contributionType()` relations and decimal amount cast.
10. **`AccountMonthlyBalance.php`**: Includes `account()` relation and decimal balances casts.

---

## 2. Verification Results

We wrote and executed a verification script to instantiate all model classes and ensure that their relationship definitions return valid Eloquent Relationship instances.

### Relation Types Check
All model relationships returned the correct relationship instances (`HasMany`, `BelongsTo`) and scopes returned `Eloquent\Builder` instances:
```
--- Verifying Eloquent Models ---
Instantiated: App\Models\Rayon - OK
Instantiated: App\Models\DataDictionary - OK
Instantiated: App\Models\Family - OK
...

--- Verifying Relationships ---
Rayon -> families(): HasMany - OK
DataDictionary scopes: active() and category(): Builder - OK
Family relations: rayon(), houseCategory(), houseStatus(), members() - OK
Member relations: family(), familyPosition(), maritalStatus(), education(), occupation(), churchRole(), membershipStatus(), mutations(), contributions() - OK
...
--- All Model Validations Passed! ---
```

### Journal `transaction_number` Auto-Generation Check
We verified that when a journal is created, the observer automatically increments the sequence number `XXXX` per month:
```bash
# First journal of June 2026
$ php artisan tinker --execute="print_r(App\Models\Journal::create(['transaction_date' => now(), 'description' => 'Test', 'created_by' => 1])->toArray());"
Array
(
    [transaction_date] => 2026-06-06T00:00:00.000000Z
    [description] => Test
    [created_by] => 1
    [transaction_number] => JRN-202606-0001
    ...
)

# Second journal of June 2026 (increment check)
$ php artisan tinker --execute="print_r(App\Models\Journal::create(['transaction_date' => now(), 'description' => 'Test 2', 'created_by' => 1])->toArray());"
Array
(
    [transaction_date] => 2026-06-06T00:00:00.000000Z
    [description] => Test 2
    [created_by] => 1
    [transaction_number] => JRN-202606-0002
    ...
)
```
The generator automatically resolves the correct year-month block and increments sequence number `0001` -> `0002` correctly.
