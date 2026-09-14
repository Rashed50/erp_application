export function useMainMember() {

    const printTable = (items, options = {}) => {
        if (!items || !items.length) return
        const win = window.open('', '_blank')

        let html = `
        <html>
        <head>
            <style>
                @page {
                    size: auto;
                    margin: 10mm 10mm;

                    @bottom-center {
                        content: "Page " counter(page) " of " counter(pages);
                        font-size: 12px;
                    }
                }

                html,
                body {
                    height: 100%;
                    font-family: Arial, Helvetica, sans-serif;
                    font-size: 12px;
                    color: #000;
                }

                body {
                    margin: 0;
                    padding: 0;
                    background: #fff;
                }

                .header {
                    display: flex;
                    align-items: center;
                    border-bottom: 2px solid #000;
                    padding: 15px 20px;
                }

                .logo-box {
                    width: 90px;
                    flex-shrink: 0;
                }

                .logo-box img {
                    width: 70px;
                    height: auto;
                }

                .title-box {
                    flex: 1;
                    text-align: center;
                    padding: 0 20px;
                }

                .title-box h5 {
                    margin: 0;
                    font-size: 20px;
                    font-weight: bold;
                }

                .title-box p {
                    margin: 4px 0;
                    font-size: 14px;
                }

                .report-title {
                    text-align: center;
                    margin: 15px 0 10px;
                    font-size: 18px;
                    font-weight: bold;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 10px;
                }

                table th,
                table td {
                    border: 1px solid #ccc;
                    padding: 6px 5px;
                    font-size: 12px;
                }

                table th {
                    background: #f2f2f2;
                    font-weight: bold;
                    text-align: left;
                }

                .text-center {
                    text-align: center;
                }

                thead {
                    display: table-header-group;
                }

                tr {
                    page-break-inside: avoid;
                }

                tbody tr:nth-child(even) {
                    background-color: #fafafa;
                }
            </style>
        </head>
        <body>
            <h3 style="text-align:center; margin-bottom:20px; background-color:#f0f0f0; padding:10px;">
                Retrieved Member List - Destiny Multi-purpose Co-operative Society Ltd.
            </h3>

            <table>
                <thead>
                    <tr>
                        <th>S/L</th>
                        <th>M-Id</th>
                        <th>DIN</th>
                        <th>Name</th>
                        <th>F. Name</th>
                        <th>M. Name</th>
                        <th>DOB</th>
                        <th>Nationality</th>
                        <th>DOJ</th>
                        <th>Share</th>
                        <th>Amount</th>
                        <th>Nominee Name</th>
                        <th>Relation</th>
                        <th>Address</th>
                    </tr>
                </thead>
                <tbody>
        `

        items.forEach((row, index) => {
            html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${row.member_id ?? ''}</td>
                    <td>${row.din_no ?? ''}</td>
                    <td>${row.member_name ?? row.name ?? ''}</td>
                    <td>${row.fathers_name ?? ''}</td>
                    <td>${row.mothers_name ?? ''}</td>
                    <td>${row.date_of_birth ?? ''}</td>
                    <td>${row.nationality ?? ''}</td>
                    <td>${row.date_of_joining ?? ''}</td>
                    <td>${row.no_of_share ?? ''}</td>
                    <td>${row.amount ?? ''}</td>
                    <td>${row.nominee_name ?? ''}</td>
                    <td>${row.relation ?? ''}</td>
                    <td>${row.address ?? ''}</td>
                </tr>
            `
        })


        html += `
                </tbody>
            </table>

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
