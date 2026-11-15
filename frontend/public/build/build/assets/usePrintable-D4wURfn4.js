import{T as q,U as Q,v as n,S as Y}from"./index.1794682228677.DG0c1fSM.js";function J(){const H=q(),t=e=>String(e??"").replace(/[&<>"']/g,o=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"})[o]),f=e=>(Number(e)||0).toFixed(2),F=e=>Array.isArray(e)?e.length?e.map(o=>typeof o=="object"?o.name??o.title??"":o).join(", "):"":typeof e=="object"&&e!==null?e.name??e.title??"":e??"",O={portrait:{width:210,height:297},landscape:{width:297,height:210}},A=8,S=({title:e="",contentHtml:o="",orientation:a="portrait",signatures:g=[],logo:c=(m=>(m=H.company)==null?void 0:m.logo_url)()||"",extraCss:i="",logoLeft:l=!1,footerNoteHtml:r=""})=>{const b=O[a]??O.portrait,d=12,x=Math.round(b.width/25.4*96),p=window.open("","_blank");if(!p)return;const y=new URL(Q,window.location.origin).href,h=H.company??{},w=[h.phone?`${t(n("Phone:"))} ${t(h.phone)}`:"",h.email?`${t(n("Email:"))} ${t(h.email)}`:""].filter(Boolean).join(" &nbsp;|&nbsp; "),u=new Date().toLocaleString(Y(),{dateStyle:"medium",timeStyle:"short"}),T=`
            <div class="side-box print-date" ${l?'style="text-align:right"':""}>
                <strong>${t(n("Print Date:"))}</strong><br>${t(u)}
            </div>
        `,_=`
            <div class="side-box logo-box" ${l?'style="text-align:left"':""}>
                ${c?`<img src="${t(c)}" onerror="this.onerror=null;this.src='${y}'" />`:""}
            </div>
        `,j=`
            <div class="header">
                ${l?_:T}
                <div class="title-box">
                    ${h.company_name?`<h5>${t(h.company_name)}</h5>`:""}
                    ${h.address?`<p>${t(h.address)}</p>`:""}
                    ${w?`<p>${w}</p>`:""}
                </div>
                ${l?T:_}
            </div>
        `,v=h.company_name||"",k=Math.max(28,Math.min(110,Math.round(x*1.1/((v.length||1)*.6)))),s=v?`<div class="watermark"><span>${t(v)}</span></div>`:"",D=`
            <div class="print-footer">
                ${g.length?`
                <div class="signatures">
                    ${g.map(I=>`
                        <div class="sign">
                            <div class="line"></div>
                            <p>${t(n(I))}</p>
                        </div>
                    `).join("")}
                </div>
            `:""}
                ${r||`<p class="system-note">${t(n("This report is computer system generated, and authentic without signature"))}</p>`}
            </div>
        `,E=`
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=${x}">
            <title>${t(e)}</title>
            <style>
                @page {
                    size: A4 ${a};
                    margin: ${d}mm;
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
                    width: ${b.width}mm;
                    min-height: ${b.height}mm;
                    margin: 0 auto;
                    padding: ${d}mm;
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
                        min-height: ${b.height-d*2}mm;
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
                    font-size: ${k}px;
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

                ${i}
            </style>
        </head>

        <body>

        ${s}

        <div class="page-content">
        ${j}
        ${e?`<h3 class="doc-title">${t(e)}</h3>`:""}

        ${o}

        ${D}
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
        `;p.document.open(),p.document.write(E),p.document.close()},L=({title:e="",columns:o=[],items:a=[],totalRow:g=null,logo:c=(l=>(l=H.company)==null?void 0:l.logo_url)()||"",orientation:i=o.length>=A?"landscape":"portrait"})=>{if(!Array.isArray(a)||!a.length||!o.length)return;const r=`
            <thead>
                <tr>
                    ${o.map(d=>`<th style="text-align:${d.align||"left"}">${d.label}</th>`).join("")}
                </tr>
            </thead>
        `,m=`
            <tbody>
                ${a.map((d,x)=>`
                    <tr>
                        ${o.map(p=>`
                            <td style="text-align:${p.align||"left"}">
                                ${p.key==="index"?x+1:p.format?p.format(d[p.key],d):F(d[p.key])}
                            </td>
                        `).join("")}
                    </tr>
                `).join("")}
            </tbody>
        `,b=g?`
                <tfoot>
                    <tr>
                        ${o.map(d=>`<th style="text-align:${d.align||"left"}">${g[d.key]??""}</th>`).join("")}
                    </tr>
                </tfoot>
            `:"";S({title:e,logo:c,orientation:i,contentHtml:`<table>${r}${m}${b}</table>`})},P=["","One","Two","Three","Four","Five","Six","Seven","Eight","Nine","Ten","Eleven","Twelve","Thirteen","Fourteen","Fifteen","Sixteen","Seventeen","Eighteen","Nineteen"],R=["","","Twenty","Thirty","Forty","Fifty","Sixty","Seventy","Eighty","Ninety"],z=e=>{const o=[];return e>=100&&(o.push(`${P[Math.floor(e/100)]} Hundred`),e%=100),e>=20?o.push(R[Math.floor(e/10)]+(e%10?` ${P[e%10]}`:"")):e>0&&o.push(P[e]),o.join(" ")},B=e=>{const o=Math.round((Number(e)||0)*100)/100;let a=Math.floor(o);const g=Math.round((o-a)*100);if(a===0&&g===0)return"Zero Taka Only";const c=[];for(const[r,m]of[[1e7,"Crore"],[1e5,"Lakh"],[1e3,"Thousand"]])a>=r&&(c.push(`${z(Math.floor(a/r))} ${m}`),a%=r);a>0&&c.push(z(a));const i=c.length?`${c.join(" ")} Taka`:"",l=g?`${z(g)} Paisa`:"";return`${[i,l].filter(Boolean).join(" and ")} Only`},C=`
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
    `,U=({title:e,partyName:o,partyAddress:a,billNo:g,reference:c,subject:i,items:l,totals:r,previousAdvance:m,notes:b,signatures:d})=>{const x=H.company??{},p=Number(r.total_amount)||0,y=Number(r.discount_amount)||0,h=Number(r.vat_amount)||0,w=p-y,u=h>0&&w>0?` (${Number((h/w*100).toFixed(2))}%)`:"",T=(Number(m==null?void 0:m.amount)||0)+(Number(r.paid_amount)||0),_=Number(r.due_amount)||0,j=`
            <div class="bill-info">
                <p><strong>${t(n("Bill No:"))}</strong> ${t(g)}</p>
                <p style="margin-top:10px"><strong>${t(n("To"))}</strong></p>
                <p class="to-name">${t(o)}</p>
                ${a?`<p>${t(a)}</p>`:""}
                ${c?`<p><strong>${t(n("PO:"))}</strong> ${t(c)}</p>`:""}
                ${i?`<p><strong>${t(n("Sub:"))}</strong> ${t(i)}</p>`:""}
            </div>
        `,v=Math.max(0,6-l.length),k=`
            <table class="bill-items">
                <thead>
                    <tr>
                        <th style="width:6%">${t(n("SL"))}</th>
                        <th>${t(n("Description of Item"))}</th>
                        <th style="width:9%">${t(n("Unit"))}</th>
                        <th style="width:9%">${t(n("Qty"))}</th>
                        <th style="width:14%">${t(n("Unit Rate"))}</th>
                        <th style="width:16%">${t(n("Sub Total"))}</th>
                    </tr>
                </thead>
                <tbody>
                    ${l.map(($,M)=>`
                        <tr>
                            <td style="text-align:center">${M+1}</td>
                            <td>
                                ${t($.item_name)}
                                ${$.description?`<br><small>${t($.description)}</small>`:""}
                            </td>
                            <td style="text-align:center">${t($.unit)}</td>
                            <td style="text-align:right">${t($.qty)}</td>
                            <td style="text-align:right">${f($.unit_price)}</td>
                            <td style="text-align:right">${f((Number($.qty)||0)*(Number($.unit_price)||0))}</td>
                        </tr>
                    `).join("")}
                    ${Array.from({length:v},()=>'<tr class="bill-filler"><td></td><td></td><td></td><td></td><td></td><td></td></tr>').join("")}
                </tbody>
            </table>
        `,N=`
            <table class="bill-totals">
                ${[["Total",p],...y>0?[["Discount",y]]:[],["VAT",h,"",u],["Sub Total",r.net_total,"strong"],["Advance Paid",T],["Net Payable",_,"strong"]].map(([$,M,V="",W=""])=>`
                    <tr class="${V}"><th>${t(n($)+W)}</th><td>${f(M)}</td></tr>
                `).join("")}
            </table>
        `,D=`<p class="bill-words"><strong>${t(n("In word:"))}</strong> ${t(B(_))}</p>`,E=b?`<p><strong>${t(n("Notes:"))}</strong> ${t(b)}</p>`:"",I=`
            <div class="footer-box">
                <p>${t(n("This is a computer generated bill."))}</p>
                <div class="contacts">
                    <span>${t(n("Email:"))} ${t(x.email)}</span>
                    <span>${t(n("Contact No:"))} ${t(x.phone)}</span>
                </div>
            </div>
        `;S({signatures:d,logoLeft:!0,extraCss:C,footerNoteHtml:I,contentHtml:`<h3 class="bill-title">${t(n(e))}</h3>${j}${k}${N}${D}${E}`})};return{printTable:L,printInvoice:({title:e="Invoice",partyLabel:o="",partyName:a="",meta:g=[],items:c=[],totals:i={},previousAdvance:l=null,notes:r="",signatures:m=["Received by","Authorized Signature"],variant:b="classic",partyAddress:d="",billNo:x="",reference:p="",subject:y=""})=>{if(b==="bill")return U({title:e,partyName:a,partyAddress:d,billNo:x,reference:p,subject:y,items:c,totals:i,previousAdvance:l,notes:r,signatures:m});const h=`
            <div class="invoice-info">
                <div>
                    <p><strong>${t(n(o))}</strong> ${t(a)}</p>
                </div>
                <div class="meta">
                    ${g.filter(s=>s.value).map(s=>`<p><strong>${t(n(s.label))}</strong> ${t(s.value)}</p>`).join("")}
                </div>
            </div>
        `,w=`
            <table>
                <thead>
                    <tr>
                        <th style="text-align:left">#</th>
                        <th style="text-align:left">${t(n("Item"))}</th>
                        <th style="text-align:right">${t(n("Qty"))}</th>
                        <th style="text-align:left">${t(n("Unit"))}</th>
                        <th style="text-align:right">${t(n("Unit Price"))}</th>
                        <th style="text-align:right">${t(n("Discount"))}</th>
                        <th style="text-align:right">${t(n("VAT"))}</th>
                        <th style="text-align:right">${t(n("Total"))}</th>
                    </tr>
                </thead>
                <tbody>
                    ${c.map((s,N)=>`
                        <tr>
                            <td>${N+1}</td>
                            <td>
                                ${t(s.item_name)}
                                ${s.description?`<br><small>${t(s.description)}</small>`:""}
                            </td>
                            <td style="text-align:right">${t(s.qty)}</td>
                            <td>${t(s.unit)}</td>
                            <td style="text-align:right">${f(s.unit_price)}</td>
                            <td style="text-align:right">${f(s.discount)}</td>
                            <td style="text-align:right">${f(s.vat)}</td>
                            <td style="text-align:right">${f(s.total_amount)}</td>
                        </tr>
                    `).join("")}
                </tbody>
            </table>
        `,u=Number(l==null?void 0:l.amount)||0,T=u>0?[["Previous Advance / Bill Adjustment",u],["Payable Amount",(Number(i.net_total)||0)-u]]:[],j=`
            <table class="totals">
                ${[["Total Amount",i.total_amount],...Number(i.overall_discount)?[["Overall Discount",i.overall_discount]]:[],["Discount",i.discount_amount],...Number(i.overall_vat)?[["Overall VAT",i.overall_vat]]:[],["VAT",i.vat_amount],["Net Total",i.net_total],...T,["Paid",i.paid_amount],["Due",i.due_amount]].map(([s,N])=>`
                    <tr>
                        <th>${t(n(s))}</th>
                        <td>${f(N)}</td>
                    </tr>
                `).join("")}
            </table>
        `,v=u>0&&(l!=null&&l.purpose)?`<div class="notes"><strong>${t(n("Adjustment Purpose"))}:</strong> ${t(l.purpose)}</div>`:"",k=r?`<div class="notes"><strong>${t(n("Notes:"))}</strong> ${t(r)}</div>`:"";S({title:n(e),signatures:m,contentHtml:h+w+j+v+k})},printDocument:S}}export{J as u};
