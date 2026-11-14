# Ledger Account Add: dynamic parent-child picker

Page: `/ledger-account-add` (`frontend/src/views/ledger-accounts/Add.vue`)

## Problem
The "Sub Account Of" field was one flat select. It fetched every group account
(`per_page: 500`) and filtered by type in the browser, so it was impossible to
drill into Bank -> City Bank -> its children. After choosing "Asset" it listed
the root `1000 Assets` instead of its children (Cash in Hand, Bank Accounts, ...).

## Backend
- New endpoint: `GET /api/ledger-accounts/children`
  (`ChartOfAccountController@children`, route `ledger-accounts.children`).
  - `?account_type_id=X` -> top-level accounts (`parent_id IS NULL`) of that type.
  - `?parent_id=Y` -> direct children of account Y.
  - Only active, open accounts; each row has `has_children`.
  - Permission: `ledger-accounts.create|ledger-accounts.update`.
- No schema or data changes. It uses the existing `parent_id` relation.

## Frontend
- Choosing an Account Type loads that type's top-level accounts.
- If the type has a single root group (e.g. `1000 Assets`) with children, the root
  becomes the implicit parent and the first select shows the root's children.
- Each selection adds another select with that account's children (unlimited depth).
  Changing an earlier select drops the selects below it.
- The deepest selected account becomes `parent_id`. With nothing selected,
  `parent_id` is the root. The "next account number" suggestion still runs from it.
- Only group (non-transaction) accounts are offered as parents.

## Not covered
- `ledger-account-edit` still uses its own parent select.
- No automated tests were added; not verified in a browser.
