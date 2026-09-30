import { imagePlaceholder } from '@/helpers/imagePlaceholder'
import { t } from '@/i18n'
import { useSettingStore } from '@/stores/settings'

export function usePrintable() {
    const settings = useSettingStore()

    // Company values are user-entered, so escape them before they go into the print HTML.
    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[char]))

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

    // Every report prints on A4, whatever the device or printer default is.
    // Wide tables turn the sheet sideways (A4 landscape) instead of shrinking
    // the text; pass `orientation` to force one.
    const A4 = {
        portrait: { width: 210, height: 297 },
        landscape: { width: 297, height: 210 },
    }
    const WIDE_TABLE_COLUMNS = 8

    const printTable = ({
        title = '',
        columns = [],
        items = [],
        logo = settings.company?.logo_url || '',
        orientation = columns.length >= WIDE_TABLE_COLUMNS ? 'landscape' : 'portrait',
    }) => {

        if (!Array.isArray(items) || !items.length || !columns.length) return

        const sheet = A4[orientation] ?? A4.portrait
        const pageMargin = 12 // mm
        // CSS px are 96 per inch; the viewport is the sheet width so phones show the whole A4 page.
        const sheetWidthPx = Math.round((sheet.width / 25.4) * 96)

        const win = window.open('', '_blank')
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
                    ${logo ? `<img src="${logo}" onerror="this.onerror=null;this.src='${placeholderUrl}'" />` : ''}
                </div>
                <div class="title-box">
                    ${company.company_name ? `<h5>${escapeHtml(company.company_name)}</h5>` : ''}
                    ${company.address ? `<p>${escapeHtml(company.address)}</p>` : ''}
                    ${contactLine ? `<p>${contactLine}</p>` : ''}
                </div>
            </div>
        `

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

        /* ---------- FOOTER ---------- */
        const footerHtml = `
            <div class="print-footer">
                <div class="sign">
                    <div class="line"></div>
                    <p>${escapeHtml(t('Prepared by'))}</p>
                </div>
                <div class="sign">
                    <div class="line"></div>
                    <p>${escapeHtml(t('Checked by'))}</p>
                </div>
                <div class="sign">
                    <div class="line"></div>
                    <p>${escapeHtml(t('Approved by'))}</p>
                </div>
            </div>
        `

        /* ---------- FINAL HTML ---------- */
        const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=${sheetWidthPx}">
            <title>${title}</title>
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

        <div class="page-content">
        ${headerHtml}
        ${title ? `<h3 style="text-align:center">${title}</h3>` : ''}

        <table>
            ${tableHead}
            ${tableBody}
        </table>

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

    return { printTable }
}
