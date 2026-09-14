<?php

namespace Modules\Payroll\Http\Controllers\Salary;

use Modules\Payroll\Entities\PartialSalary;
use App\Models\EmployeeInfo;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\DataServices\{SalaryProcessDataService,EmployeeRelatedDataService,ProjectDataService,CompanyDataService};
use Modules\Payroll\Services\PartialSalaryService;
use Modules\Payroll\Imports\PartialSalaryImport;

class PartialSalaryController extends Controller
{
    protected $partialSalaryService;

    /**
     * Constructor for Dependency Injection and Middleware
     */
    public function __construct()
    {
        // 1. Apply Middleware to all methods in this controller
        // $this->middleware('auth');

        // Apply Middleware only to specific methods (like store/delete)
        $this->middleware('permission:partial_salary_add')->only([ 'store']);
        $this->middleware('permission:partial_salary_search')->only(['index', 'search']);
        $this->middleware('permission:partial_salary_update')->only(['show', 'search']);
        $this->middleware('permission:partial_salary_delete')->only(['destroy']);
        $this->middleware('permission:partial_salary_bulk_upload',['only'=>['previewExcel','importExcel']]);
        $this->partialSalaryService = new PartialSalaryService();
    }


    public function index()
    {

        $projects =  (new ProjectDataService())->getAllActiveProjectListForDropdown();
        return view('payroll::pages.Payroll.partial_salary_index', compact('projects'));
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2000',
            'project_id' => 'nullable|integer|exists:project_infos,proj_id',
            'employee_id' => 'nullable|integer|exists:employee_infos,employee_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $records = $this->partialSalaryService->searchPartialSalaries($request);
        return response()->json($records);
    }


    // now excel downloading from searching result from UI, next time when used from server then use this api
     public function searchAndDownloadPatialSalary(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'month' => 'nullable|integer|between:1,12',
            'year' => 'nullable|integer|min:2000',
            'project_id' => 'nullable|integer|exists:project_infos,proj_id',
            'emp_auto_id' => 'nullable|integer|exists:employee_infos,emp_auto_id'
        ]);



        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = PartialSalary::with(['employee', 'project']);

        if ($request->filled('month')) {
            $query->where('month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('year', $request->year);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('emp_auto_id')) {
            $query->where('emp_auto_id', $request->emp_auto_id);
        }

        $records = $query->orderBy('year', 'desc')
                        ->orderBy('month', 'desc')
                        ->orderBy('psh_auto_id', 'desc')
                        ->get();


    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'emp_auto_id' => 'required|integer|exists:employee_infos,emp_auto_id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:' . (date('Y') - 1) . '|max:' . (date('Y') + 1),
            'amount' => 'required|integer|min:0',
            'paid_at' => 'required|date',
            'project_id' => 'required|integer|exists:project_infos,proj_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['inserted_by'] = Auth::id();

        $record = $this->partialSalaryService->storePartialSalary($data);

        return response()->json([
            'message' => 'Partial Salary Saved successfully',
            'data' => $record,
            'form_data' => $data
        ], 201);
    }

    public function show($id)
    {
        $record = PartialSalary::with(['employee', 'project'])->findOrFail($id);
        return response()->json($record);
    }

    public function update(Request $request, $id)
    {
        $record = PartialSalary::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'emp_auto_id' => 'required|integer|exists:employee_infos,emp_auto_id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000|max:' . (date('Y') + 10),
            'amount' => 'required|integer|min:0',
            'paid_at' => 'required|date',
            'project_id' => 'required|integer|exists:project_infos,proj_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $record->update($data);
        $record->load(['employee', 'project']);

        return response()->json([
            'message' => 'Partial salary history updated successfully',
            'data' => $record,  'form_data' => $data
        ]);
    }

    public function destroy($id)
    {
        $record = PartialSalary::findOrFail($id);
        $record->delete();

        return response()->json([
            'message' => 'Partial salary history deleted successfully'
        ]);
    }



    ////////////////////////////////////////////// Excel Upload Section ///////////////////////

