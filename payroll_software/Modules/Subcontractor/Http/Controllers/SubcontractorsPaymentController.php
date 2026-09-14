<?php

namespace Modules\Subcontractor\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Support\Renderable;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Illuminate\Validation\ValidationException;
use Modules\Subcontractor\Entities\SubcontractorPayment;
use App\Http\Controllers\DataServices\CompanyDataService;
use Modules\Subcontractor\Services\SubcontractorDataService;

class SubcontractorsPaymentController extends Controller
{
    protected $subcontractorDataService;

    public function __construct(SubcontractorDataService $subcontractorDataService)
    {
        $this->subcontractorDataService = $subcontractorDataService;
    }



    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $subcontractors = (new SubcontractorDataService())->getAllActiveSubcontractorsForDropdownList();
        return view('subcontractor::pages.payment.index', [
            'payment_form_data' =>
            [
                'subcontractors' => $subcontractors,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'total_amount' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:2100',
                'payment_method' => 'required|in:1,2',
                // 'bank_id' => 'nullable|integer',
                'created_by' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }

            $data = $request->all();

            $payment_file = null;
            if ($request->hasFile('payment_file') && is_file($request->file('payment_file'))) {
                $payment_file = (new UploadDownloadController())->uploadSubcontractorPaymentFile($request->file('payment_file'),null);
            }
            $data['payment_file'] = $payment_file;

            $login_id =  Auth::user()->id;
            $data['created_by'] = $login_id;
            $data['approved_by'] = $login_id;

            $arecord = (new SubcontractorDataService())->storePayment($data);
            return response()->json(['message' => 'Subcontractor saved successfully!', 'data' => $request->all()], 201);

        } catch (\Exception $ex) {
            return response()->json(['errors' => 'Operation Failed '], 500);
        }
    }

   public function processASubcontractorOverAllInvoiceAndPaymentSummary($subcontractor_id){
        try{

           $summary_amount= (new SubcontractorDataService())->processASubcontractorUptoTodayBalanceSummary($subcontractor_id);
            return response()->json(['message' => '', 'data' => $summary_amount,'id'=>$subcontractor_id], 200);

        }catch(Exception $ex){
             return response()->json(['errors' => 'Operation Failed','message'=>'Operation Failed'], 500);
        }

   }

