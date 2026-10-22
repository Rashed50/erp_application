// src/helpers/printQuotation.js
//
// Prints a quotation on an A4 sheet in the company format:
// title, number/date row, "To" block, items table, totals, signatures, contact footer.
// In the print dialog choose "Save as PDF" to get a PDF.

// ── Fixed company details (edit here) ─────────────────────────────
const COMPANY = {
    authorizedBy: 'Md. Sohel Rana',          // printed above "Authorized Signature"
    email: 'SNENGINEERINGW@GMAIL.COM',       // check the spelling against your letterhead
    phone: '01721393543',
};

const escapeHtml = (value) =>
    String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');

// Escapes, then keeps the line breaks the user typed
const withBreaks = (value) => escapeHtml(value).replace(/\r?\n/g, '<br>');

const money = (value) =>
    Number(value || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

const qtyFormat = (value) =>
    Number(value || 0).toLocaleString('en-US', { maximumFractionDigits: 2 });

// "2026-09-22" -> "22.09.2026"
const formatDate = (value) => {
    if (!value) return '';
    const [year, month, day] = String(value).slice(0, 10).split('-');
    return `${day}.${month}.${year}`;
};

const buildHtml = (quotation) => {
    const items = quotation.items ?? [];

    const rows = items.map((item, index) => {
        const lineTotal = (Number(item.qty) || 0) * (Number(item.unit_price) || 0);
        const unit = (item.unit_short_name || item.unit_name || '').toUpperCase();

        return `
            <tr>
                <td class="c">${index + 1}</td>
                <td>
                    ${escapeHtml(item.item_name)}
                    ${item.description ? `<br>${escapeHtml(item.description)}` : ''}
                </td>
                <td class="c">${escapeHtml(unit)}</td>
                <td class="c">${qtyFormat(item.qty)}</td>
                <td class="r">${money(item.unit_price)}</td>
                <td class="r">${money(lineTotal)}</td>
            </tr>`;
    }).join('');

    const discount = Number(quotation.discount_amount) || 0;
    const vat = Number(quotation.vat_amount) || 0;

    // Discount / VAT rows appear only when they have a value
    const totalRows = [
        `<tr><td>Total Amount</td><td class="r">${money(quotation.total_amount)}</td></tr>`,
        discount > 0 ? `<tr><td>Discount</td><td class="r">- ${money(discount)}</td></tr>` : '',
        vat > 0 ? `<tr><td>VAT</td><td class="r">${money(vat)}</td></tr>` : '',
        (discount > 0 || vat > 0)
            ? `<tr class="net"><td>Net Total</td><td class="r">${money(quotation.net_total)}</td></tr>`
            : '',
    ].join('');

    const title = `Quotation ${quotation.invoice_number ?? ''}`.trim();

    return `<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>${escapeHtml(title)}</title>
<style>
    @page { size: A4; margin: 0; }
    * { box-sizing: border-box; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    html, body { margin: 0; padding: 0; }
    body { font-family: 'Times New Roman', Times, serif; font-size: 13px; color: #000; }

    /* The header/footer rows of this wrapper table repeat on every printed page
       and keep the content clear of the fixed footer. */
    table.layout { width: 100%; border-collapse: collapse; }
    table.layout > thead > tr > td,
    table.layout > tbody > tr > td,
    table.layout > tfoot > tr > td { padding: 0 16mm; }
    .header-space { height: 22mm; }   /* empty on purpose: room for the printed letterhead */
    .footer-space { height: 36mm; }

    .title { text-align: center; color: #0563c1; text-decoration: underline; font-size: 15px; margin: 0 0 16px; }
    .meta { display: flex; justify-content: space-between; font-weight: bold; margin-bottom: 14px; }
    .to p { margin: 2px 0; }
    .sub { margin: 12px 0 8px; }

    table.items { width: 100%; border-collapse: collapse; margin-top: 6px; }
    table.items th, table.items td { border: 1px solid #000; padding: 4px 6px; vertical-align: middle; }
    table.items th { text-align: center; font-weight: bold; }
    table.items tr { page-break-inside: avoid; }
    .c { text-align: center; }
    .r { text-align: right; }

    table.totals { margin: 8px 0 0 auto; border-collapse: collapse; min-width: 240px; }
    table.totals td { padding: 3px 8px; }
    table.totals tr.net td { font-weight: bold; border-top: 1px solid #000; }

    .notes { margin-top: 12px; }

    .signs { display: flex; justify-content: space-between; margin-top: 70px; page-break-inside: avoid; }
    .sign { width: 190px; text-align: center; }
    .sign .signer { height: 18px; font-weight: bold; }
    .sign .line { border-top: 1px solid #1f3a8a; padding-top: 3px; font-style: italic; font-weight: bold; color: #1f3a8a; }

    .footer { position: fixed; left: 0; right: 0; bottom: 10mm; padding: 0 16mm; }
    .footer .bar {
        border-top: 4px solid #1f5fc6; padding-top: 6px;
        display: flex; justify-content: space-between;
        font-family: Arial, Helvetica, sans-serif; font-size: 12px; font-weight: 800;
        letter-spacing: 1px; color: #1f5fc6;
    }
</style>
</head>
<body>

<table class="layout">
    <thead><tr><td><div class="header-space"></div></td></tr></thead>

    <tbody><tr><td>
        <h3 class="title">QUOTATION</h3>

        <div class="meta">
            <div>Quotation No- ${escapeHtml(quotation.invoice_number)}</div>
            <div>Date: ${escapeHtml(formatDate(quotation.issue_date))}</div>
        </div>

        <div class="to">
            <p>To</p>
            <p>${escapeHtml(quotation.customer_name)}</p>
            ${quotation.customer_address ? `<p>${withBreaks(quotation.customer_address)}</p>` : ''}
        </div>

        ${quotation.description ? `<div class="sub">Sub: ${escapeHtml(quotation.description)}</div>` : ''}

        <table class="items">
            <thead>
                <tr>
                    <th style="width: 40px;">SL</th>
                    <th>Description of Item</th>
                    <th style="width: 70px;">Unit</th>
                    <th style="width: 60px;">Qty</th>
                    <th style="width: 90px;">Unit Rate</th>
                    <th style="width: 110px;">Total Amount</th>
                </tr>
            </thead>
            <tbody>${rows}</tbody>
        </table>

        <table class="totals"><tbody>${totalRows}</tbody></table>

        ${quotation.notes ? `<div class="notes"><strong>Note:</strong> ${withBreaks(quotation.notes)}</div>` : ''}

        <div class="signs">
            <div class="sign">
                <div class="signer"></div>
                <div class="line">Receiver's Signature</div>
            </div>
            <div class="sign">
                <div class="signer">${escapeHtml(COMPANY.authorizedBy)}</div>
                <div class="line">Authorized Signature</div>
            </div>
        </div>
    </td></tr></tbody>

    <tfoot><tr><td><div class="footer-space"></div></td></tr></tfoot>
</table>

<div class="footer">
    <div class="bar">
        <span>EMAIL: ${escapeHtml(COMPANY.email)}</span>
        <span>CELL PHN NO: ${escapeHtml(COMPANY.phone)}</span>
    </div>
</div>

</body>
</html>`;
};

/**
 * Prints one quotation (the object returned by GET /api/quotations/{id}).
 * A hidden iframe is used instead of a new tab, so pop-up blockers never interfere,
 * even though the quotation is fetched asynchronously before printing.
 */
export function printQuotation(quotation) {
    const iframe = document.createElement('iframe');
    iframe.style.cssText = 'position:fixed;right:0;bottom:0;width:0;height:0;border:0;';
    document.body.appendChild(iframe);

    const frameWindow = iframe.contentWindow;
    const frameDocument = frameWindow.document;

    frameDocument.open();
    frameDocument.write(buildHtml(quotation));
    frameDocument.close();

    const cleanup = () => setTimeout(() => iframe.remove(), 500);
    frameWindow.onafterprint = cleanup;

    // Small delay so the page is laid out before the print dialog opens
    setTimeout(() => {
        frameWindow.focus();
        frameWindow.print();

        // Fallback for browsers that do not fire onafterprint
        setTimeout(() => iframe.remove(), 60000);
    }, 300);
}