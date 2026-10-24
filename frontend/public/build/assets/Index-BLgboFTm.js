import{r as rt,o as at,i as I,m as d,c as u,b as t,d as C,g as T,w as lt,t as e,x as $,F as j,B as z,f as _,J as R,A as N,y as D,z as E,a as H,s as L,v as U,j as B,h as it,p as ot,C as st,D as ct,q as K}from"./index.1792841390907.BjgGEAGp.js";import{S as ut}from"./sweetalert2-z6gKu1nJ.js";import{u as mt,_ as pt}from"./usePaginatedFetch-DHp_u1Ot.js";import{u as et}from"./useFetch-PvydizLD.js";const tt={authorizedBy:"Md. Sohel Rana",email:"SNENGINEERINGW@GMAIL.COM",phone:"01721393543"},k=r=>String(r??"").replace(/&/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/"/g,"&quot;"),nt=r=>k(r).replace(/\r?\n/g,"<br>"),q=r=>Number(r||0).toLocaleString("en-US",{minimumFractionDigits:2,maximumFractionDigits:2}),_t=r=>Number(r||0).toLocaleString("en-US",{maximumFractionDigits:2}),ht=r=>{if(!r)return"";const[g,h,y]=String(r).slice(0,10).split("-");return`${y}.${h}.${g}`},vt=r=>{const h=(r.items??[]).map((a,w)=>{const S=(Number(a.qty)||0)*(Number(a.unit_price)||0),F=(a.unit_short_name||a.unit_name||"").toUpperCase();return`
            <tr>
                <td class="c">${w+1}</td>
                <td>
                    ${k(a.item_name)}
                    ${a.description?`<br>${k(a.description)}`:""}
                </td>
                <td class="c">${k(F)}</td>
                <td class="c">${_t(a.qty)}</td>
                <td class="r">${q(a.unit_price)}</td>
                <td class="r">${q(S)}</td>
            </tr>`}).join(""),y=Number(r.discount_amount)||0,x=Number(r.vat_amount)||0,i=[`<tr><td>Total Amount</td><td class="r">${q(r.total_amount)}</td></tr>`,y>0?`<tr><td>Discount</td><td class="r">- ${q(y)}</td></tr>`:"",x>0?`<tr><td>VAT</td><td class="r">${q(x)}</td></tr>`:"",y>0||x>0?`<tr class="net"><td>Net Total</td><td class="r">${q(r.net_total)}</td></tr>`:""].join(""),V=`Quotation ${r.invoice_number??""}`.trim();return`<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>${k(V)}</title>
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
            <div>Quotation No- ${k(r.invoice_number)}</div>
            <div>Date: ${k(ht(r.issue_date))}</div>
        </div>

        <div class="to">
            <p>To</p>
            <p>${k(r.customer_name)}</p>
            ${r.customer_address?`<p>${nt(r.customer_address)}</p>`:""}
        </div>

        ${r.description?`<div class="sub">Sub: ${k(r.description)}</div>`:""}

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
            <tbody>${h}</tbody>
        </table>

        <table class="totals"><tbody>${i}</tbody></table>

        ${r.notes?`<div class="notes"><strong>Note:</strong> ${nt(r.notes)}</div>`:""}

        <div class="signs">
            <div class="sign">
                <div class="signer"></div>
                <div class="line">Receiver's Signature</div>
            </div>
            <div class="sign">
                <div class="signer">${k(tt.authorizedBy)}</div>
                <div class="line">Authorized Signature</div>
            </div>
        </div>
    </td></tr></tbody>

    <tfoot><tr><td><div class="footer-space"></div></td></tr></tfoot>
</table>

<div class="footer">
    <div class="bar">
        <span>EMAIL: ${k(tt.email)}</span>
        <span>CELL PHN NO: ${k(tt.phone)}</span>
    </div>
</div>

</body>
</html>`};function ft(r){const g=document.createElement("iframe");g.style.cssText="position:fixed;right:0;bottom:0;width:0;height:0;border:0;",document.body.appendChild(g);const h=g.contentWindow,y=h.document;y.open(),y.write(vt(r)),y.close();const x=()=>setTimeout(()=>g.remove(),500);h.onafterprint=x,setTimeout(()=>{h.focus(),h.print(),setTimeout(()=>g.remove(),6e4)},300)}const bt={class:"row"},gt={class:"col-md-10"},yt={class:"row"},$t={class:"col-md-4"},xt={class:"form-group mb-3"},wt={for:"customer_id"},kt={value:"",disabled:""},Ct=["value"],At={key:0,class:"error-msg"},Nt={class:"col-md-4"},Tt={class:"form-group mb-3"},Vt={key:0,class:"error-msg"},St={class:"col-md-4"},Ut={class:"form-group mb-3"},Dt={for:"issue_date"},Ft={key:0,class:"error-msg"},Pt={class:"col-md-12"},It={class:"form-group mb-3"},jt={for:"notes"},zt={class:"mb-3"},Et={key:0,class:"error-msg mb-2"},qt={class:"table-responsive"},Bt={class:"table table-bordered align-middle"},Ot={style:{width:"180px"}},Mt={style:{width:"80px"}},Qt={style:{width:"110px"}},Lt={style:{width:"110px"}},Rt={style:{width:"110px"},class:"text-end"},Ht=["onUpdate:modelValue","onChange"],Gt={value:""},Wt=["value"],Yt=["onUpdate:modelValue"],Jt={key:0,class:"error-msg"},Xt=["onUpdate:modelValue"],Zt=["onUpdate:modelValue"],Kt={key:0,class:"error-msg"},te=["onUpdate:modelValue"],ee={value:""},oe=["value"],se=["onUpdate:modelValue"],ne={key:0,class:"error-msg"},ae={class:"text-end fw-bold"},le={class:"text-center"},ie=["disabled","onClick"],de={class:"row align-items-start"},re={class:"col-md-8"},ce={class:"row"},ue={class:"col-md-6"},me={class:"form-group mb-3"},pe={for:"discount_amount"},_e={key:0,class:"error-msg"},he={class:"col-md-6"},ve={class:"form-group mb-3"},fe={for:"vat_amount"},be={key:0,class:"error-msg"},ge={class:"col-md-12"},ye={class:"form-group mb-3"},$e={for:"file"},xe={key:0,class:"error-msg"},we={class:"col-md-4"},ke={class:"table table-sm"},Ce={class:"text-end"},Ae={class:"text-end"},Ne={class:"text-end"},Te={class:"text-end"},Ve={class:"fw-bold border-top"},Se={class:"text-end"},Ue={class:"row mt-4"},De={class:"col-md-12 d-flex gap-2"},Fe=5*1024*1024,Pe={__name:"Add",emits:["saved","cancel"],setup(r,{emit:g}){const h=g,y=()=>{const o=new Date;return new Date(o.getTime()-o.getTimezoneOffset()*6e4).toISOString().slice(0,10)},x=()=>({product_id:"",item_name:"",description:"",qty:1,unit_id:"",unit_price:0,discount:0,vat:0}),i=rt({customer_id:"",invoice_number:"",issue_date:y(),discount_amount:0,vat_amount:0,notes:"",items:[x()]}),V=B(null),a=B({}),w=B(!1),{items:S,fetchData:F}=et("/api/customers",{per_page:100}),{items:O,fetchData:G}=et("/api/products",{per_page:500,active_status:1}),M=B([]),W=async()=>{var o;try{const{data:s}=await H.get("/api/asset/lookups/inventory-units");M.value=((o=s.data)==null?void 0:o.units)??[]}catch(s){console.error("Unit load error:",s),L("error",U("Failed to load units."))}},Y=o=>{const s=O.value.find(b=>b.id===o.product_id);s&&(o.item_name=s.name)},J=o=>{V.value=o.target.files[0]||null},X=()=>i.items.push(x()),c=o=>{i.items.length>1&&i.items.splice(o,1)},p=o=>(Number(o.qty)||0)*(Number(o.unit_price)||0),v=it(()=>{const o=i.items.reduce((n,f)=>n+p(f),0),s=Number(i.discount_amount)||0,b=o-s,l=Number(i.vat_amount)||0;return{totalAmount:o,discountAmount:s,subtotalAfterDiscount:b,vatAmount:l,netTotal:b+l}}),A=()=>{const o=new FormData;return o.append("customer_id",i.customer_id),o.append("invoice_number",i.invoice_number),o.append("issue_date",i.issue_date),o.append("discount_amount",i.discount_amount??0),o.append("vat_amount",i.vat_amount??0),o.append("notes",i.notes??""),i.items.forEach((s,b)=>{["item_name","description","unit_id","qty","unit_price","discount","vat"].forEach(l=>{o.append(`items[${b}][${l}]`,s[l]??"")})}),V.value&&o.append("file",V.value),o},P=async()=>{var o,s,b,l,n,f;if(!w.value){if(a.value={},v.value.discountAmount>v.value.totalAmount){a.value={discount_amount:U("The discount cannot be greater than the total amount.")};return}if(V.value&&V.value.size>Fe){a.value={file:U("The attachment must not be larger than 5 MB.")};return}w.value=!0;try{const{data:m}=await H.post("/api/quotations",A());L("success",m.message),h("saved")}catch(m){const Q=((s=(o=m.response)==null?void 0:o.data)==null?void 0:s.errors)??((l=(b=m.response)==null?void 0:b.data)==null?void 0:l.data);Q&&typeof Q=="object"&&!Array.isArray(Q)?(a.value=Object.fromEntries(Object.entries(Q).map(([dt,Z])=>[dt,Array.isArray(Z)?Z[0]:Z])),L("error",Object.values(a.value).join(" "))):L("error",((f=(n=m.response)==null?void 0:n.data)==null?void 0:f.message)||U("Failed to save the quotation."))}finally{w.value=!1}}};return at(()=>{F(),G(),W()}),(o,s)=>{const b=I("v-btn"),l=I("v-card");return d(),u("div",bt,[s[12]||(s[12]=t("div",{class:"col-md-1"},null,-1)),t("div",gt,[C(l,{style:{padding:"15px",margin:"15px 0px"}},{default:T(()=>[t("form",{onSubmit:lt(P,["prevent"])},[t("div",yt,[t("div",$t,[t("div",xt,[t("label",wt,e(o.$t("Customer:")),1),$(t("select",{id:"customer_id",class:"form-select","onUpdate:modelValue":s[0]||(s[0]=n=>i.customer_id=n),required:""},[t("option",kt,e(o.$t("Select a Customer")),1),(d(!0),u(j,null,z(_(S),n=>(d(),u("option",{key:n.id,value:n.id},e(n.name),9,Ct))),128))],512),[[R,i.customer_id]]),a.value.customer_id?(d(),u("div",At,e(a.value.customer_id),1)):N("",!0)])]),t("div",Nt,[t("div",Tt,[s[7]||(s[7]=t("label",{for:"invoice_number"}," Quotation Number",-1)),$(t("input",{type:"text",id:"invoice_number",class:"form-control","onUpdate:modelValue":s[1]||(s[1]=n=>i.invoice_number=n),required:""},null,512),[[D,i.invoice_number]]),a.value.invoice_number?(d(),u("div",Vt,e(a.value.invoice_number),1)):N("",!0)])]),t("div",St,[t("div",Ut,[t("label",Dt,e(o.$t("Issue Date:")),1),$(t("input",{type:"date",id:"issue_date",class:"form-control","onUpdate:modelValue":s[2]||(s[2]=n=>i.issue_date=n),required:""},null,512),[[D,i.issue_date]]),a.value.issue_date?(d(),u("div",Ft,e(a.value.issue_date),1)):N("",!0)])]),t("div",Pt,[t("div",It,[t("label",jt,e(o.$t("Notes:")),1),$(t("textarea",{id:"notes",class:"form-control",rows:"2","onUpdate:modelValue":s[3]||(s[3]=n=>i.notes=n)},null,512),[[D,i.notes]])])])]),s[10]||(s[10]=t("hr",null,null,-1)),t("h5",zt,e(o.$t("Items")),1),a.value.items?(d(),u("div",Et,e(a.value.items),1)):N("",!0),t("div",qt,[t("table",Bt,[t("thead",null,[t("tr",null,[t("th",Ot,e(o.$t("Product")),1),t("th",null,e(o.$t("Item Name")),1),t("th",null,e(o.$t("Description")),1),t("th",Mt,e(o.$t("Qty")),1),t("th",Qt,e(o.$t("Unit")),1),t("th",Lt,e(o.$t("Unit Price")),1),t("th",Rt,e(o.$t("Total")),1),s[8]||(s[8]=t("th",{style:{width:"50px"}},null,-1))])]),t("tbody",null,[(d(!0),u(j,null,z(i.items,(n,f)=>(d(),u("tr",{key:f},[t("td",null,[$(t("select",{class:"form-control","onUpdate:modelValue":m=>n.product_id=m,onChange:m=>Y(n)},[t("option",Gt,e(o.$t("Custom item")),1),(d(!0),u(j,null,z(_(O),m=>(d(),u("option",{key:m.id,value:m.id},e(m.code)+" - "+e(m.name),9,Wt))),128))],40,Ht),[[R,n.product_id]])]),t("td",null,[$(t("input",{type:"text",class:"form-select","onUpdate:modelValue":m=>n.item_name=m,required:""},null,8,Yt),[[D,n.item_name]]),a.value[`items.${f}.item_name`]?(d(),u("div",Jt,e(a.value[`items.${f}.item_name`]),1)):N("",!0)]),t("td",null,[$(t("input",{type:"text",class:"form-control","onUpdate:modelValue":m=>n.description=m},null,8,Xt),[[D,n.description]])]),t("td",null,[$(t("input",{type:"number",min:"0.01",step:"0.01",class:"form-control","onUpdate:modelValue":m=>n.qty=m,required:""},null,8,Zt),[[D,n.qty,void 0,{number:!0}]]),a.value[`items.${f}.qty`]?(d(),u("div",Kt,e(a.value[`items.${f}.qty`]),1)):N("",!0)]),t("td",null,[$(t("select",{class:"form-select","onUpdate:modelValue":m=>n.unit_id=m},[t("option",ee,e(o.$t("Select Unit")),1),(d(!0),u(j,null,z(M.value,m=>(d(),u("option",{key:m.id,value:m.id},e(m.short_name),9,oe))),128))],8,te),[[R,n.unit_id]])]),t("td",null,[$(t("input",{type:"number",min:"0",step:"0.01",class:"form-control","onUpdate:modelValue":m=>n.unit_price=m,required:""},null,8,se),[[D,n.unit_price,void 0,{number:!0}]]),a.value[`items.${f}.unit_price`]?(d(),u("div",ne,e(a.value[`items.${f}.unit_price`]),1)):N("",!0)]),t("td",ae,e(p(n).toFixed(2)),1),t("td",le,[t("button",{type:"button",class:"btn btn-sm btn-outline-danger",disabled:i.items.length===1,onClick:m=>c(f)},[...s[9]||(s[9]=[t("i",{class:"fa-solid fa-trash"},null,-1)])],8,ie)])]))),128))])])]),C(b,{type:"button",class:"text-none mb-4",color:"grey-lighten-3",rounded:"0",variant:"flat",onClick:X},{default:T(()=>[E(e(o.$t("+ Add Item")),1)]),_:1}),s[11]||(s[11]=t("hr",null,null,-1)),t("div",de,[t("div",re,[t("div",ce,[t("div",ue,[t("div",me,[t("label",pe,e(o.$t("Discount:")),1),$(t("input",{type:"number",min:"0",step:"0.01",id:"discount_amount",class:"form-control","onUpdate:modelValue":s[4]||(s[4]=n=>i.discount_amount=n)},null,512),[[D,i.discount_amount,void 0,{number:!0}]]),a.value.discount_amount?(d(),u("div",_e,e(a.value.discount_amount),1)):N("",!0)])]),t("div",he,[t("div",ve,[t("label",fe,e(o.$t("VAT:")),1),$(t("input",{type:"number",min:"0",step:"0.01",id:"vat_amount",class:"form-control","onUpdate:modelValue":s[5]||(s[5]=n=>i.vat_amount=n)},null,512),[[D,i.vat_amount,void 0,{number:!0}]]),a.value.vat_amount?(d(),u("div",be,e(a.value.vat_amount),1)):N("",!0)])]),t("div",ge,[t("div",ye,[t("label",$e,e(o.$t("Attachment:")),1),t("input",{type:"file",id:"file",class:"form-control",accept:".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx",onChange:J},null,32),a.value.file?(d(),u("div",xe,e(a.value.file),1)):N("",!0)])])])]),t("div",we,[t("table",ke,[t("tbody",null,[t("tr",null,[t("td",null,e(o.$t("Total Amount")),1),t("td",Ce,e(v.value.totalAmount.toFixed(2)),1)]),t("tr",null,[t("td",null,e(o.$t("Discount")),1),t("td",Ae,e(v.value.discountAmount.toFixed(2)),1)]),t("tr",null,[t("td",null,e(o.$t("Subtotal")),1),t("td",Ne,e(v.value.subtotalAfterDiscount.toFixed(2)),1)]),t("tr",null,[t("td",null,e(o.$t("VAT")),1),t("td",Te,e(v.value.vatAmount.toFixed(2)),1)]),t("tr",Ve,[t("td",null,e(o.$t("Net Total")),1),t("td",Se,e(v.value.netTotal.toFixed(2)),1)])])])])]),t("div",Ue,[t("div",De,[C(b,{type:"submit",class:"text-none text-white",color:"blue-darken-4",rounded:"0",variant:"flat",disabled:w.value,loading:w.value},{default:T(()=>[E(e(o.$t("Submit")),1)]),_:1},8,["disabled","loading"]),C(b,{type:"button",class:"text-none",color:"grey-lighten-2",rounded:"0",variant:"flat",onClick:s[6]||(s[6]=n=>o.$emit("cancel"))},{default:T(()=>[E(e(o.$t("Cancel")),1)]),_:1})])])],32)]),_:1})])])}}},Ie={class:"main-content-wrapper mt-4"},je={class:"container-fluid"},ze={class:"d-flex justify-content-between align-items-center mt-3 mb-3"},Ee={class:"page-title mb-0"},qe={class:"d-flex align-center gap-2"},Be={class:"row"},Oe={class:"col-md-12 mb-2"},Me={class:"row align-items-center"},Qe={class:"col-md-3"},Le={value:""},Re=["value"],He={class:"col-md-5"},Ge={class:"search-wrapper d-flex align-center gap-2"},We={class:"text-left"},Ye={class:"text-left"},Je={class:"text-left"},Xe={class:"text-right"},Ze={class:"text-center"},Ke={class:"text-center"},to={key:0},eo={colspan:"7",class:"text-center py-4"},oo={key:1},so={colspan:"7",class:"text-center py-4"},no={class:"text-right"},ao={class:"text-center"},lo=["href","title"],io={key:1},ro={class:"text-center"},co={class:"table-action-menu"},uo={class:"menu-item"},mo=["onClick"],po={class:"menu-item"},_o=["onClick"],go={__name:"Index",setup(r){const g=[{key:"list",label:"Search",title:"All Quotation",icon:"fa-search",color:"blue-darken-3"},{key:"add",label:"New",title:"Add New Quotation",icon:"fa-plus",color:"green-darken-3"}],h=B("list"),y=it(()=>g.find(c=>c.key===h.value)??g[0]),x=c=>{h.value=c,c==="list"&&F()},i=()=>{x("list")},{items:V,loading:a,filters:w,pagination:S,fetchData:F,changePage:O,changePerPage:G,resetFilters:M}=mt("/api/quotations",{search:"",customer_id:""}),{items:W,fetchData:Y}=et("/api/customers",{per_page:100}),J=async c=>{var p,v;try{const{data:A}=await H.get(`/api/quotations/${c.id}`);if(!A.success)return;ft(A.data)}catch(A){K.error(((v=(p=A.response)==null?void 0:p.data)==null?void 0:v.message)||U("Failed to load the quotation."))}},X=async c=>{var v,A;if((await ut.fire({title:U("Are you sure?"),text:U('Delete quotation "{invoice}"?',{invoice:c.invoice_number}),icon:"warning",showCancelButton:!0,confirmButtonText:U("Yes, delete"),cancelButtonText:U("Cancel")})).isConfirmed)try{const P=await H.delete(`/api/quotations/${c.id}`);P.data.success&&(K.success(P.data.message),F())}catch(P){K.error(((A=(v=P.response)==null?void 0:v.data)==null?void 0:A.message)||U("Failed to delete quotation."))}};return at(()=>{F(),Y()}),(c,p)=>{const v=I("v-btn"),A=I("v-text-field"),P=I("v-progress-linear"),o=I("v-menu"),s=I("v-table"),b=I("v-card");return d(),u("div",Ie,[t("div",je,[t("div",ze,[t("h4",Ee,e(c.$t(y.value.title)),1),t("div",qe,[(d(),u(j,null,z(g,l=>C(v,{key:l.key,class:st(["text-none",{"text-white":h.value===l.key}]),color:l.color,variant:h.value===l.key?"flat":"outlined",rounded:"0",onClick:n=>x(l.key)},{default:T(()=>[t("i",{class:st(["fa-solid me-1",l.icon])},null,2),E(" "+e(c.$t(l.label)),1)]),_:2},1032,["class","color","variant","onClick"])),64))])]),h.value==="list"?(d(),ot(b,{key:0,style:{padding:"5px","margin-top":"15px"}},{default:T(()=>[t("div",Be,[t("div",Oe,[t("div",Me,[p[3]||(p[3]=t("div",{class:"col-md-4"},null,-1)),t("div",Qe,[$(t("select",{class:"form-select","onUpdate:modelValue":p[0]||(p[0]=l=>_(w).customer_id=l)},[t("option",Le,e(c.$t("All Customers")),1),(d(!0),u(j,null,z(_(W),l=>(d(),u("option",{key:l.id,value:l.id},e(l.name),9,Re))),128))],512),[[R,_(w).customer_id]])]),t("div",He,[t("div",Ge,[C(A,{variant:"outlined",density:"compact",placeholder:c.$t("Search invoice no..."),modelValue:_(w).search,"onUpdate:modelValue":p[1]||(p[1]=l=>_(w).search=l),"hide-details":"",class:"flex-grow-1"},null,8,["placeholder","modelValue"]),C(v,{type:"button",onClick:_(F),class:"text-none text-white",color:"blue-darken-3",rounded:"0",variant:"flat","min-width":"100"},{default:T(()=>[E(e(c.$t("Search")),1)]),_:1},8,["onClick"]),C(v,{onClick:lt(_(M),["prevent"]),class:"text-none",color:"grey-lighten-3",rounded:"0",variant:"flat","min-width":"100"},{default:T(()=>[E(e(c.$t("Reload")),1)]),_:1},8,["onClick"])])])])]),C(s,{class:"custom-bordered"},{default:T(()=>[t("thead",null,[t("tr",null,[p[4]||(p[4]=t("th",{class:"text-left"},"#",-1)),t("th",We,e(c.$t("Invoice No")),1),t("th",Ye,e(c.$t("Customer")),1),t("th",Je,e(c.$t("Issue Date")),1),t("th",Xe,e(c.$t("Net Total")),1),t("th",Ze,e(c.$t("File")),1),t("th",Ke,e(c.$t("Action")),1)])]),t("tbody",null,[_(a)?(d(),u("tr",to,[t("td",eo,[C(P,{indeterminate:"",color:"primary",size:"30"}),E(" "+e(c.$t("Loading...")),1)])])):_(V).length?(d(!0),u(j,{key:2},z(_(V),(l,n)=>(d(),u("tr",{key:l.id},[t("td",null,e((_(S).page-1)*_(S).perPage+n+1),1),t("td",null,e(l.invoice_number),1),t("td",null,e(l.customer_name),1),t("td",null,e(l.issue_date),1),t("td",no,e(Number(l.net_total).toFixed(2)),1),t("td",ao,[l.file_url?(d(),u("a",{key:0,href:l.file_url,target:"_blank",rel:"noopener",title:c.$t("Open attachment")},[...p[5]||(p[5]=[t("i",{class:"fa-solid fa-paperclip"},null,-1)])],8,lo)):(d(),u("span",io,"-"))]),t("td",ro,[C(o,null,{activator:T(({props:f})=>[t("button",ct({type:"button",class:"table-action-button"},{ref_for:!0},f),[...p[6]||(p[6]=[t("i",{class:"fa-solid fa-bars"},null,-1)])],16)]),default:T(()=>[t("ul",co,[t("li",uo,[t("button",{type:"button",class:"menu-link",onClick:f=>J(l)},e(c.$t("Print Quotation")),9,mo)]),t("li",po,[t("button",{type:"button",class:"menu-link",onClick:f=>X(l)},e(c.$t("Delete")),9,_o)])])]),_:2},1024)])]))),128)):(d(),u("tr",oo,[t("td",so,e(c.$t("No records found.")),1)]))])]),_:1}),C(pt,{"current-page":_(S).page,"per-page":_(S).perPage,total:_(S).total,"last-page":_(S).lastPage,onPageChange:_(O),onPerPageChange:_(G)},null,8,["current-page","per-page","total","last-page","onPageChange","onPerPageChange"])])]),_:1})):h.value==="add"?(d(),ot(Pe,{key:1,onSaved:i,onCancel:p[2]||(p[2]=l=>x("list"))})):N("",!0)])])}}};export{go as default};
