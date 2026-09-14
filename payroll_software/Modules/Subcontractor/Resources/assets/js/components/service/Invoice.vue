<template>
    <div class="container">
        <h3>Prepare Invoice</h3>
        <div class="row mb-3">
            <div class="col-md-4">
                <label for="subcontractor" class="form-label">Subcontractor <span class="text-danger">*</span></label>
                <select id="subcontractor" v-model="selectedSubcontractor" class="form-select" required>
                    <option value="">Select Subcontractor</option>
                    <option v-for="sc in data_for_form.subcontractors" :key="sc.subcon_auto_id" :value="sc.subcon_auto_id">
                        {{ sc.subcon_name }}
                    </option>
                </select>
            </div>
             <div class="col-md-3">
                <label for="service_month" class="form-label">Service Month <span class="text-danger">*</span></label>
                <select id="service_month" v-model="serviceMonth" class="form-select" required>
                    <option value="">Month</option>
                    <option v-for="m in 12" :key="m" :value="m">{{ monthName(m) }}</option>
                </select>
            </div>
            <div class="col-md-3">
                  <label for="service_year" class="form-label">Service Year <span class="text-danger">*</span></label>
                <!-- <input type="number" id="service_year" v-model="serviceYear" class="form-select" min="2000" max="2100" required /> -->
                 <select v-model="serviceYear" id="service_year" class="form-select">
                                <option v-for="year in years" :key="year" :value="year">
                                {{ year }}
                                </option>
                        </select>

            </div>

        </div>
        <div class="row mb-3">

            <div class="col-md-4">
                <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                <input type="date" id="invoice_date" v-model="invoiceDate" class="form-control" required />
            </div>
            <div class="col-md-3">
                <label for="invoice_no" class="form-label">Invoice Number <span class="text-danger">*</span></label>
                <input type="text" id="invoice_no" v-model="invoiceNo" class="form-control" required />
            </div>
             <div class="col-md-3">


                        <label for="service_year" class="form-label">Payment Method <span class="text-danger">*</span></label>
                <select  v-model="payment_method"   class="form-select"  >
                                <option value="1">Cash</option>
                                <option value="2">Bank</option>
                                <option value="3">Cheque</option>
                </select>
            </div>

        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Workers</th>
                            <th>Manhours</th>
                            <th>Per Hour Rate</th>
                            <th>Sub Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(item, index) in items" :key="index">
                            <td>
                                <select v-model="item.category_id" class="form-select" @change="calculateTotal(index)">
                                    <option value="">Select Category</option>
                                    <option v-for="category in data_for_form.item_categories" :key="category.catg_id" :value="category.catg_name">
                                        {{ category.catg_name }}
                                    </option>
                                </select>
                            </td>
                            <td>
                                <input type="number" v-model.number="item.total_workers" class="form-control" @input="calculateTotal(index)" min="0" step="1" required>
                            </td>
                            <td>
                                <input type="number" v-model.number="item.total_manhours" class="form-control" @input="calculateTotal(index)" min="0" step="0.5" required>
                            </td>
                            <td>
                                <input type="number" v-model.number="item.unit_price" class="form-control" @input="calculateTotal(index)" min="0" step="0.5" required>
                            </td>
                            <td class="text-center" style="width: 15%;">
                                <span style="font-weight: bold; font-size: 14px; color: black;">{{ item.total.toFixed(2) }}</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-link text-danger" @click="removeItem(index)" title="Delete"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-success" @click="addItem">Add Item</button>
            </div>
        </div>
        <div class="row">
                <label for="other" class="form-label col-md-10 text-end">Other Charges:</label>
                <input type="number" id="other" v-model.number="otherCharges" class="form-control col-md-2 text-end font-weight-bold" min="0" step="1">
        </div>

        <div class="row mt-2">
            <label for="other" class="form-label col-md-10 text-end">Grand Total:</label>
                <span class="form-control col-md-2 text-end font-weight-bold" min="0" step="1">{{ grandTotal.toFixed(2) }}</span>
        </div>

        <div class="row mt-2">
            <div class="col-md-12">
                <label for="remarks" class="form-label">Remarks</label>
                <textarea id="remarks" v-model="remarks" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <button class="btn btn-primary float-end" @click="generateInvoice">Generate Invoice</button>
            </div>
        </div>
    </div>
