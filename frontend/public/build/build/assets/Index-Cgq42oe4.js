import{r as pt,o as ct,O as _t,i as E,m as l,c as d,b as t,d as S,g as U,w as ut,t as e,x as b,F as P,B as I,f as _,J as L,A as x,y as q,z as j,a as Z,s as X,v as D,j as R,h as mt,p as lt,C as dt,D as vt,q as nt}from"./index.1794469316646.B28u9Ftl.js";import{S as ht}from"./sweetalert2-z6gKu1nJ.js";import{u as ft,_ as gt}from"./usePaginatedFetch-W0Zx1kfH.js";import{u as it}from"./useFetch-DB16LIpO.js";const at={authorizedBy:"Md. Sohel Rana",email:"SNENGINEERINGW@GMAIL.COM",phone:"01721393543"},C=c=>String(c??"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;"),rt=c=>C(c).replace(/\r?\n/g,"<br>"),B=c=>Number(c||0).toLocaleString("en-US",{minimumFractionDigits:2,maximumFractionDigits:2}),bt=c=>Number(c||0).toLocaleString("en-US",{maximumFractionDigits:2}),yt=c=>{if(!c)return"";const[y,v,h]=String(c).slice(0,10).split("-");return`${h}.${v}.${y}`},$t=c=>{const v=(c.items??[]).map(($,a)=>{const w=(Number($.qty)||0)*(Number($.unit_price)||0),i=($.unit_short_name||$.unit_name||"").toUpperCase();return`
            <tr>
                <td class="c">${a+1}</td>
                <td>
                    ${C($.item_name)}
                    ${$.description?`<br>${C($.description)}`:""}
                </td>
                <td class="c">${C(i)}</td>
                <td class="c">${bt($.qty)}</td>
                <td class="r">${B($.unit_price)}</td>
                <td class="r">${B(w)}</td>
            </tr>`}).join(""),h=Number(c.discount_amount)||0,A=Number(c.vat_amount)||0,O=[`<tr><td>Total Amount</td><td class="r">${B(c.total_amount)}</td></tr>`,h>0?`<tr><td>Discount</td><td class="r">- ${B(h)}</td></tr>`:"",A>0?`<tr><td>VAT</td><td class="r">${B(A)}</td></tr>`:"",h>0||A>0?`<tr class="net"><td>Net Total</td><td class="r">${B(c.net_total)}</td></tr>`:""].join(""),Q=`Quotation ${c.invoice_number??""}`.trim();return`<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>${C(Q)}</title>
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
            <div>Quotation No- ${C(c.invoice_number)}</div>
            <div>Date: ${C(yt(c.issue_date))}</div>
        </div>

        <div class="to">
            <p>To</p>
            <p>${C(c.customer_name)}</p>
            ${c.customer_address?`<p>${rt(c.customer_address)}</p>`:""}
        </div>

        ${c.description?`<div class="sub">Sub: ${C(c.description)}</div>`:""}

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
            <tbody>${v}</tbody>
        </table>

        <table class="totals"><tbody>${O}</tbody></table>

        ${c.notes?`<div class="notes"><strong>Note:</strong> ${rt(c.notes)}</div>`:""}

        <div class="signs">
            <div class="sign">
                <div class="signer"></div>
                <div class="line">Receiver's Signature</div>
            </div>
            <div class="sign">
                <div class="signer">${C(at.authorizedBy)}</div>
                <div class="line">Authorized Signature</div>
            </div>
        </div>
    </td></tr></tbody>

    <tfoot><tr><td><div class="footer-space"></div></td></tr></tfoot>
</table>

<div class="footer">
    <div class="bar">
        <span>EMAIL: ${C(at.email)}</span>
        <span>CELL PHN NO: ${C(at.phone)}</span>
    </div>
</div>

</body>
</html>`};function wt(c){const y=document.createElement("iframe");y.style.cssText="position:fixed;right:0;bottom:0;width:0;height:0;border:0;",document.body.appendChild(y);const v=y.contentWindow,h=v.document;h.open(),h.write($t(c)),h.close();const A=()=>setTimeout(()=>y.remove(),500);v.onafterprint=A,setTimeout(()=>{v.focus(),v.print(),setTimeout(()=>y.remove(),6e4)},300)}const xt={class:"row"},kt={class:"col-md-10"},Ct={class:"row"},St={class:"col-md-4"},At={class:"form-group mb-3"},Nt={for:"customer_id"},Tt={value:"",disabled:""},Ut=["value"],Dt={key:0,class:"error-msg"},Vt={class:"col-md-4"},Ft={class:"form-group mb-3"},qt={for:"quotation_for"},Pt={value:"",disabled:""},It=["value"],Et={key:0,class:"error-msg"},Mt={class:"col-md-4"},jt={class:"form-group mb-3"},Ot={for:"signature_by"},Qt={value:"",disabled:""},zt=["value"],Bt={key:0,class:"error-msg"},Lt={class:"col-md-4"},Rt={class:"form-group mb-3"},Gt={for:"invoice_number"},Ht={class:"text-muted"},Yt={key:0,class:"error-msg"},Wt={class:"col-md-4"},Xt={class:"form-group mb-3"},Zt={for:"issue_date"},Jt={key:0,class:"error-msg"},Kt={class:"col-md-12"},te={class:"form-group mb-3"},ee={for:"notes"},oe={class:"mb-3"},se={key:0,class:"error-msg mb-2"},ne={class:"table-responsive"},ae={class:"table table-bordered align-middle"},ie={style:{width:"180px"}},le={style:{width:"80px"}},de={style:{width:"110px"}},re={style:{width:"110px"}},ce={style:{width:"110px"},class:"text-end"},ue=["onUpdate:modelValue","onChange"],me={value:""},pe=["value"],_e=["onUpdate:modelValue"],ve={key:0,class:"error-msg"},he=["onUpdate:modelValue"],fe=["onUpdate:modelValue"],ge={key:0,class:"error-msg"},be=["onUpdate:modelValue"],ye={value:""},$e=["value"],we=["onUpdate:modelValue"],xe={key:0,class:"error-msg"},ke={class:"text-end fw-bold"},Ce={class:"text-center"},Se=["disabled","onClick"],Ae={class:"row align-items-start"},Ne={class:"col-md-8"},Te={class:"row"},Ue={class:"col-md-6"},De={class:"form-group mb-3"},Ve={for:"discount_amount"},Fe={key:0,class:"error-msg"},qe={class:"col-md-6"},Pe={class:"form-group mb-3"},Ie={for:"vat_amount"},Ee={key:0,class:"error-msg"},Me={class:"col-md-12"},je={class:"form-group mb-3"},Oe={for:"file"},Qe={key:0,class:"error-msg"},ze={class:"col-md-4"},Be={class:"table table-sm"},Le={class:"text-end"},Re={class:"text-end"},Ge={class:"text-end"},He={class:"text-end"},Ye={class:"fw-bold border-top"},We={class:"text-end"},Xe={class:"row mt-4"},Ze={class:"col-md-12 d-flex gap-2"},Je=5*1024*1024,Ke={__name:"Add",emits:["saved","cancel"],setup(c,{emit:y}){const v=y,h=R(!0),A=()=>{const o=new Date;return new Date(o.getTime()-o.getTimezoneOffset()*6e4).toISOString().slice(0,10)},O=[{id:1,name:"Innovative Engineering"},{id:2,name:"Green Traders"},{id:3,name:"Others"}],Q=[{id:1,name:"MD. Ehsanul Haque Dewan"},{id:2,name:"MD. Sohel Rana"}],$=()=>({product_id:"",item_name:"",description:"",qty:1,unit_id:"",unit_price:0,discount:0,vat:0}),a=pt({customer_id:"",quotation_for:"",signature_by:"",invoice_number:"",issue_date:A(),discount_amount:0,vat_amount:0,notes:"",items:[$()]}),w=R(null),i=R({}),M=R(!1),{items:H,fetchData:J}=it("/api/customers",{per_page:100}),{items:Y,fetchData:K}=it("/api/products",{per_page:500,active_status:1}),W=R([]),tt=async()=>{var o;try{const{data:s}=await Z.get("/api/asset/lookups/inventory-units");W.value=((o=s.data)==null?void 0:o.units)??[]}catch(s){console.error("Unit load error:",s),X("error",D("Failed to load units."))}},r=o=>{const s=Y.value.find(f=>f.id===o.product_id);s&&(o.item_name=s.name)},p=o=>{w.value=o.target.files[0]||null},N=()=>a.items.push($()),T=o=>{a.items.length>1&&a.items.splice(o,1)},V=o=>(Number(o.qty)||0)*(Number(o.unit_price)||0),F=mt(()=>{const o=a.items.reduce((n,g)=>n+V(g),0),s=Number(a.discount_amount)||0,f=o-s,k=Number(a.vat_amount)||0;return{totalAmount:o,discountAmount:s,subtotalAfterDiscount:f,vatAmount:k,netTotal:f+k}}),et=()=>{const o=new FormData;return o.append("customer_id",a.customer_id),o.append("concern_company_id",a.quotation_for),o.append("signature_by",a.signature_by),o.append("invoice_number",a.invoice_number),o.append("issue_date",a.issue_date),o.append("discount_amount",a.discount_amount??0),o.append("vat_amount",a.vat_amount??0),o.append("notes",a.notes??""),a.items.forEach((s,f)=>{["item_name","description","unit_id","qty","unit_price","discount","vat"].forEach(k=>{o.append(`items[${f}][${k}]`,s[k]??"")})}),w.value&&o.append("file",w.value),o},ot=async()=>{var o,s,f,k,n,g;if(!M.value){if(i.value={},F.value.discountAmount>F.value.totalAmount){i.value={discount_amount:D("The discount cannot be greater than the total amount.")};return}if(w.value&&w.value.size>Je){i.value={file:D("The attachment must not be larger than 5 MB.")};return}M.value=!0;try{const{data:m}=await Z.post("/api/quotations",et());X("success",m.message),v("saved")}catch(m){const z=((s=(o=m.response)==null?void 0:o.data)==null?void 0:s.errors)??((k=(f=m.response)==null?void 0:f.data)==null?void 0:k.data);z&&typeof z=="object"&&!Array.isArray(z)?(i.value=Object.fromEntries(Object.entries(z).map(([G,st])=>[G,Array.isArray(st)?st[0]:st])),X("error",Object.values(i.value).join(" "))):X("error",((g=(n=m.response)==null?void 0:n.data)==null?void 0:g.message)||D("Failed to save the quotation."))}finally{M.value=!1}}},u=()=>{const o=O.find(G=>String(G.id)===String(a.quotation_for)),s=H.value.find(G=>String(G.id)===String(a.customer_id));if(!o||!s)return"";const f=(o.name||"").replace(/[^A-Za-z]/g,"").toUpperCase().slice(0,2).padEnd(2,"X"),k=(s.name||"").replace(/[^A-Za-z]/g,"").toUpperCase().slice(0,3).padEnd(3,"X"),n=new Date,g=String(n.getFullYear()).slice(-2),m=String(n.getMonth()+1).padStart(2,"0"),z=String(Math.floor(Math.random()*900)+100);return`${f}-${k}-${g}${m}-${z}`};return ct(()=>{J(),K(),tt()}),_t(()=>[a.quotation_for,a.customer_id],()=>{h.value&&(a.invoice_number=u())}),(o,s)=>{const f=E("v-btn"),k=E("v-card");return l(),d("div",xt,[s[14]||(s[14]=t("div",{class:"col-md-1"},null,-1)),t("div",kt,[S(k,{style:{padding:"15px",margin:"15px 0px"}},{default:U(()=>[t("form",{onSubmit:ut(ot,["prevent"])},[t("div",Ct,[t("div",St,[t("div",At,[t("label",Nt,e(o.$t("Customer:")),1),b(t("select",{id:"customer_id",class:"form-select","onUpdate:modelValue":s[0]||(s[0]=n=>a.customer_id=n),required:""},[t("option",Tt,e(o.$t("Select a Customer")),1),(l(!0),d(P,null,I(_(H),n=>(l(),d("option",{key:n.id,value:n.id},e(n.name),9,Ut))),128))],512),[[L,a.customer_id]]),i.value.customer_id?(l(),d("div",Dt,e(i.value.customer_id),1)):x("",!0)])]),t("div",Vt,[t("div",Ft,[t("label",qt,e(o.$t("Quotation For:")),1),b(t("select",{id:"quotation_for",class:"form-select","onUpdate:modelValue":s[1]||(s[1]=n=>a.quotation_for=n),required:""},[t("option",Pt,e(o.$t("Select Company")),1),(l(),d(P,null,I(O,n=>t("option",{key:n.id,value:n.id},e(n.name),9,It)),64))],512),[[L,a.quotation_for]]),i.value.quotation_for?(l(),d("div",Et,e(i.value.quotation_for),1)):x("",!0)])]),t("div",Mt,[t("div",jt,[t("label",Ot,e(o.$t("Signature By:")),1),b(t("select",{id:"signature_by",class:"form-select","onUpdate:modelValue":s[2]||(s[2]=n=>a.signature_by=n),required:""},[t("option",Qt,e(o.$t("Select Employee")),1),(l(),d(P,null,I(Q,n=>t("option",{key:n.id,value:n.name},e(n.name),9,zt)),64))],512),[[L,a.signature_by]]),i.value.signature_by?(l(),d("div",Bt,e(i.value.signature_by),1)):x("",!0)])]),t("div",Lt,[t("div",Rt,[t("label",Gt,e(o.$t("Quotation Number")),1),b(t("input",{type:"text",id:"invoice_number",class:"form-control","onUpdate:modelValue":s[3]||(s[3]=n=>a.invoice_number=n),onInput:s[4]||(s[4]=n=>h.value=!1),required:""},null,544),[[q,a.invoice_number]]),t("small",Ht,e(h.value?o.$t("Auto-generated"):o.$t("Manual override")),1),i.value.invoice_number?(l(),d("div",Yt,e(i.value.invoice_number),1)):x("",!0)])]),t("div",Wt,[t("div",Xt,[t("label",Zt,e(o.$t("Issue Date:")),1),b(t("input",{type:"date",id:"issue_date",class:"form-control","onUpdate:modelValue":s[5]||(s[5]=n=>a.issue_date=n),required:""},null,512),[[q,a.issue_date]]),i.value.issue_date?(l(),d("div",Jt,e(i.value.issue_date),1)):x("",!0)])]),t("div",Kt,[t("div",te,[t("label",ee,e(o.$t("Notes:")),1),b(t("textarea",{id:"notes",class:"form-control",rows:"2","onUpdate:modelValue":s[6]||(s[6]=n=>a.notes=n)},null,512),[[q,a.notes]])])])]),s[12]||(s[12]=t("hr",null,null,-1)),t("h5",oe,e(o.$t("Items")),1),i.value.items?(l(),d("div",se,e(i.value.items),1)):x("",!0),t("div",ne,[t("table",ae,[t("thead",null,[t("tr",null,[t("th",ie,e(o.$t("Product")),1),t("th",null,e(o.$t("Item Name")),1),t("th",null,e(o.$t("Description")),1),t("th",le,e(o.$t("Qty")),1),t("th",de,e(o.$t("Unit")),1),t("th",re,e(o.$t("Unit Price")),1),t("th",ce,e(o.$t("Total")),1),s[10]||(s[10]=t("th",{style:{width:"50px"}},null,-1))])]),t("tbody",null,[(l(!0),d(P,null,I(a.items,(n,g)=>(l(),d("tr",{key:g},[t("td",null,[b(t("select",{class:"form-control","onUpdate:modelValue":m=>n.product_id=m,onChange:m=>r(n)},[t("option",me,e(o.$t("Custom item")),1),(l(!0),d(P,null,I(_(Y),m=>(l(),d("option",{key:m.id,value:m.id},e(m.code)+" - "+e(m.name),9,pe))),128))],40,ue),[[L,n.product_id]])]),t("td",null,[b(t("input",{type:"text",class:"form-select","onUpdate:modelValue":m=>n.item_name=m,required:""},null,8,_e),[[q,n.item_name]]),i.value[`items.${g}.item_name`]?(l(),d("div",ve,e(i.value[`items.${g}.item_name`]),1)):x("",!0)]),t("td",null,[b(t("input",{type:"text",class:"form-control","onUpdate:modelValue":m=>n.description=m},null,8,he),[[q,n.description]])]),t("td",null,[b(t("input",{type:"number",min:"0.01",step:"0.01",class:"form-control","onUpdate:modelValue":m=>n.qty=m,required:""},null,8,fe),[[q,n.qty,void 0,{number:!0}]]),i.value[`items.${g}.qty`]?(l(),d("div",ge,e(i.value[`items.${g}.qty`]),1)):x("",!0)]),t("td",null,[b(t("select",{class:"form-select","onUpdate:modelValue":m=>n.unit_id=m},[t("option",ye,e(o.$t("Select Unit")),1),(l(!0),d(P,null,I(W.value,m=>(l(),d("option",{key:m.id,value:m.id},e(m.short_name),9,$e))),128))],8,be),[[L,n.unit_id]])]),t("td",null,[b(t("input",{type:"number",min:"0",step:"0.01",class:"form-control","onUpdate:modelValue":m=>n.unit_price=m,required:""},null,8,we),[[q,n.unit_price,void 0,{number:!0}]]),i.value[`items.${g}.unit_price`]?(l(),d("div",xe,e(i.value[`items.${g}.unit_price`]),1)):x("",!0)]),t("td",ke,e(V(n).toFixed(2)),1),t("td",Ce,[t("button",{type:"button",class:"btn btn-sm btn-outline-danger",disabled:a.items.length===1,onClick:m=>T(g)},[...s[11]||(s[11]=[t("i",{class:"fa-solid fa-trash"},null,-1)])],8,Se)])]))),128))])])]),S(f,{type:"button",class:"text-none mb-4",color:"grey-lighten-3",rounded:"0",variant:"flat",onClick:N},{default:U(()=>[j(e(o.$t("+ Add Item")),1)]),_:1}),s[13]||(s[13]=t("hr",null,null,-1)),t("div",Ae,[t("div",Ne,[t("div",Te,[t("div",Ue,[t("div",De,[t("label",Ve,e(o.$t("Discount:")),1),b(t("input",{type:"number",min:"0",step:"0.01",id:"discount_amount",class:"form-control","onUpdate:modelValue":s[7]||(s[7]=n=>a.discount_amount=n)},null,512),[[q,a.discount_amount,void 0,{number:!0}]]),i.value.discount_amount?(l(),d("div",Fe,e(i.value.discount_amount),1)):x("",!0)])]),t("div",qe,[t("div",Pe,[t("label",Ie,e(o.$t("VAT:")),1),b(t("input",{type:"number",min:"0",step:"0.01",id:"vat_amount",class:"form-control","onUpdate:modelValue":s[8]||(s[8]=n=>a.vat_amount=n)},null,512),[[q,a.vat_amount,void 0,{number:!0}]]),i.value.vat_amount?(l(),d("div",Ee,e(i.value.vat_amount),1)):x("",!0)])]),t("div",Me,[t("div",je,[t("label",Oe,e(o.$t("Attachment:")),1),t("input",{type:"file",id:"file",class:"form-control",accept:".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx",onChange:p},null,32),i.value.file?(l(),d("div",Qe,e(i.value.file),1)):x("",!0)])])])]),t("div",ze,[t("table",Be,[t("tbody",null,[t("tr",null,[t("td",null,e(o.$t("Total Amount")),1),t("td",Le,e(F.value.totalAmount.toFixed(2)),1)]),t("tr",null,[t("td",null,e(o.$t("Discount")),1),t("td",Re,e(F.value.discountAmount.toFixed(2)),1)]),t("tr",null,[t("td",null,e(o.$t("Subtotal")),1),t("td",Ge,e(F.value.subtotalAfterDiscount.toFixed(2)),1)]),t("tr",null,[t("td",null,e(o.$t("VAT")),1),t("td",He,e(F.value.vatAmount.toFixed(2)),1)]),t("tr",Ye,[t("td",null,e(o.$t("Net Total")),1),t("td",We,e(F.value.netTotal.toFixed(2)),1)])])])])]),t("div",Xe,[t("div",Ze,[S(f,{type:"submit",class:"text-none text-white",color:"blue-darken-4",rounded:"0",variant:"flat",disabled:M.value,loading:M.value},{default:U(()=>[j(e(o.$t("Submit")),1)]),_:1},8,["disabled","loading"]),S(f,{type:"button",class:"text-none",color:"grey-lighten-2",rounded:"0",variant:"flat",onClick:s[9]||(s[9]=n=>o.$emit("cancel"))},{default:U(()=>[j(e(o.$t("Cancel")),1)]),_:1})])])],32)]),_:1})])])}}},to={class:"main-content-wrapper mt-4"},eo={class:"container-fluid"},oo={class:"d-flex justify-content-between align-items-center mt-3 mb-3"},so={class:"page-title mb-0"},no={class:"d-flex align-center gap-2"},ao={class:"row"},io={class:"col-md-12 mb-2"},lo={class:"row align-items-center"},ro={class:"col-md-3"},co={value:""},uo=["value"],mo={class:"col-md-5"},po={class:"search-wrapper d-flex align-center gap-2"},_o={class:"text-left"},vo={class:"text-left"},ho={class:"text-left"},fo={class:"text-left"},go={class:"text-left"},bo={class:"text-right"},yo={class:"text-center"},$o={class:"text-center"},wo={key:0},xo={colspan:"7",class:"text-center py-4"},ko={key:1},Co={colspan:"7",class:"text-center py-4"},So={class:"text-right"},Ao={class:"text-center"},No=["href","title"],To={key:1},Uo={class:"text-center"},Do={class:"table-action-menu"},Vo={class:"menu-item"},Fo=["onClick"],qo={class:"menu-item"},Po=["onClick"],Oo={__name:"Index",setup(c){const y=[{key:"list",label:"Search",title:"All Quotation",icon:"fa-search",color:"blue-darken-3"},{key:"add",label:"New",title:"Add New Quotation",icon:"fa-plus",color:"green-darken-3"}],v=R("list"),h=mt(()=>y.find(r=>r.key===v.value)??y[0]),A=r=>{v.value=r,r==="list"&&i()},O=()=>{A("list")},{items:Q,loading:$,filters:a,pagination:w,fetchData:i,changePage:M,changePerPage:H,resetFilters:J}=ft("/api/quotations",{search:"",customer_id:""}),{items:Y,fetchData:K}=it("/api/customers",{per_page:100}),W=async r=>{var p,N;try{const{data:T}=await Z.get(`/api/quotations/${r.id}`);if(!T.success)return;wt(T.data)}catch(T){nt.error(((N=(p=T.response)==null?void 0:p.data)==null?void 0:N.message)||D("Failed to load the quotation."))}},tt=async r=>{var N,T;if((await ht.fire({title:D("Are you sure?"),text:D('Delete quotation "{invoice}"?',{invoice:r.invoice_number}),icon:"warning",showCancelButton:!0,confirmButtonText:D("Yes, delete"),cancelButtonText:D("Cancel")})).isConfirmed)try{const V=await Z.delete(`/api/quotations/${r.id}`);V.data.success&&(nt.success(V.data.message),i())}catch(V){nt.error(((T=(N=V.response)==null?void 0:N.data)==null?void 0:T.message)||D("Failed to delete quotation."))}};return ct(()=>{i(),K()}),(r,p)=>{const N=E("v-btn"),T=E("v-text-field"),V=E("v-progress-linear"),F=E("v-menu"),et=E("v-table"),ot=E("v-card");return l(),d("div",to,[t("div",eo,[t("div",oo,[t("h4",so,e(r.$t(h.value.title)),1),t("div",no,[(l(),d(P,null,I(y,u=>S(N,{key:u.key,class:dt(["text-none",{"text-white":v.value===u.key}]),color:u.color,variant:v.value===u.key?"flat":"outlined",rounded:"0",onClick:o=>A(u.key)},{default:U(()=>[t("i",{class:dt(["fa-solid me-1",u.icon])},null,2),j(" "+e(r.$t(u.label)),1)]),_:2},1032,["class","color","variant","onClick"])),64))])]),v.value==="list"?(l(),lt(ot,{key:0,style:{padding:"5px","margin-top":"15px"}},{default:U(()=>[t("div",ao,[t("div",io,[t("div",lo,[p[3]||(p[3]=t("div",{class:"col-md-4"},null,-1)),t("div",ro,[b(t("select",{class:"form-select","onUpdate:modelValue":p[0]||(p[0]=u=>_(a).customer_id=u)},[t("option",co,e(r.$t("All Customers")),1),(l(!0),d(P,null,I(_(Y),u=>(l(),d("option",{key:u.id,value:u.id},e(u.name),9,uo))),128))],512),[[L,_(a).customer_id]])]),t("div",mo,[t("div",po,[S(T,{variant:"outlined",density:"compact",placeholder:r.$t("Search invoice no..."),modelValue:_(a).search,"onUpdate:modelValue":p[1]||(p[1]=u=>_(a).search=u),"hide-details":"",class:"flex-grow-1"},null,8,["placeholder","modelValue"]),S(N,{type:"button",onClick:_(i),class:"text-none text-white",color:"blue-darken-3",rounded:"0",variant:"flat","min-width":"100"},{default:U(()=>[j(e(r.$t("Search")),1)]),_:1},8,["onClick"]),S(N,{onClick:ut(_(J),["prevent"]),class:"text-none",color:"grey-lighten-3",rounded:"0",variant:"flat","min-width":"100"},{default:U(()=>[j(e(r.$t("Reload")),1)]),_:1},8,["onClick"])])])])]),S(et,{class:"custom-bordered"},{default:U(()=>[t("thead",null,[t("tr",null,[p[4]||(p[4]=t("th",{class:"text-left"},"#",-1)),t("th",_o,e(r.$t("Quotation No")),1),t("th",vo,e(r.$t("Customer")),1),t("th",ho,e(r.$t("Issue Date")),1),t("th",fo,e(r.$t("Signature By")),1),t("th",go,e(r.$t("Quotation For")),1),t("th",bo,e(r.$t("Net Total")),1),t("th",yo,e(r.$t("File")),1),t("th",$o,e(r.$t("Action")),1)])]),t("tbody",null,[_($)?(l(),d("tr",wo,[t("td",xo,[S(V,{indeterminate:"",color:"primary",size:"30"}),j(" "+e(r.$t("Loading...")),1)])])):_(Q).length?(l(!0),d(P,{key:2},I(_(Q),(u,o)=>(l(),d("tr",{key:u.id},[t("td",null,e((_(w).page-1)*_(w).perPage+o+1),1),t("td",null,e(u.invoice_number),1),t("td",null,e(u.customer_name),1),t("td",null,e(u.issue_date),1),t("td",null,e(u.signature_by),1),t("td",null,e(u.concern_company_name??"-"),1),t("td",So,e(Number(u.net_total).toFixed(2)),1),t("td",Ao,[u.file_url?(l(),d("a",{key:0,href:u.file_url,target:"_blank",rel:"noopener",title:r.$t("Open attachment")},[...p[5]||(p[5]=[t("i",{class:"fa-solid fa-paperclip"},null,-1)])],8,No)):(l(),d("span",To,"-"))]),t("td",Uo,[S(F,null,{activator:U(({props:s})=>[t("button",vt({type:"button",class:"table-action-button"},{ref_for:!0},s),[...p[6]||(p[6]=[t("i",{class:"fa-solid fa-bars"},null,-1)])],16)]),default:U(()=>[t("ul",Do,[t("li",Vo,[t("button",{type:"button",class:"menu-link",onClick:s=>W(u)},e(r.$t("Print Quotation")),9,Fo)]),t("li",qo,[t("button",{type:"button",class:"menu-link",onClick:s=>tt(u)},e(r.$t("Delete")),9,Po)])])]),_:2},1024)])]))),128)):(l(),d("tr",ko,[t("td",Co,e(r.$t("No records found.")),1)]))])]),_:1}),S(gt,{"current-page":_(w).page,"per-page":_(w).perPage,total:_(w).total,"last-page":_(w).lastPage,onPageChange:_(M),onPerPageChange:_(H)},null,8,["current-page","per-page","total","last-page","onPageChange","onPerPageChange"])])]),_:1})):v.value==="add"?(l(),lt(Ke,{key:1,onSaved:O,onCancel:p[2]||(p[2]=u=>A("list"))})):x("",!0)])])}}};export{Oo as default};
