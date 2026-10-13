import { imagePlaceholder } from '@/helpers/imagePlaceholder'
import { t } from '@/i18n'
import { useSettingStore } from '@/stores/settings'

export function usePrintable() {
    const settings = useSettingStore()

    // Company values are user-entered, so escape them before they go into the print HTML.
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[char]))

    const formatAmount = (value) => (Number(value) || 0).toFixed(2)

    // 🔹 Cell value formatter (array / object / primitive)
    const formatCellValue = (value) => {
        if (Array.isArray(value)) {
            if (!value.length) return ''

            return value
                .map(v =>
                    typeof v === 'object'
                        ? (v.name ?? v.title ?? '')
                        : v
                )
                .join(', ')
        }

        if (typeof value === 'object' && value !== null) {
            return value.name ?? value.title ?? ''
        }

        return value ?? ''
    }

    // Every print is on A4, whatever the device or printer default is.
    // Wide tables turn the sheet sideways (A4 landscape) instead of shrinking
    // the text; pass `orientation` to force one.
    const A4 = {
        portrait: { width: 210, height: 297 },
        landscape: { width: 297, height: 210 },
    }
    const WIDE_TABLE_COLUMNS = 8
    const DEFAULT_SIGNATURES = ['Prepared by', 'Checked by', 'Approved by']

    /**
     * Open a print window in the shared company layout: header (logo, name,
     * address, phone, email), the company name as a background watermark and
     * signature lines in the footer. Every report and invoice prints through
     * this so they all share one format.
     */
    const printDocument = ({
        title = '',
        contentHtml = '',
        orientation = 'portrait',
        signatures = DEFAULT_SIGNATURES,
        logo = settings.company?.logo_url || '',
    }) => {
        const sheet = A4[orientation] ?? A4.portrait
        const pageMargin = 12 // mm
        // CSS px are 96 per inch; the viewport is the sheet width so phones show the whole A4 page.
        const sheetWidthPx = Math.round((sheet.width / 25.4) * 96)

        const win = window.open('', '_blank')
        if (!win) return
        // The print window is a separate document, so the app-wide image
        // fallback doesn't reach it; the placeholder needs an absolute URL.
        const placeholderUrl = new URL(imagePlaceholder, window.location.origin).href

        /* ---------- HEADER (company info from Settings) ---------- */
        const company = settings.company ?? {}
        const contactLine = [
            company.phone ? `${escapeHtml(t('Phone:'))} ${escapeHtml(company.phone)}` : '',
            company.email ? `${escapeHtml(t('Email:'))} ${escapeHtml(company.email)}` : '',
        ].filter(Boolean).join(' &nbsp;|&nbsp; ')
        const headerHtml = `
            <div class="header">
                <div class="logo-box">
                    ${logo ? `<img src="${escapeHtml(logo)}" onerror="this.onerror=null;this.src='${placeholderUrl}'" />` : ''}
                </div>
                <div class="title-box">
                    ${company.company_name ? `<h5>${escapeHtml(company.company_name)}</h5>` : ''}
                    ${company.address ? `<p>${escapeHtml(company.address)}</p>` : ''}
                    ${contactLine ? `<p>${contactLine}</p>` : ''}
                </div>
                <div class="logo-box"></div>
            </div>
        `

        /* ---------- WATERMARK (company name, repeated on every page) ---------- */
        const watermarkText = company.company_name || ''
        // Scale the text so long names still fit diagonally across the sheet.
        const watermarkFontPx = Math.max(28, Math.min(110, Math.round((sheetWidthPx * 1.1) / ((watermarkText.length || 1) * 0.6))))
        const watermarkHtml = watermarkText
            ? `<div class="watermark"><span>${escapeHtml(watermarkText)}</span></div>`
            : ''

        /* ---------- FOOTER (signature lines) ---------- */
        const footerHtml = `
            <div class="print-footer">
                ${signatures.map(label => `
                    <div class="sign">
                        <div class="line"></div>
                        <p>${escapeHtml(t(label))}</p>
                    </div>
                `).join('')}
            </div>
        `

        /* ---------- FINAL HTML ---------- */
        const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=${sheetWidthPx}">
            <title>${escapeHtml(title)}</title>
            <style>
                @page {
                    size: A4 ${orientation};
                    margin: ${pageMargin}mm;
                }

                * {
                    box-sizing: border-box;
                }

                html {
                    -webkit-text-size-adjust: 100%;
                    text-size-adjust: 100%;
                }

                body {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    color: #000;
                    margin: 0;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* On screen the preview is an A4 sheet; in print the @page box is the sheet. */
                .page-content {
                    width: ${sheet.width}mm;
                    min-height: ${sheet.height}mm;
                    margin: 0 auto;
                    padding: ${pageMargin}mm;
                    display: flex;
                    flex-direction: column;
                }

                @media screen {
                    body {
                        background: #e9e9e9;
                    }

                    .page-content {
                        background: #fff;
                        box-shadow: 0 0 6px rgba(0, 0, 0, 0.25);
                    }
                }

                @media print {
                    .page-content {
                        width: auto;
                        min-height: ${sheet.height - pageMargin * 2}mm;
                        padding: 0;
                    }
                }

                /* A fixed element repeats on every printed page. It sits above the
                   content (faint and click-through) so shaded cells don't hide it. */
                .watermark {
                    position: fixed;
                    inset: 0;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    pointer-events: none;
                    z-index: 10;
                    overflow: hidden;
                }

                .watermark span {
                    font-size: ${watermarkFontPx}px;
                    font-weight: bold;
                    color: #000;
                    opacity: 0.07;
                    white-space: nowrap;
                    text-transform: uppercase;
                    transform: rotate(-30deg);
                }

                .header {
                    display: flex;
                    align-items: center;
                    border-bottom: 2px solid #000;
                    padding-bottom: 10px;
                    margin-bottom: 10px;
                }

                .logo-box {
                    width: 80px;
                }

                .logo-box img {
                    width: 70px;
                }

                .title-box {
                    flex: 1;
                    text-align: center;
                }

                .title-box h5 {
                    font-size: 18px;
                    margin: 0 0 6px;
                }

                .title-box p {
                    margin: 2px 0;
                }

                .doc-title {
                    text-align: center;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }

                /* Long text wraps inside its cell so the table never runs past the A4 width. */
                th, td {
                    border: 1px solid #000;
                    padding: 5px;
                    overflow-wrap: anywhere;
                }

                th {
                    background: #f2f2f2;
                    font-weight: bold;
                }

                thead {
                    display: table-header-group;
                }

                tr {
                    page-break-inside: avoid;
                }

                /* ---------- INVOICE ---------- */
                .invoice-info {
                    display: flex;
                    justify-content: space-between;
                    gap: 20px;
                }

                .invoice-info p {
                    margin: 3px 0;
                }

                .invoice-info .meta {
                    text-align: right;
                }

                table.totals {
                    width: 45%;
                    margin-left: auto;
                }

                table.totals th {
                    text-align: left;
                }

                table.totals td {
                    text-align: right;
                }

                .notes {
                    margin-top: 12px;
                }

                /* ---------- FOOTER ---------- */
                .print-footer {
                    margin-top: auto;
                    padding-top: 50px;
                    display: flex;
                    justify-content: space-around;
                    text-align: center;
                    font-size: 12px;
                    page-break-inside: avoid;
                }

                .print-footer .line {
                    width: 120px;
                    border-top: 1px solid #000;
                    margin: 0 auto 5px;
                }
            </style>
        </head>

        <body>

        ${watermarkHtml}

        <div class="page-content">
        ${headerHtml}
        ${title ? `<h3 class="doc-title">${escapeHtml(title)}</h3>` : ''}

        ${contentHtml}

        ${footerHtml}
        </div>

            <script>
                window.onload = function () {
                    window.focus();

                    setTimeout(function () {
                        window.print();
                    }, 300);

                    window.onafterprint = function () {
                        if (window.opener) {
                            window.opener.focus();
                        }

                        setTimeout(function () {
                            window.close();
                        }, 300);
                    };
                };
            </script>

        </body>
        </html>
        `

        win.document.open()
        win.document.write(html)
        win.document.close()
    }

    const printTable = ({
        title = '',
        columns = [],
        items = [],
        totalRow = null,
        logo = settings.company?.logo_url || '',
        orientation = columns.length >= WIDE_TABLE_COLUMNS ? 'landscape' : 'portrait',
    }) => {

        if (!Array.isArray(items) || !items.length || !columns.length) return

        /* ---------- TABLE HEAD ---------- */
        const tableHead = `
            <thead>
                <tr>
                    ${columns.map(col =>
                        `<th style="text-align:${col.align || 'left'}">${col.label}</th>`
                    ).join('')}
                </tr>
            </thead>
        `

        /* ---------- TABLE BODY ---------- */
        const tableBody = `
            <tbody>
                ${items.map((row, i) => `
                    <tr>
                        ${columns.map(col => `
                            <td style="text-align:${col.align || 'left'}">
                                ${
                                    col.key === 'index'
                                        ? i + 1
                                        : col.format
                                            ? col.format(row[col.key], row)
                                            : formatCellValue(row[col.key])
                                }
                            </td>
                        `).join('')}
                    </tr>
                `).join('')}
            </tbody>
        `

        /* ---------- TABLE FOOT (optional totals, keyed like the columns) ---------- */
        const tableFoot = totalRow
            ? `
                <tfoot>
                    <tr>
                        ${columns.map(col =>
                            `<th style="text-align:${col.align || 'left'}">${totalRow[col.key] ?? ''}</th>`
                        ).join('')}
                    </tr>
                </tfoot>
            `
            : ''

        printDocument({
            title,
            logo,
            orientation,
            contentHtml: `<table>${tableHead}${tableBody}${tableFoot}</table>`,
        })
    }

    /**
     * Print a sale or purchase invoice in the shared company layout.
     *
     * @param {{
     *   title?: string,
     *   partyLabel: string,
     *   partyName: string,
     *   meta?: Array<{ label: string, value: string }>,
     *   items: Array<{ item_name: string, description?: string, qty: number, unit_price: number, discount: number, vat: number, total_amount: number }>,
     *   totals: { total_amount: number, discount_amount: number, vat_amount: number, net_total: number, paid_amount: number, due_amount: number },
     *   notes?: string,
     *   signatures?: string[],
     * }} invoice
     */
    const printInvoice = ({
        title = 'Invoice',
        partyLabel = '',
        partyName = '',
        meta = [],
        items = [],
        totals = {},
        notes = '',
        signatures = ['Received by', 'Authorized Signature'],
    }) => {
        const infoHtml = `
            <div class="invoice-info">
                <div>
                    <p><strong>${escapeHtml(t(partyLabel))}</strong> ${escapeHtml(partyName)}</p>
                </div>
                <div class="meta">
                    ${meta.filter(row => row.value).map(row =>
                        `<p><strong>${escapeHtml(t(row.label))}</strong> ${escapeHtml(row.value)}</p>`
                    ).join('')}
                </div>
            </div>
        `

        const itemsHtml = `
            <table>
                <thead>
                    <tr>
                        <th style="text-align:left">#</th>
                        <th style="text-align:left">${escapeHtml(t('Item'))}</th>
                        <th style="text-align:right">${escapeHtml(t('Qty'))}</th>
                        <th style="text-align:right">${escapeHtml(t('Unit Price'))}</th>
                        <th style="text-align:right">${escapeHtml(t('Discount'))}</th>
                        <th style="text-align:right">${escapeHtml(t('VAT'))}</th>
                        <th style="text-align:right">${escapeHtml(t('Total'))}</th>
                    </tr>
                </thead>
                <tbody>
                    ${items.map((item, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td>
                                ${escapeHtml(item.item_name)}
                                ${item.description ? `<br><small>${escapeHtml(item.description)}</small>` : ''}
                            </td>
                            <td style="text-align:right">${escapeHtml(item.qty)}</td>
                            <td style="text-align:right">${formatAmount(item.unit_price)}</td>
                            <td style="text-align:right">${formatAmount(item.discount)}</td>
                            <td style="text-align:right">${formatAmount(item.vat)}</td>
                            <td style="text-align:right">${formatAmount(item.total_amount)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `

        const totalRows = [
            ['Total Amount', totals.total_amount],
            ['Discount', totals.discount_amount],
            ['VAT', totals.vat_amount],
            ['Net Total', totals.net_total],
            ['Paid', totals.paid_amount],
            ['Due', totals.due_amount],
        ]
        const totalsHtml = `
            <table class="totals">
                ${totalRows.map(([label, value]) => `
                    <tr>
                        <th>${escapeHtml(t(label))}</th>
                        <td>${formatAmount(value)}</td>
                    </tr>
                `).join('')}
            </table>
        `

        const notesHtml = notes
            ? `<div class="notes"><strong>${escapeHtml(t('Notes:'))}</strong> ${escapeHtml(notes)}</div>`
            : ''

        printDocument({
            title: t(title),
            signatures,
            contentHtml: infoHtml + itemsHtml + totalsHtml + notesHtml,
        })
    }

    return { printTable, printInvoice, printDocument }
}
