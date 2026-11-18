import { imagePlaceholder } from '@/helpers/imagePlaceholder'
import { intlLocale, t } from '@/i18n'
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

    /**
     * Open a print window in the shared company layout: header (print date,
     * company name, address and contacts, logo), the company name as a background
     * watermark and a system-generated note (plus any signature lines) in the
     * footer. Every report and invoice prints through
     * this so they all share one format.
     */
    const printDocument = ({
        title = '',
        contentHtml = '',
        orientation = 'portrait',
        signatures = [],
        logo = settings.company?.logo_url || '',
        extraCss = '',
        logoLeft = false,
        footerNoteHtml = '',
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
        const printDate = new Date().toLocaleString(intlLocale(), { dateStyle: 'medium', timeStyle: 'short' })
        const printDateHtml = `
            <div class="side-box print-date" ${logoLeft ? 'style="text-align:right"' : ''}>
                <strong>${escapeHtml(t('Print Date:'))}</strong><br>${escapeHtml(printDate)}
            </div>
        `
        const logoHtml = `
            <div class="side-box logo-box" ${logoLeft ? 'style="text-align:left"' : ''}>
                ${logo ? `<img src="${escapeHtml(logo)}" onerror="this.onerror=null;this.src='${placeholderUrl}'" />` : ''}
            </div>
        `
        const headerHtml = `
            <div class="header">
                ${logoLeft ? logoHtml : printDateHtml}
                <div class="title-box">
                    ${company.company_name ? `<h5>${escapeHtml(company.company_name)}</h5>` : ''}
                    ${company.address ? `<p>${escapeHtml(company.address)}</p>` : ''}
                    ${contactLine ? `<p>${contactLine}</p>` : ''}
                </div>
                ${logoLeft ? printDateHtml : logoHtml}
            </div>
        `

        /* ---------- WATERMARK (company name, repeated on every page) ---------- */
        const watermarkText = company.company_name || ''
        // Scale the text so long names still fit diagonally across the sheet.
        const watermarkFontPx = Math.max(28, Math.min(110, Math.round((sheetWidthPx * 1.1) / ((watermarkText.length || 1) * 0.6))))
        const watermarkHtml = watermarkText
            ? `<div class="watermark"><span>${escapeHtml(watermarkText)}</span></div>`
            : ''

        /* ---------- FOOTER (optional signature lines + system-generated note) ---------- */
        const signaturesHtml = signatures.length
            ? `
                <div class="signatures">
                    ${signatures.map(label => `
                        <div class="sign">
                            <div class="line"></div>
                            <p>${escapeHtml(t(label))}</p>
                        </div>
                    `).join('')}
                </div>
            `
            : ''
        const footerHtml = `
            <div class="print-footer">
                ${signaturesHtml}
                ${footerNoteHtml || `<p class="system-note">${escapeHtml(t('This report is computer system generated, and authentic without signature'))}</p>`}
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

                .side-box {
                    width: 130px;
                }

                .print-date {
                    font-size: 11px;
                    text-align: left;
                }

                .logo-box {
                    text-align: right;
                }

                .logo-box img {
                    max-width: 90px;
                    max-height: 70px;
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
                    padding-top: 30px;
                    text-align: center;
                    font-size: 12px;
                    page-break-inside: avoid;
                }

                .print-footer .signatures {
                    display: flex;
                    justify-content: space-around;
                    padding-top: 20px;
                    margin-bottom: 15px;
                }

                .print-footer .system-note {
                    border-top: 1px solid #000;
                    padding-top: 6px;
                    margin: 0;
                    font-size: 11px;
                    font-style: italic;
                }

                .print-footer .line {
                    width: 120px;
                    border-top: 1px solid #000;
                    margin: 0 auto 5px;
                }

                ${extraCss}
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
        signatures = [],
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
                    <tr${row.__emphasis ? ' style="font-weight:bold"' : ''}>
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
            signatures,
            contentHtml: `<table>${tableHead}${tableBody}${tableFoot}</table>`,
        })
    }

    // Whole-taka amount in words, Bangladeshi grouping (lakh, crore).
    const ONES = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve',
        'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen']
    const TENS = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety']

    const belowThousand = (n) => {
        const words = []
        if (n >= 100) {
            words.push(`${ONES[Math.floor(n / 100)]} Hundred`)
            n %= 100
        }
        if (n >= 20) {
            words.push(TENS[Math.floor(n / 10)] + (n % 10 ? ` ${ONES[n % 10]}` : ''))
        } else if (n > 0) {
            words.push(ONES[n])
        }
        return words.join(' ')
    }

    const amountInWords = (value) => {
        const amount = Math.round((Number(value) || 0) * 100) / 100
        let taka = Math.floor(amount)
        const paisa = Math.round((amount - taka) * 100)
        if (taka === 0 && paisa === 0) return 'Zero Taka Only'

        const parts = []
        for (const [size, name] of [[10000000, 'Crore'], [100000, 'Lakh'], [1000, 'Thousand']]) {
            if (taka >= size) {
                parts.push(`${belowThousand(Math.floor(taka / size))} ${name}`)
                taka %= size
            }
        }
        if (taka > 0) parts.push(belowThousand(taka))

        const takaWords = parts.length ? `${parts.join(' ')} Taka` : ''
        const paisaWords = paisa ? `${belowThousand(paisa)} Paisa` : ''
        return `${[takaWords, paisaWords].filter(Boolean).join(' and ')} Only`
    }

    const BILL_CSS = `
        .bill-title { text-align: center; font-size: 18px; letter-spacing: 3px; margin: 4px 0 10px; text-transform: uppercase; }
        .bill-info p { margin: 4px 0; }
        .bill-info .to-name { font-weight: bold; }
        table.bill-items th { text-align: center; }
        table.bill-items tbody td { border-top: 0; border-bottom: 0; }
        table.bill-items tbody tr:last-child td { border-bottom: 1px solid #000; }
        .bill-filler td { height: 22px; }
        table.bill-totals { width: 52%; margin-left: auto; margin-top: 0; }
        table.bill-totals th, table.bill-totals td { border: 0; border-bottom: 1px solid #000; background: transparent; padding: 4px 5px; }
        table.bill-totals th { text-align: left; font-weight: normal; }
        table.bill-totals td { text-align: right; }
        table.bill-totals tr.strong th, table.bill-totals tr.strong td { font-weight: bold; }
        .bill-words { margin-top: 12px; }
        .footer-box { border: 1px solid #000; padding: 6px 10px; text-align: center; }
        .footer-box p { margin: 0 0 6px; font-style: italic; }
        .footer-box .contacts { display: flex; justify-content: space-between; font-style: normal; font-size: 11px; }
    `

    // The bill layout drawn on the owner's sketch: logo left, company centre,
    // print date right; BILL title; To / PO / Sub; item table; totals with the
    // amount in words; signatures; and a computer-generated note with the
    // company email and contact number.
    const printBillInvoice = ({ title, partyName, partyAddress, billNo, reference, subject, items, totals, previousAdvance, notes, signatures }) => {
        const company = settings.company ?? {}
        const gross = Number(totals.total_amount) || 0
        const discount = Number(totals.discount_amount) || 0
        const vat = Number(totals.vat_amount) || 0
        const taxable = gross - discount
        const vatPercent = vat > 0 && taxable > 0 ? ` (${Number((vat / taxable * 100).toFixed(2))}%)` : ''
        // The advance applied to the bill plus anything already paid against it.
        const advance = (Number(previousAdvance?.amount) || 0) + (Number(totals.paid_amount) || 0)
        const netPayable = Number(totals.due_amount) || 0

        const infoHtml = `
            <div class="bill-info">
                <p><strong>${escapeHtml(t('Bill No:'))}</strong> ${escapeHtml(billNo)}</p>
                <p style="margin-top:10px"><strong>${escapeHtml(t('To'))}</strong></p>
                <p class="to-name">${escapeHtml(partyName)}</p>
                ${partyAddress ? `<p>${escapeHtml(partyAddress)}</p>` : ''}
                ${reference ? `<p><strong>${escapeHtml(t('PO:'))}</strong> ${escapeHtml(reference)}</p>` : ''}
                ${subject ? `<p><strong>${escapeHtml(t('Sub:'))}</strong> ${escapeHtml(subject)}</p>` : ''}
            </div>
        `

        const fillerRows = Math.max(0, 6 - items.length)
        const itemsHtml = `
            <table class="bill-items">
                <thead>
                    <tr>
                        <th style="width:6%">${escapeHtml(t('SL'))}</th>
                        <th>${escapeHtml(t('Description of Item'))}</th>
                        <th style="width:9%">${escapeHtml(t('Unit'))}</th>
                        <th style="width:9%">${escapeHtml(t('Qty'))}</th>
                        <th style="width:14%">${escapeHtml(t('Unit Rate'))}</th>
                        <th style="width:16%">${escapeHtml(t('Sub Total'))}</th>
                    </tr>
                </thead>
                <tbody>
                    ${items.map((item, i) => `
                        <tr>
                            <td style="text-align:center">${i + 1}</td>
                            <td>
                                ${escapeHtml(item.item_name)}
                                ${item.description ? `<br><small>${escapeHtml(item.description)}</small>` : ''}
                            </td>
                            <td style="text-align:center">${escapeHtml(item.unit)}</td>
                            <td style="text-align:right">${escapeHtml(item.qty)}</td>
                            <td style="text-align:right">${formatAmount(item.unit_price)}</td>
                            <td style="text-align:right">${formatAmount((Number(item.qty) || 0) * (Number(item.unit_price) || 0))}</td>
                        </tr>
                    `).join('')}
                    ${Array.from({ length: fillerRows }, () => '<tr class="bill-filler"><td></td><td></td><td></td><td></td><td></td><td></td></tr>').join('')}
                </tbody>
            </table>
        `

        const totalRows = [
            ['Total', gross],
            ...(discount > 0 ? [['Discount', discount]] : []),
            ['VAT', vat, '', vatPercent],
            ['Sub Total', totals.net_total, 'strong'],
            ['Advance Paid', advance],
            ['Net Payable', netPayable, 'strong'],
        ]
        const totalsHtml = `
            <table class="bill-totals">
                ${totalRows.map(([label, value, kind = '', suffix = '']) => `
                    <tr class="${kind}"><th>${escapeHtml(t(label) + suffix)}</th><td>${formatAmount(value)}</td></tr>
                `).join('')}
            </table>
        `

        const wordsHtml = `<p class="bill-words"><strong>${escapeHtml(t('In word:'))}</strong> ${escapeHtml(amountInWords(netPayable))}</p>`
        const notesHtml = notes
            ? `<p><strong>${escapeHtml(t('Notes:'))}</strong> ${escapeHtml(notes)}</p>`
            : ''

        const footerNoteHtml = `
            <div class="footer-box">
                <p>${escapeHtml(t('This is a computer generated bill.'))}</p>
                <div class="contacts">
                    <span>${escapeHtml(t('Email:'))} ${escapeHtml(company.email)}</span>
                    <span>${escapeHtml(t('Contact No:'))} ${escapeHtml(company.phone)}</span>
                </div>
            </div>
        `

        printDocument({
            signatures,
            logoLeft: true,
            extraCss: BILL_CSS,
            footerNoteHtml,
            contentHtml: `<h3 class="bill-title">${escapeHtml(t(title))}</h3>${infoHtml}${itemsHtml}${totalsHtml}${wordsHtml}${notesHtml}`,
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
     *   items: Array<{ item_name: string, description?: string, qty: number, unit?: string, unit_price: number, discount: number, vat: number, total_amount: number }>,
     *   totals: { total_amount: number, overall_discount?: number, discount_amount: number, overall_vat?: number, vat_amount: number, net_total: number, paid_amount: number, due_amount: number },
     *   previousAdvance?: { amount: number, purpose?: string },
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
        previousAdvance = null,
        notes = '',
        signatures = ['Received by', 'Authorized Signature'],
        variant = 'classic',
        partyAddress = '',
        billNo = '',
        reference = '',
        subject = '',
    }) => {
        if (variant === 'bill') {
            return printBillInvoice({ title, partyName, partyAddress, billNo, reference, subject, items, totals, previousAdvance, notes, signatures })
        }

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
                        <th style="text-align:left">${escapeHtml(t('Unit'))}</th>
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
                            <td>${escapeHtml(item.unit)}</td>
                            <td style="text-align:right">${formatAmount(item.unit_price)}</td>
                            <td style="text-align:right">${formatAmount(item.discount)}</td>
                            <td style="text-align:right">${formatAmount(item.vat)}</td>
                            <td style="text-align:right">${formatAmount(item.total_amount)}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `

        // Discount and VAT are the invoice totals; the overall (invoice-level)
        // part is shown on its own line when there is one.
        // A previous advance / bill adjusted against the invoice lowers the amount payable.
        const advanceAmount = Number(previousAdvance?.amount) || 0
        const advanceRows = advanceAmount > 0
            ? [
                ['Previous Advance / Bill Adjustment', advanceAmount],
                ['Payable Amount', (Number(totals.net_total) || 0) - advanceAmount],
            ]
            : []
        const totalRows = [
            ['Total Amount', totals.total_amount],
            ...(Number(totals.overall_discount) ? [['Overall Discount', totals.overall_discount]] : []),
            ['Discount', totals.discount_amount],
            ...(Number(totals.overall_vat) ? [['Overall VAT', totals.overall_vat]] : []),
            ['VAT', totals.vat_amount],
            ['Net Total', totals.net_total],
            ...advanceRows,
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

        const advancePurposeHtml = advanceAmount > 0 && previousAdvance?.purpose
            ? `<div class="notes"><strong>${escapeHtml(t('Adjustment Purpose'))}:</strong> ${escapeHtml(previousAdvance.purpose)}</div>`
            : ''

        const notesHtml = notes
            ? `<div class="notes"><strong>${escapeHtml(t('Notes:'))}</strong> ${escapeHtml(notes)}</div>`
            : ''

        printDocument({
            title: t(title),
            signatures,
            contentHtml: infoHtml + itemsHtml + totalsHtml + advancePurposeHtml + notesHtml,
        })
    }

    return { printTable, printInvoice, printDocument }
}
