import EmployeeSalaryComponent from "./components/EmployeeSalaryComponent.vue";
import SalaryUpdateContainer from "./components/SalaryUpdateContainer.vue";
import SalaryProcessComponent from "./components/SalaryProcessComponent.vue";


import EmployeeBonusContainer from "./components/employee_bonus/EmployeeBonusContainer.vue";

// Daily Attendance Part
import AttendanceProcessComponent from "./components/attendance/AttendanceProcessComponent.vue";
import AttendanceReportComponent from "./components/attendance/AttendanceReportComponent.vue";

import AttendanceINOUTComponent from "./components/attendance/InOutComponent.vue";
import OvertimeComponent from "./components/attendance/OvertimeComponent.vue";
import AttendanceBioComponent from "./components/attendance/AttendanceBioComponent.vue";

import PartialSalaryHistoryManager from "./components/salary/PartialSalaryHistoryManager.vue";
// payslip upload
import PayslipUploadManager from './components/payslip_upload/SalarySheetManager.vue';

import "vue3-toastify/dist/index.css";

export { EmployeeSalaryComponent ,SalaryProcessComponent,AttendanceProcessComponent,AttendanceReportComponent,AttendanceINOUTComponent,OvertimeComponent,PartialSalaryHistoryManager,PayslipUploadManager,SalaryUpdateContainer,EmployeeBonusContainer,AttendanceBioComponent};
