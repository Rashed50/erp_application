import 'vue3-toastify/dist/index.css';
import EditSubcontractorForm from "./components/EditSubcontractorForm.vue";
import NewSubcontractorForm from "./components/NewSubcontractorForm.vue";
import SearchSubcontractor from "./components/SearchSubcontractor.vue";
import SubContractor from "./components/SubContractor.vue";
import ReportsSubContractor from "./components/SubContractorReport.vue";
// service
import Sc_EditService from "./components/service/EditService.vue";
import Sc_NewService from "./components/service/NewService.vue";
import Sc_SearchService from "./components/service/SearchService.vue";
import Sc_SerivceMenu from "./components/service/SerivceMenu.vue";
import Sc_Invoice from "./components/service/Invoice.vue";

import Sc_EditPayment from "./components/payment/EditPayment.vue";
import Sc_NewPayment from "./components/payment/NewPayment.vue";
import Sc_PaymentMenu from "./components/payment/PaymentMenu.vue";
import Sc_SearchPayment from "./components/payment/SearchPayment.vue";





export {
    EditSubcontractorForm, NewSubcontractorForm, ReportsSubContractor, Sc_EditPayment, Sc_EditService, Sc_Invoice, Sc_NewPayment, Sc_NewService, Sc_PaymentMenu, Sc_SearchPayment, Sc_SearchService, Sc_SerivceMenu, SearchSubcontractor, SubContractor
};
