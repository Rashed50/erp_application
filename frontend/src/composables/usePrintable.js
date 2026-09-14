export function usePrintable() {

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

    const printTable = ({
        title = '',
        columns = [],
        items = [],
        logo = '',
    }) => {

        if (!Array.isArray(items) || !items.length || !columns.length) return

        const win = window.open('', '_blank')

        /* ---------- HEADER ---------- */
        const headerHtml = `
            <div class="header">
                <div class="logo-box">
                    ${logo ? `<img src="${logo}" />` : ''}
                </div>
                <div class="title-box">
                    <h5>ডেসটিনি মাল্টিপারপাস কো-অপারেটিভ সোসাইটি লিঃ</h5>
                    <p>নিবন্ধন নং ২৩০, তারিখ: ২৩/০৪/২০০৫ খ্রিঃ</p>
                    <p>সাহারা সেন্টার (লেভেল-০৭), ৩৭/এ, কাকরাইল, ঢাকা</p>
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
                    <p>প্রস্তুতকারী</p>
                </div>
                <div class="sign">
                    <div class="line"></div>
                    <p>যাচাইকারী</p>
                </div>
                <div class="sign">
                    <div class="line"></div>
                    <p>অনুমোদনকারী</p>
                </div>
            </div>
        `

        /* ---------- FINAL HTML ---------- */
        const html = `
        <html>
        <head>
            <style>
                @page {
                    margin: 12mm;
                }

                body {
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    color: #000;
                }

                .page-content {
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
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

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }

                th, td {
                    border: 1px solid #000;
                    padding: 5px;
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