public function previewExcel(Request $request)
{
    $validator = Validator::make($request->all(), [
       // 'project_id' => 'required|integer|exists:project_infos,proj_id',
        'month' => 'required|integer|between:1,12',
        'year' => 'required|integer|min:' . (date('Y') - 1) . '|max:' . (date('Y') + 1),
        'paid_at' => 'required|date',
        'file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {
        $file = $request->file('file');
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows)) {
            return response()->json(['error' => 'File is empty'], 400);
        }

        // Get headers
        $headers = array_shift($rows);
        $expectedHeaders = ['employee_id', 'amount'];

        // Validate headers
        $normalizedHeaders = array_map('strtolower', $headers);
        foreach ($expectedHeaders as $expected) {
            if (!in_array($expected, $normalizedHeaders)) {
                return response()->json([
                    'error' => "Missing required column: {$expected}. Found columns: " . implode(', ', $headers)
                ], 400);
            }
        }

        // Process preview data
        $previewData = [];
        $employees = EmployeeInfo::select('emp_auto_id','employee_name', 'employee_id','akama_no')->get();
        $errors = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;
            $employeeId = $row[array_search('employee_id', $normalizedHeaders)];
            $amount = $row[array_search('amount', $normalizedHeaders)];
            $result = $employees->firstWhere('employee_id', $employeeId);
            $rowData = [
                'row_number' => $rowNumber,
                'employee_id' => $employeeId,
                'emp_auto_id' => $result ? $result->emp_auto_id : 'Not Found',
                'employee_name' => $result ? $result->employee_name : 'Not Found',
                'akama_no' => $result ? $result->akama_no : 'Not Found',
                'amount' => $amount,
                'is_valid' => true,
                'error_message' => null
            ];

            // Validate
            if (!isset($result)) {
                $rowData['is_valid'] = false;
                $rowData['error_message'] = "Employee ID not found";
                $errors[] = "Row {$rowNumber}: Employee ID '{$employeeId}' not found";
            }

            if (!is_numeric($amount) || $amount < 0) {
                $rowData['is_valid'] = false;
                $rowData['error_message'] = $rowData['error_message']
                    ? $rowData['error_message'] . ". Invalid amount"
                    : "Invalid amount";
                $errors[] = "Row {$rowNumber}: Invalid amount '{$amount}'";
            }

            $previewData[] = $rowData;
        }

        return response()->json([
            'preview_data' => $previewData,
            'total_rows' => count($rows),
            'valid_rows' => count(array_filter($previewData, function($item) {
                return $item['is_valid'] === true;
            })),
            'invalid_rows' => count(array_filter($previewData, function($item) {
                return $item['is_valid'] === false;
            })),
            'errors' => $errors
        ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Error processing file: ' . $e->getMessage()], 500);
    }
}

public function importExcel(Request $request)
{
    $validator = Validator::make($request->all(), [
    //    'project_id' => 'required|integer|exists:project_infos,proj_id',
        'month' => 'required|integer|between:1,12',
        'year' => 'required|integer|min:' . (date('Y') - 1) . '|max:' . (date('Y') + 1),
        'paid_at' => 'required|date',
        'file' => 'required|file|mimes:xlsx,xls,csv|max:5120'
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    try {


            $import = new PartialSalaryImport(
                $request->project_id,
                $request->month,
                $request->year,
                $request->paid_at
            );

            Excel::import($import, $request->file('file'));
            $errors = $import->getErrors();
            $successCount = $import->getSuccessCount();

            return response()->json([
                'message' => "Import completed. Successfully imported: {$successCount} records",
                'success_count' => $successCount,
                'error_count' => count($errors),
                'errors' => $errors
            ]);

    } catch (\Exception $e) {
        return response()->json(['error' => 'Import failed: ' . $e->getMessage()], 500);
    }
}

public function downloadSampleExcel()
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Set headers
    $sheet->setCellValue('A1', 'employee_code');
    $sheet->setCellValue('B1', 'amount');

    // Add sample data
    $sheet->setCellValue('A2', 'EMP001');
    $sheet->setCellValue('B2', '5000');

    $sheet->setCellValue('A3', 'EMP002');
    $sheet->setCellValue('B3', '7500');

    // Style headers
    $sheet->getStyle('A1:B1')->getFont()->setBold(true);
    $sheet->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

    // Auto-size columns
    foreach (range('A', 'B') as $col) {
        $sheet->getColumnDimension($col)->setAutoSize(true);
    }

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="partial_salary_sample.xlsx"');
    $writer->save('php://output');
    exit;
}

    private function getMonths()
    {
        return [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];
    }
}