   // create payment file
    public function generateInvoice(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'subcon_auto_id'=>'required|integer',
                'total_amount' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric|min:0',
                'grand_total' => 'required|numeric|min:0',
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:2100',
                'payment_method' => 'required|integer:1,2',
                // 'bank_id' => 'nullable|integer',
                'payment_date'=> 'required|date',
               // 'created_by' => 'nullable|exists:users,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            $data = $request->all();
          //  dd($data);

            $subcontractor = $this->subcontractorDataService->getASubcontractorById($data['subcon_auto_id']);
            if($subcontractor==null){
                return "Subcontractor Not Found ";
            }else if($this->subcontractorDataService->checkIsPendingASubcontractPaymentInvoice($data['subcon_auto_id'])){
                return "Please Close the Previous Pending Payment invoice and Try Again";
            }


            $payment_file = null;
            if ($request->hasFile('payment_file') && is_file($request->file('payment_file'))) {
                $payment_file = (new UploadDownloadController())->uploadSubcontractorPaymentFile($request->file('payment_file'),null);
            }

            $data['payment_file'] = $payment_file;
            $login_id =  Auth::user()->id;
            $data['created_by'] = $login_id;
            $data['approved_by'] = $login_id;

          // $amonth_summary= (new SubcontractorDataService())->processASubcontractorSingleMonthTotalInvoiceAndPaidAmountForInvoiceGeneration($data['subcon_auto_id'],$data['month'],$data['year']);

           $arecord = (new SubcontractorDataService())->storePayment($data);


            $invoice_data = $this->subcontractorDataService->generateSubcontractorPaymentInvoice($request->all());
            $company = (new CompanyDataService())->findCompanryProfile();
            $prepared_by = Auth::user()->name;

            return View('subcontractor::pages.payment.invoice', [
                'invoice_data' => $invoice_data,
                //'amonth_summary' =>$amonth_summary,
                'prepared_by' => $prepared_by,
                'company' => $company
            ]);


            $pdf = Pdf::loadView('subcontractor::pages.payment.invoice', [
                'invoice_data' => $invoice_data,
               // 'amonth_summary' =>$amonth_summary,
                'prepared_by' => $prepared_by,
                'company' => $company
            ])->setPaper('a4');

            // //! Open PDF in browser
            return $pdf->stream('payment_invoice.pdf');

        } catch (Exception $ex) {

            return response()->json(['errors' => 'Operation Failed '.$ex], 500);
        }


    }



    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try{

            $payment = SubcontractorPayment::find($id);

             if (!$payment) {
                return response()->json(['message' => 'Payment not found'], 404);
            }
            return response()->json(['success'=>true, 'data'=>$payment],200);
        }
        catch (Exception $ex) {
            return response()->json(['errors' => 'Operation Failed '.$ex], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $payment = SubcontractorPayment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'total_amount' => 'numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'grand_total' => 'numeric|min:0',
            'month' => 'integer|min:1|max:12',
            'year' => 'integer|min:2000|max:2100',
            'payment_method' => 'in:1,2',
            'bank_id' => 'nullable|integer',
            'remarks' => 'nullable|string',
            'act_status' => 'boolean',
            'approved_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment->update($request->all());
        return response()->json($payment);
    }

    public function searchPaymentForListView(Request $request)
    {

        $request->validate([
            // 'subcon_auto_id' => 'required|numeric',
            // 'month'          => 'required|integer|min:1|max:12',
            'year'           => 'required|integer'
        ]);

        $services =  (new SubcontractorDataService())->searchPaymentRecordsForListView($request->subcon_auto_id, $request->month, $request->year);
        return response()->json(['message' => '', 'data' => $services,'dd' => $request->all()], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $payment = SubcontractorPayment::find($id);
        if (!$payment) {
            return response()->json(['message' => 'Payment not found'], 404);
        }

        $payment->delete();
        return response()->json(['message' => 'Payment deleted successfully']);
    }



    // search single payment record for edit page
    public function showPaymentAPI($id)
    {
        try {
            $payment = SubcontractorPayment::with([
                'subcontractor:subcon_auto_id,subcon_name',
                'approvedUser:id,name',
                'createdUser:id,name',
                'updatedUser:id,name'
            ])
                ->where('subcon_pay_auto_id', $id)
                ->firstOrFail();

                $amonth_summary= (new SubcontractorDataService())->processASubcontractorSingleMonthTotalInvoiceAndPaidAmount($payment['subcon_auto_id'],$payment['month'],$payment['year']);


            // Transform the response data if needed
            $responseData = [
                'payment' => $payment,
                'amonth_summary'=>$amonth_summary,
            ];

            return response()->json([
                'success' => true,
                'data' => $responseData
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcontractor payment not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subcontractor payment details',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function updatePaymentAPI(Request $request, $payment_id)
    {
        try {

            // Log::debug('Received data:', $request->all());

            // Log all request data as JSON
            // Log::info('Payment Update Request:', [
            //     'id' => $id,
            //     'input_data' => $request->all(),
            //     'files' => $request->files->all(),
            // ]);

            $payment = SubcontractorPayment::where('subcon_pay_auto_id', $payment_id)->firstOrFail();
            $request['payment_type'] = 'partial';
            $validator = $this->subcontractorDataService->PaymentRequestValidate($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                 //   'll'=>$validator->validated(),
                ], 422);
            }

            $validatedData = $validator->validated();


            // Log current file path before update
            $oldFilePath = $payment->payment_file;

            // Handle file upload if present
            $validatedData['payment_file'] = null;
            if ($request->hasFile('payment_file')) {
                // Store new file
                $validatedData['payment_file'] =  (new UploadDownloadController())->uploadSubcontractorPaymentFile($request->file('payment_file'),null);
            }

            // Set updated_by
            $validatedData['updated_by'] = Auth::id();
            $validatedData['approved_by'] = Auth::id();

            (new SubcontractorDataService())->updatePaymentInformation($payment_id,$validatedData);
            if( $validatedData['act_status'] == 10){
                (new SubcontractorDataService())->updateAPaymentRecordAsPaidConfirmation($payment_id,$validatedData);
            }


            // Load relationships for response
            $payment->load(['subcontractor', 'approvedUser', 'createdUser', 'updatedUser']);

            return response()->json([
                'success' => true,
                'message' => 'Payment updated successfully',
                'data' => [
                    'payment' => $payment
                ]
            ], Response::HTTP_OK);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update payment',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    public function destroyPaymentAPI($id)
    {
        try {
            $payment = SubcontractorPayment::where('subcon_pay_auto_id', $id)->firstOrFail();

            if ($payment->payment_file) {
                 (new UploadDownloadController())->deleteSubcontractorPaymentFile($payment->payment_file );
            }

            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Payment deleted successfully'
            ], Response::HTTP_OK);  // http code = 200

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], Response::HTTP_NOT_FOUND); // http code = 404

        } catch (\Exception $e) {
            Log::error('Error deleting payment: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete payment',
                'error'   => config('app.debug') ? $e->getMessage() : null
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // http code = 500
        }
    }
}
