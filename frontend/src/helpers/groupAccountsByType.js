/**
 * Groups ledger accounts by their account type (Asset, Liability, Expense, ...)
 * for <optgroup> dropdowns, keeping the order the accounts came in.
 */
export function groupAccountsByType(accounts) {
    const groups = new Map()

    for (const account of accounts) {
        const type = account.account_type || 'Other'
        if (!groups.has(type)) groups.set(type, [])
        groups.get(type).push(account)
    }

    return [...groups].map(([type, items]) => ({ type, accounts: items }))
}
