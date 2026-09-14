<?php

namespace App\Http\Controllers\Admin\Helper;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class UploadDownloadController extends Controller
{


  public function uploadEmployeeProfilePhoto($file,$oldFilePath)
  {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("emph", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

//     if($file == null)
//     return null;
//     $appoint_name = 'ph-' . time() . '.' . $file->getClientOriginalExtension();
//     $destinationPath = "uploads/employee/profile/";
//     $uploadPath =  $destinationPath . $appoint_name;

//     $file->move($destinationPath, $appoint_name);
//     if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
//     {  unlink($oldFilePath);}         // File::delete($oldFilePath);

//   return $uploadPath;

  }

  public function uploadEmployeePassportFile($file,$oldFilePath)
  {
         try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("pasp", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

  }

  public function uploadEmployeeIqamaFile($file,$oldFilePath)
  {

        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("iqm", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

//     if($file == null)
//     return null;
//     $appoint_name = 'iq-' . time() . '.' . $file->getClientOriginalExtension();
//     $destinationPath = "uploads/employee/iqama/";
//     $uploadPath =  $destinationPath . $appoint_name;
//     $file->move($destinationPath, $appoint_name);
//     if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
//     {  unlink($oldFilePath);}         // File::delete($oldFilePath);

//    return $uploadPath;

  }

  public function uploadEmployeeAppointmentLetterFile($file,$oldFilePath)
  {
    try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("ofl", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }
  }

  public function uploadEmployeeMedicalReportFile($file,$oldFilePath)
  {
      try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("medr", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

  }



  public function uploadEmployeeCOVIDCertificateFile($file,$oldFilePath)
  {
      try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("medr", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

  }

 public function uploadEmployeeEducationalDocuments($file,$oldFilePath)
  {
      try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("ofl", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

  }

   public function uploadEmployeeBloodGroupPaper($file,$oldFilePath)
  {
        try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("blg", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

  }


    public function uploadBDOfficePaymentSlip($file,$oldFilePath)
  {
       try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("bdslp", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

  }


   /*
   ==========================================================================
   ============================= Employee Promotion Releted Documents =======
   ==========================================================================
  */
        //Promotion Approved Document
    public function uploadEmployeePromotionApprovedPhoto($file,$oldFilePath)
    {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("prom", $file); // only the file name in the bucket
            }


            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

    }



   /*
     ==========================================================================
     ============================= Company Vehicle Documents ==================
     ==========================================================================
    */


    // (Insurance Certificate)
    public function uploadVehicleInsuranceCertificate($file,$oldFilePath)
    {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket
            }

            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

    }
    // (Registration Certificate)
    public function uploadVehicleRegistrationCertificate($file,$oldFilePath)
    {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }
    }
    // (Vehicle Photo)
    public function uploadVehiclePhoto($file,$oldFilePath)
    {
         try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }
    }
    /*
     ==========================================================================
     ============================= Company Driver Documents ============================
     ==========================================================================
    */
    // Driver Iqama Certificate
    public function uploadDriverIqamaCertificate($file,$oldFilePath)
    {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }


    }
    // Driver Driving License
    public function uploadDrivingLicenseCertificate($file,$oldFilePath)
    {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

    }
    // Driver Insurance Certificate
    public function uploadDriverInsuranceCertificate($file,$oldFilePath)
    {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        }catch (\Exception $e) {
            return null;
        }

    }

    // upload video and photos of vehicle
    public function uploadDriverVehiclePhotos($file,$oldFilePath)
    {
         try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("vehs", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        }catch (\Exception $e) {
            return null;
        }
    }

       /*
     ==========================================================================
         ================ Company Building Rent Documents ================
     ==========================================================================
    */
    public function uploadBuildingRentDeedPhoto($file,$oldFilePath)
    {
         try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("rntded", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }
    }


     /*
     ==========================================================================
         ================ uploadEmployeeSalarySheet ================
     ==========================================================================
    */
    public function uploadEmployeeSalarySheet($file,$oldFilePath)
    {
         if($file == null)
             return null;

        if ($file->isValid()) {
            // upload into S3 bucket Use 'put' and pass the file object directly
           return Storage::disk('s3')->put("pslip", $file); // only the file name in the bucket

        }else
        {
             return null;
        }
    }

    public function deleteUploadedSalarySheet($oldFilePath)
    {
        if($oldFilePath == null)
                return false;

        Storage::disk('s3')->delete($oldFilePath);
        return true;


    }





    /*
     ==========================================================================
         ================ upload employee TUV Photo ================
     ==========================================================================
    */
    public function uploadEmployeeTUVPhoto($file,$oldFilePath)
    {

         try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("tuv", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }


    //     if($file == null)
    //     return null;
    //     $appoint_name = 'Emp_TUV-' . time() . '.' . $file->getClientOriginalExtension();
    //     $destinationPath = "uploads/employee/tuv_photo/";
    //     $uploadPath =  $destinationPath . $appoint_name;
    //     $file->move($destinationPath, $appoint_name);
    //     if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
    //     {  unlink($oldFilePath);}

    //    return $uploadPath;
    }



      /*
     ==========================================================================
         ================ upload employee Mobile Bill Paper Photo ================
     ==========================================================================
    */
    public function uploadEmployeeMobileBillPaper($file,$oldFilePath)
    {


         try {
            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("mobil", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }
    }


     /*
     ==========================================================================
     =============================== uploadEmployeeSalarySheet ================
     ==========================================================================
    */
    public function uploadAdvancePaper($file,$oldFilePath)
    {

        if($file == null)
            return null;

        if ($file->isValid()) {
            // upload into S3 bucket Use 'put' and pass the file object directly
             return Storage::disk('s3')->put("emadv", $file); // only the file name in the bucket
        }else
         return null;

    }

    public function deleteUploadedAdvancePaper($oldFilePath)
    {
         if ($oldFilePath && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
        }

    }


    /*
     ==========================================================================
     =============================== Upload Item Recived Paper ================
     ==========================================================================
    */
    public function uploadItemReceivedPaper($file,$oldFilePath)
    {
        if($file == null)
        return null;
        $file_aname = 'Item-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/Items/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteItemReceivedPaper($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }


        /*
     ==========================================================================
     =============================== Upload Invoice Paper ================
     ==========================================================================
    */
    public function uploadInvoiceSummaryPaper($file,$oldFilePath)
    {

        if($file == null)
        return null;

        $file_aname = 'inv-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/invoice/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteInvoiceSummaryUploadedPaper($oldFilePath)
    {
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }

         /*
     ==========================================================================
     =============================== Upload Leave Paper =======================
     ==========================================================================
    */
    public function uploadEmployeeLeaveApplicationPaper($file,$oldFilePath)
    {

        
         if($file == null)
                return null;

        if ($file->isValid()) {
            // upload into S3 bucket Use 'put' and pass the file object directly
            return Storage::disk('s3')->put("lapl", $file); // only the file name in the bucket

        }else
         return null;
    }

    public function deleteEmployeeLeaveApplicationPaper($oldFilePath)
    {
        return;
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }


    /*
     ==========================================================================
     =============================== Project Contract Paper====================
     ==========================================================================
    */
    public function uploadProjectContractPaper($file,$oldFilePath)
    {
       // dd($file);
        if($file == null)
        return null;

        $file_aname = 'Ag-' . time() . '.' . $file->getClientOriginalExtension();
        $destinationPath = "uploads/project/";
        $uploadPath =  $destinationPath . $file_aname;
        $file->move($destinationPath, $file_aname);

        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}

        return $uploadPath;
    }

    public function deleteProjectContractPaper($oldFilePath)
    {
        return;
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }


    /*
     ==========================================================================
     =============================== Project Contract Paper====================
     ==========================================================================
    */
    public function uploadDrInvoiceFile($file,$oldFilePath)
    {
         if($file == null)
                return null;

         if ($file->isValid()) {
            // upload into S3 bucket Use 'put' and pass the file object directly
             return Storage::disk('s3')->put("drvou", $file); // only the file name in the bucket

        }else
         return null;

    }

    public function deleteDrInvoiceFileFromPath($oldFilePath)
    {
        return ;
        if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        {  unlink($oldFilePath);}
    }

    public function uploadCrInvoiceFile($file,$oldFilePath)
    {

         if($file == null)
                return null;

          if ($file->isValid()) {
            // upload into S3 bucket Use 'put' and pass the file object directly
            return Storage::disk('s3')->put("crinv", $file); // only the file name in the bucket

        }else
         return null;

    }


        // upload cash paid by employee paper
    public function uploadCashPaidByEmolyeePaper($file,$oldFilePath)
    {
        // if($file == null)
        // return null;
        // $file_aname = 'cashpaid-' . time() . '.' . $file->getClientOriginalExtension();
        // $destinationPath = "uploads/advance/";
        // $uploadPath =  $destinationPath . $file_aname;
        // $file->move($destinationPath, $file_aname);

        // if($oldFilePath != null && $oldFilePath != "" && File::exists($oldFilePath))
        // {  unlink($oldFilePath);}

        // return $uploadPath;


        if($file == null)
        return null;

        if ($file->isValid()) {
            // upload into S3 bucket Use 'put' and pass the file object directly
             return Storage::disk('s3')->put("emadv", $file); // only the file name in the bucket

        }else
         return null;
    }



         /*
     ==========================================================================
         ================ update ajeer file ================
     ==========================================================================
    */
    public function uploadEmployeeAjeerFile($file,$oldFilePath)
    {

        try {

             if($file == null)
                return null;

            $uploaded_path = null;

            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
            $uploaded_path = Storage::disk('s3')->put("ajeer", $file); // only the file name in the bucket
            }


            if ($oldFilePath && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }

    }


    public function uploadEmployeefiscalClosingSheet($file,$oldFilePath)
    {
         if($file == null)
             return null;

        if ($file->isValid()) {
            if ($oldFilePath && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            // upload into S3 bucket Use 'put' and pass the file object directly
           return Storage::disk('s3')->put("pslip", $file); // only the file name in the bucket

        }
        return null;
    }


    // all file delete employee info before approval
     public function deleteAnEmplyoeeAllfilesBeforeApproval($employee)
     {
        if(!empty($employee->profile_photo) && Storage::disk('s3')->exists($employee->profile_photo) ){
            Storage::disk('s3')->delete($employee->profile_photo);
        }
        if(!empty($employee->pasfort_photo) && Storage::disk('s3')->exists($employee->pasfort_photo) ){
            Storage::disk('s3')->delete($employee->pasfort_photo);
        }
        if(!empty($employee->akama_photo) && Storage::disk('s3')->exists($employee->akama_photo) ){
            Storage::disk('s3')->delete($employee->akama_photo);
        }
     }

public function uploadTicketFile($file,$oldFilePath)
  {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("tick", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }
  }

    public function deleteTicketFile($oldFilePath)
    {
        if($oldFilePath != null && Storage::disk('s3')->exists($oldFilePath))
        {  Storage::disk('s3')->delete($oldFilePath);}
    }



public function uploadAccountingPurchaseInvoice($file,$oldFilePath)
  {
        try {

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("acc_pur", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;

        } catch (\Exception $e) {
            return null;
        }
  }

    public function deleteAccountingPurchaseInvoice($oldFilePath)
    {
        if($oldFilePath != null && Storage::disk('s3')->exists($oldFilePath))
        {  Storage::disk('s3')->delete($oldFilePath);}
    }


    /*
    ===========================================================================
    ============================== SUBCONTRACTOR FILE SECTION =================
    ===========================================================================
    */

    public function uploadSubcontractorInvoiceFile($file,$oldFilePath){

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("sc_inv", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;
    }
    public function deleteSubcontractorInvoiceFile($oldFilePath){

           
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return true;
    }

    public function uploadSubcontractorPaymentFile($file,$oldFilePath){

            if($file == null)
                return null;
            $uploaded_path = null;
            if ($file->isValid()) {
                // upload into S3 bucket Use 'put' and pass the file object directly
                $uploaded_path = Storage::disk('s3')->put("sc_pay", $file); // only the file name in the bucket
            }
            if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
                Storage::disk('s3')->delete($oldFilePath);
            }
            return $uploaded_path;
    }
     public function deleteSubcontractorPaymentFile($oldFilePath){
          
        if (is_null($oldFilePath) == false  && Storage::disk('s3')->exists($oldFilePath)) {
            Storage::disk('s3')->delete($oldFilePath);
        }
        return true;
    }



}

