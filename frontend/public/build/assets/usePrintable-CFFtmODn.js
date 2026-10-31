import{R as z,S as I,B as o}from"./index.1792231955243.CcngIGpY.js";function D(){const $=z(),t=e=>String(e??"").replace(/[&<>"']/g,i=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"})[i]),b=e=>(Number(e)||0).toFixed(2),w=e=>Array.isArray(e)?e.length?e.map(i=>typeof i=="object"?i.name??i.title??"":i).join(", "):"":typeof e=="object"&&e!==null?e.name??e.title??"":e??"",y={portrait:{width:210,height:297},landscape:{width:297,height:210}},v=8,k=["Prepared by","Checked by","Approved by"],u=({title:e="",contentHtml:i="",orientation:p="portrait",signatures:m=k,logo:x=(l=>(l=$.company)==null?void 0:l.logo_url)()||""})=>{const d=y[p]??y.portrait,h=12,f=Math.round(d.width/25.4*96),c=window.open("","_blank");if(!c)return;const r=new URL(I,window.location.origin).href,a=$.company??{},s=[a.phone?`${t(o("Phone:"))} ${t(a.phone)}`:"",a.email?`${t(o("Email:"))} ${t(a.email)}`:""].filter(Boolean).join(" &nbsp;|&nbsp; "),n=`
            <div class="header">
                <div class="logo-box">
                    ${x?`<img src="${t(x)}" onerror="this.onerror=null;this.src='${r}'" />`:""}
                </div>
                <div class="title-box">
                    ${a.company_name?`<h5>${t(a.company_name)}</h5>`:""}
                    ${a.address?`<p>${t(a.address)}</p>`:""}
                    ${s?`<p>${s}</p>`:""}
                </div>
                <div class="logo-box"></div>
            </div>
        `,g=a.company_name||"",j=Math.max(28,Math.min(110,Math.round(f*1.1/((g.length||1)*.6)))),A=g?`<div class="watermark"><span>${t(g)}</span></div>`:"",_=`
            <div class="print-footer">
                ${m.map(H=>`
                    <div class="sign">
                        <div class="line"></div>
                        <p>${t(o(H))}</p>
                    </div>
                `).join("")}
            </div>
        `,T=`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=${f}">
            <title>${t(e)}</title>
            <style>
                @page {
                    size: A4 ${p};
                    margin: ${h}mm;
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
                    padding: ${h}mm;
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
                        min-height: ${d.height-h*2}mm;
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

        ${A}

        <div class="page-content">
        ${n}
        ${e?`<h3 class="doc-title">${t(e)}</h3>`:""}

        ${i}

        ${_}
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
        `;c.document.open(),c.document.write(T),c.document.close()};return{printTable:({title:e="",columns:i=[],items:p=[],totalRow:m=null,logo:x=(d=>(d=$.company)==null?void 0:d.logo_url)()||"",orientation:l=i.length>=v?"landscape":"portrait"})=>{if(!Array.isArray(p)||!p.length||!i.length)return;const h=`
            <thead>
                <tr>
                    ${i.map(r=>`<th style="text-align:${r.align||"left"}">${r.label}</th>`).join("")}
                </tr>
            </thead>
        `,f=`
            <tbody>
                ${p.map((r,a)=>`
                    <tr>
                        ${i.map(s=>`
                            <td style="text-align:${s.align||"left"}">
                                ${s.key==="index"?a+1:s.format?s.format(r[s.key],r):w(r[s.key])}
                            </td>
                        `).join("")}
                    </tr>
                `).join("")}
            </tbody>
        `,c=m?`
                <tfoot>
                    <tr>
                        ${i.map(r=>`<th style="text-align:${r.align||"left"}">${m[r.key]??""}</th>`).join("")}
                    </tr>
                </tfoot>
            `:"";u({title:e,logo:x,orientation:l,contentHtml:`<table>${h}${f}${c}</table>`})},printInvoice:({title:e="Invoice",partyLabel:i="",partyName:p="",meta:m=[],items:x=[],totals:l={},notes:d="",signatures:h=["Received by","Authorized Signature"]})=>{const f=`
            <div class="invoice-info">
                <div>
                    <p><strong>${t(o(i))}</strong> ${t(p)}</p>
                </div>
                <div class="meta">
                    ${m.filter(n=>n.value).map(n=>`<p><strong>${t(o(n.label))}</strong> ${t(n.value)}</p>`).join("")}
                </div>
            </div>
        `,c=`
            <table>
                <thead>
                    <tr>
                        <th style="text-align:left">#</th>
                        <th style="text-align:left">${t(o("Item"))}</th>
                        <th style="text-align:right">${t(o("Qty"))}</th>
                        <th style="text-align:right">${t(o("Unit Price"))}</th>
                        <th style="text-align:right">${t(o("Discount"))}</th>
                        <th style="text-align:right">${t(o("VAT"))}</th>
                        <th style="text-align:right">${t(o("Total"))}</th>
                    </tr>
                </thead>
                <tbody>
                    ${x.map((n,g)=>`
                        <tr>
                            <td>${g+1}</td>
                            <td>
                                ${t(n.item_name)}
                                ${n.description?`<br><small>${t(n.description)}</small>`:""}
                            </td>
                            <td style="text-align:right">${t(n.qty)}</td>
                            <td style="text-align:right">${b(n.unit_price)}</td>
                            <td style="text-align:right">${b(n.discount)}</td>
                            <td style="text-align:right">${b(n.vat)}</td>
                            <td style="text-align:right">${b(n.total_amount)}</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        `,a=`
            <table class="totals">
                ${[["Total Amount",l.total_amount],["Discount",l.discount_amount],["VAT",l.vat_amount],["Net Total",l.net_total],["Paid",l.paid_amount],["Due",l.due_amount]].map(([n,g])=>`
                    <tr>
                        <th>${t(o(n))}</th>
                        <td>${b(g)}</td>
                    </tr>
                `).join("")}
            </table>
        `,s=d?`<div class="notes"><strong>${t(o("Notes:"))}</strong> ${t(d)}</div>`:"";u({title:o(e),signatures:h,contentHtml:f+c+a+s})},printDocument:u}}export{D as u};
