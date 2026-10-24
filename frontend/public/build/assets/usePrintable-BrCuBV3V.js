import{T as z,U as D,v as i,S as P}from"./index.1792841390907.BjgGEAGp.js";function N(){const $=z(),t=e=>String(e??"").replace(/[&<>"']/g,o=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"})[o]),f=e=>(Number(e)||0).toFixed(2),v=e=>Array.isArray(e)?e.length?e.map(o=>typeof o=="object"?o.name??o.title??"":o).join(", "):"":typeof e=="object"&&e!==null?e.name??e.title??"":e??"",w={portrait:{width:210,height:297},landscape:{width:297,height:210}},_=8,b=({title:e="",contentHtml:o="",orientation:p="portrait",signatures:h=[],logo:m=(a=>(a=$.company)==null?void 0:a.logo_url)()||""})=>{const d=w[p]??w.portrait,g=12,x=Math.round(d.width/25.4*96),c=window.open("","_blank");if(!c)return;const s=new URL(D,window.location.origin).href,r=$.company??{},l=[r.phone?`${t(i("Phone:"))} ${t(r.phone)}`:"",r.email?`${t(i("Email:"))} ${t(r.email)}`:""].filter(Boolean).join(" &nbsp;|&nbsp; "),n=new Date().toLocaleString(P(),{dateStyle:"medium",timeStyle:"short"}),u=`
            <div class="header">
                <div class="side-box print-date">
                    <strong>${t(i("Print Date:"))}</strong><br>${t(n)}
                </div>
                <div class="title-box">
                    ${r.company_name?`<h5>${t(r.company_name)}</h5>`:""}
                    ${r.address?`<p>${t(r.address)}</p>`:""}
                    ${l?`<p>${l}</p>`:""}
                </div>
                <div class="side-box logo-box">
                    ${m?`<img src="${t(m)}" onerror="this.onerror=null;this.src='${s}'" />`:""}
                </div>
            </div>
        `,y=r.company_name||"",j=Math.max(28,Math.min(110,Math.round(x*1.1/((y.length||1)*.6)))),k=y?`<div class="watermark"><span>${t(y)}</span></div>`:"",A=`
            <div class="print-footer">
                ${h.length?`
                <div class="signatures">
                    ${h.map(H=>`
                        <div class="sign">
                            <div class="line"></div>
                            <p>${t(i(H))}</p>
                        </div>
                    `).join("")}
                </div>
            `:""}
                <p class="system-note">${t(i("This report is computer system generated, and authentic without signature"))}</p>
            </div>
        `,T=`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=${x}">
            <title>${t(e)}</title>
            <style>
                @page {
                    size: A4 ${p};
                    margin: ${g}mm;
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
                    width: ${d.width}mm;
                    min-height: ${d.height}mm;
                    margin: 0 auto;
                    padding: ${g}mm;
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
                        min-height: ${d.height-g*2}mm;
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
                    font-size: ${j}px;
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
            </style>
        </head>

        <body>

        ${k}

        <div class="page-content">
        ${u}
        ${e?`<h3 class="doc-title">${t(e)}</h3>`:""}

        ${o}

        ${A}
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
            <\/script>

        </body>
        </html>
        `;c.document.open(),c.document.write(T),c.document.close()};return{printTable:({title:e="",columns:o=[],items:p=[],totalRow:h=null,logo:m=(d=>(d=$.company)==null?void 0:d.logo_url)()||"",orientation:a=o.length>=_?"landscape":"portrait"})=>{if(!Array.isArray(p)||!p.length||!o.length)return;const g=`
            <thead>
                <tr>
                    ${o.map(s=>`<th style="text-align:${s.align||"left"}">${s.label}</th>`).join("")}
                </tr>
            </thead>
        `,x=`
            <tbody>
                ${p.map((s,r)=>`
                    <tr>
                        ${o.map(l=>`
                            <td style="text-align:${l.align||"left"}">
                                ${l.key==="index"?r+1:l.format?l.format(s[l.key],s):v(s[l.key])}
                            </td>
                        `).join("")}
                    </tr>
                `).join("")}
            </tbody>
        `,c=h?`
                <tfoot>
                    <tr>
                        ${o.map(s=>`<th style="text-align:${s.align||"left"}">${h[s.key]??""}</th>`).join("")}
                    </tr>
                </tfoot>
            `:"";b({title:e,logo:m,orientation:a,contentHtml:`<table>${g}${x}${c}</table>`})},printInvoice:({title:e="Invoice",partyLabel:o="",partyName:p="",meta:h=[],items:m=[],totals:a={},notes:d="",signatures:g=["Received by","Authorized Signature"]})=>{const x=`
            <div class="invoice-info">
                <div>
                    <p><strong>${t(i(o))}</strong> ${t(p)}</p>
                </div>
                <div class="meta">
                    ${h.filter(n=>n.value).map(n=>`<p><strong>${t(i(n.label))}</strong> ${t(n.value)}</p>`).join("")}
                </div>
            </div>
        `,c=`
            <table>
                <thead>
                    <tr>
                        <th style="text-align:left">#</th>
                        <th style="text-align:left">${t(i("Item"))}</th>
                        <th style="text-align:right">${t(i("Qty"))}</th>
                        <th style="text-align:left">${t(i("Unit"))}</th>
                        <th style="text-align:right">${t(i("Unit Price"))}</th>
                        <th style="text-align:right">${t(i("Discount"))}</th>
                        <th style="text-align:right">${t(i("VAT"))}</th>
                        <th style="text-align:right">${t(i("Total"))}</th>
                    </tr>
                </thead>
                <tbody>
                    ${m.map((n,u)=>`
                        <tr>
                            <td>${u+1}</td>
                            <td>
                                ${t(n.item_name)}
                                ${n.description?`<br><small>${t(n.description)}</small>`:""}
                            </td>
                            <td style="text-align:right">${t(n.qty)}</td>
                            <td>${t(n.unit)}</td>
                            <td style="text-align:right">${f(n.unit_price)}</td>
                            <td style="text-align:right">${f(n.discount)}</td>
                            <td style="text-align:right">${f(n.vat)}</td>
                            <td style="text-align:right">${f(n.total_amount)}</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        `,r=`
            <table class="totals">
                ${[["Total Amount",a.total_amount],...Number(a.overall_discount)?[["Overall Discount",a.overall_discount]]:[],["Discount",a.discount_amount],...Number(a.overall_vat)?[["Overall VAT",a.overall_vat]]:[],["VAT",a.vat_amount],["Net Total",a.net_total],["Paid",a.paid_amount],["Due",a.due_amount]].map(([n,u])=>`
                    <tr>
                        <th>${t(i(n))}</th>
                        <td>${f(u)}</td>
                    </tr>
                `).join("")}
            </table>
        `,l=d?`<div class="notes"><strong>${t(i("Notes:"))}</strong> ${t(d)}</div>`:"";b({title:i(e),signatures:g,contentHtml:x+c+r+l})},printDocument:b}}export{N as u};