</template>

<script>
    import { toast } from 'vue3-toastify';

export default {
    props: ["data_for_form"],
    data() {
        return {
            selectedSubcontractor: '',
            invoiceDate: new Date().toISOString().substr(0, 10),
            invoiceNo: '',
            serviceMonth: '',
            serviceYear: new Date().getFullYear(),
            years: [new Date().getFullYear(),new Date().getFullYear()-1],
            payment_method: '1',
            items: [
                {
                    category_id: '',
                    total_workers: 1,
                    total_manhours: 0,
                    unit_price: 0,
                    total: 0
                }
            ],
            otherCharges: 0,
            remarks: ''
        };
    },
    computed: {
        grandTotal() {
            const itemsTotal = this.items.reduce((sum, item) => sum + item.total, 0);
            return itemsTotal + (this.otherCharges || 0);
        }
    },
    methods: {
        addItem() {
            this.items.push({
                category_id: '',
                total_workers: 1,
                total_manhours: 0,
                unit_price: 0,
                total: 0
            });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        calculateTotal(index) {
            const item = this.items[index];
            // Total = total_manhours * unit_price
            item.total = (item.total_manhours || 0) * (item.unit_price || 0);
        },
        monthName(m) {
            return new Date(2000, m - 1, 1).toLocaleString('default', { month: 'long' });
        },
        generateInvoice() {
            // Validation
            if (!this.selectedSubcontractor || !this.invoiceDate || !this.invoiceNo || !this.serviceMonth || !this.serviceYear) {
                toast.error('Please fill all mandatory fields.');
                return;
            }
            if (this.items.some(item => !item.category_id || !item.total_workers || !item.total_manhours || !item.unit_price)) {
                toast.error('Please fill all item fields.');
                return;
            }
            // Logic to generate invoice and open in new tab
            const invoiceWindow = window.open('', '_blank');
            invoiceWindow.document.write(this.generateInvoiceHtml());
           // invoiceWindow.document.close();
        },
        generateInvoiceHtml() {
            // --- Invoice Data Preparation ---
            const companyName = 'ASLOOB BEDAA CO.';
            const companyAddress = 'Riyadh, Saudi Arabia';
            // Use the actual logo path from the PDF reference
            const logoUrl = '/contents/admin/assets/images/logo_new.png'; // This matches the PDF report reference
            const invoiceTitle = 'INVOICE';
            const dateStr = this.invoiceDate ? new Date(this.invoiceDate).toLocaleDateString() : new Date().toLocaleDateString();
            const subcon = this.data_for_form.subcontractors.find(s => s.subcon_auto_id == this.selectedSubcontractor) || {};
            const subconName = subcon.subcon_name || '-';
            const subconIqama = subcon.iqama_no || '-';
            const subconPassport = subcon.passport_no || '-';
            const subconMobile = subcon.mobile_no || '-';
            const subconEmail = subcon.subcont_email || '-';
            const subconAddress = subcon.present_address || '-';
            const subconJoining = subcon.joining_date || '-';
            const subconStatus = subcon.status || '-';
            const invoiceNo = this.invoiceNo;
            const serviceMonth = this.monthName(this.serviceMonth);
            const serviceYear = this.serviceYear;
            const remarks = this.remarks || '-';
            const otherCharges = this.otherCharges || 0;
            // --- Table Rows ---
            let rows = '';
            this.items.forEach((item, idx) => {
                debugger;
                const cat = this.data_for_form.item_categories.find(c => c.catg_name == item.category_id);
                rows += `<tr>
                    <td>${cat ? cat.catg_name : ''}</td>
                    <td style='text-align:center;'>${item.total_workers}</td>
                    <td style='text-align:center;'>${item.total_manhours}</td>
                    <td style='text-align:center;'>${item.unit_price.toFixed(2)}</td>
                    <td style='text-align:right;'>${item.total.toFixed(2)}</td>
                </tr>`;
            });
            // --- Calculation Section ---
            const subtotal = this.items.reduce((sum, item) => sum + item.total, 0);
            const discount = 0; // Placeholder for future
            const tax = 0; // Placeholder for future
            const grandTotal = this.grandTotal.toFixed(2);
            const grandTotalWords = this.numberToWords(this.grandTotal);
            // --- Terms & Conditions ---
            const terms = `
                <ol style='margin:0 0 0 18px;padding:0;font-size:11px;'>
                    <li>Payment is due within 30 days from the invoice date.</li>
                    <li>All services are subject to company policy and KSA law.</li>
                    <li>Disputes must be reported within 7 days of receipt.</li>
                </ol>
            `;
            // --- HTML Layout ---
            return `<!DOCTYPE html>
<html><head><title>Invoice</title>
<style>
body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #fff; margin: 0; }
.invoice-a4 { width: 210mm; height: 297mm; min-height: 297mm; max-height: 297mm; margin: 0 auto; background: #fff; box-shadow: 0 0 10px #ccc; padding: 18mm 14mm 24mm 14mm; box-sizing: border-box; position: relative; overflow: hidden; }
.header { text-align: center; margin-bottom: 8px; position: relative; }
.invoice-logo { position: absolute; left: 0; top: 0; height: 60px; }
.invoice-title { font-size: 28px; font-weight: bold; letter-spacing: 2px; color: #222; margin-bottom: 2px; }
.invoice-meta { font-size: 13px; color: #444; margin-bottom: 10px; }
.top-info { display: flex; justify-content: space-between; margin-bottom: 10px; }
.info-col { width: 48%; font-size: 13px; color: #222; margin-top: 10px; }
.info-label { font-weight: bold; color: #222; }
.payment-type-box { border: 1px solid #bbb; border-radius: 6px; padding: 10px 14px; background: #fafafa; margin-bottom: 0; width: 220px; margin-top: 0; float: none; }
.payment-type-title { font-weight: bold; font-size: 13px; margin-bottom: 4px; }
.payment-type-options { margin-bottom: 6px; }
.checkbox { display: inline-block; width: 13px; height: 13px; border: 1.5px solid #222; border-radius: 2px; margin-right: 4px; vertical-align: middle; }
.checked { background: #222; }
.payment-type-details { font-size: 12px; color: #333; }
.service-details { margin: 12px 0 10px 0; font-size: 13px; }
.table-section { margin-top: 10px; }
.invoice-table { width: 100%; border-collapse: collapse; margin-bottom: 0; }
.invoice-table th, .invoice-table td { border: 1px solid #bbb; padding: 7px 8px; font-size: 13px; }
.invoice-table th { background: #f5f5f5; color: #222; font-weight: 600; }
.invoice-table td { background: #fff; color: #222; }
.summary-table { width: 40%; float: right; margin-top: 12px; border-collapse: collapse; }
.summary-table td { font-size: 13px; padding: 6px 8px; border: none; text-align: right; }
.summary-table .label { text-align: left; color: #444; }
.summary-table .grand { font-weight: bold; font-size: 15px; color: #111; border-top: 1.5px solid #bbb; }
.grand-total-words { font-size: 13px; font-weight: 500; margin-top: 8px; margin-right:10px; color: #333; text-align: left; }
.remarks-section { font-size: 13px; margin-top: 50px; margin-right:10px; color: #444; text-align: left; }
.payment-type-bottom { margin-top: 60px; margin-bottom: 0; width: 420px; }
.divider { border-top: 1.5px solid #bbb; margin: 18px 0 8px 0; }
.footer { position: absolute; left: 14mm; right: 14mm; bottom: 14mm; border-top: 1px solid #e0e0e0; padding-top: 8px; font-size: 12px; color: #555; background: #fff; }
.terms-title { font-weight: bold; font-size: 13px; margin-bottom: 2px; }
@media print { body { background: #fff; } .invoice-a4 { box-shadow: none; } .footer { position: fixed; bottom: 14mm; } }
</style>
</head><body>
<div class='invoice-a4'>
    <div class='header'>
        <img src='${logoUrl}' class='invoice-logo' alt='Company Logo'>
        <div class='invoice-title'>${invoiceTitle}</div>
        <div class='invoice-meta'>Invoice No: <b>${invoiceNo}</b> &nbsp; | &nbsp; Date: <b>${dateStr}</b></div>
    </div>
    <div class='top-info'>
        <div class='info-col'>
            <div class='info-label'>Service Provider</div>
             <div>${subconName}</div>
            <div>${subconAddress}</div>
            <div>${subconEmail}</div>

        </div>
        <div class='info-col' style='text-align:right;'>
            <div class='info-label'>Bill To</div>
              <div>${companyName}</div>
            <div>${companyAddress}</div>
        </div>
    </div>
    <div class='service-details'>
        <b>Service Duration:</b> ${serviceMonth}, ${serviceYear}
    </div>
    <div class='table-section'>
        <table class='invoice-table'>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Total Workers</th>
                    <th>Total Manhours</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                ${rows}
            </tbody>
        </table>
        <table class='summary-table'>
            <tr><td class='label'>Subtotal</td><td>${subtotal.toFixed(2)}</td></tr>
            <tr><td class='label'>Discount</td><td>${discount.toFixed(2)}</td></tr>
            <tr><td class='label'>Tax</td><td>${tax.toFixed(2)}</td></tr>
            <tr><td class='label'>Other</td><td>${otherCharges.toFixed(2)}</td></tr>
            <tr><td class='label grand'>Grand Total</td><td class='grand'>${grandTotal}</td></tr>
            <tr><td colspan='2' style='text-align: left;'><b>In Words:</b>  ${grandTotalWords}</td></tr>

        </table>
    </div>
    <div class='remarks-section'><b>Remarks:</b> ${remarks}</div>


    <div class='payment-type-bottom'>
        <div class='payment-type-box'>
            <div class='payment-type-title'>Payment Type</div>
            <div class='payment-type-options'>
                <span class='checkbox checked'></span> Bank
                <span class='checkbox'></span> Cash
                <span class='checkbox'></span> Cheque
            </div>
            <div class='payment-type-details'>
                <div><b>Bank Name:</b> Al Rajhi Bank</div>
                <div><b>Account No:</b> 1234567890</div>
                <div><b>IBAN:</b> SA12345678901234567890</div>
            </div>
        </div>
    </div>
    <div class='divider'></div>
    <div class='footer'>
        <div class='terms-title'>Terms & Conditions</div>
        ${terms}
    </div>
</div>
</body></html>`;
        },
        numberToWords(num) {
            // Simple number to words for up to 999999.99 (English)
            // For production, use a library or more robust solution
            const th = ['', 'Thousand', 'Million', 'Billion'];
            const dg = ['Zero','One','Two','Three','Four','Five','Six','Seven','Eight','Nine'];
            const tn = ['Ten','Eleven','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen'];
            const tw = ['Twenty','Thirty','Forty','Fifty','Sixty','Seventy','Eighty','Ninety'];
            let s = String(parseFloat(num).toFixed(2));
            let [n, dec] = s.split('.');
            n = parseInt(n, 10);
            if (isNaN(n)) return '';
            if (n === 0) return 'Zero Riyals Only';
            let str = '';
            let i = 0;
            while (n > 0) {
                let k = n % 1000;
                if (k) {
                    let t = '';
                    if (k > 99) t += dg[Math.floor(k/100)] + ' Hundred ';
                    k = k % 100;
                    if (k > 0 && k < 10) t += dg[k];
                    else if (k >= 10 && k < 20) t += tn[k-10];
                    else if (k >= 20) t += tw[Math.floor(k/10)-2] + (k%10 ? '-' + dg[k%10] : '');
                    str = t + ' ' + th[i] + ' ' + str;
                }
                n = Math.floor(n/1000); i++;
            }
            str = str.replace(/ +/g, ' ').trim() + ' Riyals';
            if (dec && parseInt(dec, 10) > 0) {
                str += ' and ' + (dec[0] !== '0' ? dg[parseInt(dec[0])] + (dec[1] !== '0' ? ' ' + dg[parseInt(dec[1])] : '') : dg[parseInt(dec[1])]) + ' Halalas';
            } else {
                str += ' Only';
            }
            return str;
        },
    }
};
</script>

<style scoped>
.table th, .table td {
    vertical-align: middle;
}
</style>
