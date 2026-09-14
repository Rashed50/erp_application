<?php

namespace Modules\Expense\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controller;
use App\Models\EmployeeInfo;
use Modules\Expense\Models\Ticket;
use Modules\Account\Services\FileManager;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Http\Controllers\DataServices\CompanyDataService;
use App\Http\Controllers\Admin\Helper\UploadDownloadController;
// Get all permissions
// $permissions = Auth::user()->getAllPermissions();


class TicketController extends Controller
{

    // Permision
    function __construct()
    {
        $this->middleware('permission:ticket_list',     ['only' => ['index']]);
        $this->middleware('permission:ticket_add',      ['only' => ['create', 'store']]);
        $this->middleware('permission:ticket_edit',     ['only' => ['edit', 'update']]);
        $this->middleware('permission:ticket_approval', ['only' => ['updateApprovalStatus']]);
        $this->middleware('permission:ticket_delete',   ['only' => ['delete']]);
        $this->middleware('permission:ticket_search',   ['only' => ['searchTicketAPI']]);
    }


    public function index(Request $request)
    {
        $ticketForOptions = ['employee', 'others'];
        $ticketTypes   = ['one_way', 'return'];
        $paidByOptions = [
            2 => 'Company',
            1 => 'Self',
        ];

        return view('expense::pages.ticket.index', [
            'data' => [
                'ticketForOptions' => $ticketForOptions,
                'ticketTypes'      => $ticketTypes,
                'paidByOptions'    => $paidByOptions,
            ]
        ]);
    }


    public function create()
    {
        $ticketForOptions = ['employee', 'others'];
        $ticketTypes   = ['one_way', 'return'];
        $paidByOptions = [
            1 => 'Company',
            2 => 'Self',
        ];

        return view('expense::pages.ticket.create', [
            'data' => [
                'ticketForOptions' => $ticketForOptions,
                'ticketTypes'   => $ticketTypes,
                'paidByOptions' => $paidByOptions,
            ]
        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_for'     => 'required|in:employee,others',
            'ticket_type'    => 'nullable|in:one_way,return',
            'emp_auto_id'    => 'nullable|exists:employee_infos,emp_auto_id',
            'confirm_date'   => 'required|date',
            'qty'            => 'required|integer|min:1',
            'paid_by'        => 'required|in:1,2',
            'unit_price'     => 'required|numeric|min:0',
            'total_price'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle attachment uploads
        $attachmentPath = null;
        if ($request->hasFile('attachment') && is_file($request->file('attachment'))) {
            $attachmentPath =   (new UploadDownloadController())->uploadTicketFile($request->file('attachment'), null);
        }

        $employee = DB::table('employee_infos')
            ->where('emp_auto_id', $request->emp_auto_id)
            ->first();


        //$paid_by = 1;          //  2 = company
        // if ($employee && $employee->hourly_employee) {
        //     $paid_by = 1;     // self
        // }

        $confirmDate = \Carbon\Carbon::parse($request->confirm_date)->format('Y-m-d H:i:s');

        $ticket = Ticket::create([
            'ticket_for'    => $request->ticket_for,
            'emp_auto_id'   => $request->emp_auto_id,
            'ticket_type'   => $request->ticket_type,
            'paid_by'       => $request->paid_by,
            'ticket_number' => $request->ticket_number,
            'confirm_date'  => $confirmDate,
            'qty'           => $request->qty,
            'unit_price'    => $request->unit_price,
            'total_price'   => $request->total_price,
            'remarks'       => $request->remarks,
            'reference_by'  => $request->reference_by,
            'attachment'    => $attachmentPath ,//now single file,  is_array($attachmentPath) ? implode(',', $attachmentPath) : $attachmentPath,
            'created_by'    => auth()->user()->id,
            'updated_by'    => auth()->user()->id,
        ]);

        return response()->json(['success' => true, 'message' => 'Ticket created successfully!', 'ticket' => $ticket], 201);
    }

    public function update(Request $request, $id)
    {
        // Similar validation and update logic as the store method
        // You can implement this based on your requirements
    }

    public function delete($id)
    {
        try{

            $ticket = Ticket::find($id);

            if (!$ticket) {
                return response()->json(['success' => false,'message' => 'Ticket not found.','status' => 404,'error' => 'Record not found'], 404);
            }

            // Optionally, delete the associated attachment file
            if ($ticket->attachment) {
                $attachmentPath =   (new UploadDownloadController())->deleteTicketFile($ticket->attachment);
            }

            $ticket->delete();

            return response()->json(['message' => 'Record deleted successfully.','status' => 200,'success' => true], 200);
        }catch(\Exception $e){
            Log::error('Error deleting ticket: ' . $e->getMessage());
            return response()->json(['success' => false,'message' => 'An error occurred while deleting the Record.','status' => 500,'error' => $e->getMessage()], 500);
        }
    }


    public function list_index(){
        return view('expense::pages.ticket.list');
    }



    public function listAPI(Request $request)
    {
        $perPage  = (int)$request->query('per_page', 100);
        $page     = (int)$request->query('page', 1);
        $search   = $request->query('search', '');
        $type     = $request->query('ticket_type', null);
        $download = (int)$request->query('is_download', 0);

        $selectedDate = $request->query('selectedDate', null);
        $dateFrom = $request->query('dateFrom', null);
        $dateTo   = $request->query('dateTo', null);

        // Initial query
        $query = Ticket::with(['employeeInfo' => function ($query) {
            $query->select('employee_id', 'emp_auto_id', 'employee_name', 'passfort_no','akama_no');
        }])
        ->where('is_approved', 0)
        ->orderBy('created_at', 'desc');

        // Filter by ticket type
        if (!empty($type)) {
            $query->where('ticket_type', $type);
        }

        // Filter by date range based on the selected date type
        if ($selectedDate) {
            if ($dateFrom) {
                switch ($selectedDate) {
                    case 'confirm_date':
                        $query->whereDate('confirm_date', '>=', $dateFrom);
                        break;
                    case 'created_at':
                        $query->whereDate('created_at', '>=', $dateFrom);
                        break;
                }
            }

            if ($dateTo) {
                switch ($selectedDate) {
                    case 'confirm_date':
                        $query->whereDate('confirm_date', '<=', $dateTo);
                        break;
                    case 'created_at':
                        $query->whereDate('created_at', '<=', $dateTo);
                        break;
                }
            }
        }

        // Search by ticket number or employee details
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhereHas('employeeInfo', function ($employeeQuery) use ($search) {
                        $employeeQuery->where('emp_name', 'like', "%{$search}%")
                            ->orWhere('emp_email', 'like', "%{$search}%")
                            ->orWhere('emp_address', 'like', "%{$search}%");
                    });
            });
        }

        // Check if the request is to download the data as a PDF
        if ($download === 1) {
            $tickets = $query->get();

            // Calculate totals
            $totals = [
                'total_qty' => $tickets->sum('qty'),
                'total_unit_price' => $tickets->sum('unit_price'),
                'total_price' => $tickets->sum('total_price')
            ];

            $pdf = PDF::loadView('expense::tickets.report.list_pdf', [
                'tickets' => $tickets,
                'totals' => $totals
            ]);

            return $pdf->stream('ticket_report_' . date('Y-m-d_H-i-s') . '.pdf');
        }

        // Paginate the data
        $tickets = $query->paginate($perPage, ['*'], 'page', $page);
        return response()->json($tickets);
    }


    public function updateApprovalStatus($ticketId, Request $request)
    {
        $request->validate([
            'is_approved' => 'required|boolean',
        ]);

        $ticket = Ticket::find($ticketId);

        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found.'
            ], 404);
        }

        $ticket->is_approved = $request->is_approved;
        $ticket->approved_by = auth()->id();
        $ticket->save();

        return response()->json([
            'message' => 'Ticket approval status updated successfully.',
            'ticket' => $ticket
        ], 200);
    }


    public function searchTicketAPI(Request $request)
    {
        $selectedDate = 'created';
        $employeeId = $request->query('employeeId', null);
        $passportId = $request->query('passportId', null);
        $dateFrom = $request->query('dateFrom', null);
        $dateTo = $request->query('dateTo', null);

        $query = Ticket::with([
            'employeeInfo' => function ($query) {
                $query->select('employee_id', 'emp_auto_id', 'employee_name', 'passfort_no','akama_no');
            },
            'approvedBy' => function ($query) {
                $query->select('id', 'name', 'email');
            }
        ])->orderBy('created_at', 'desc');


        // Filter by employee ID
        if ($employeeId) {
            $query->whereHas('employeeInfo', function ($q) use ($employeeId) {
                $q->where('employee_id', $employeeId);
            });
        }

        // Filter by passport ID
        if ($passportId) {
            $query->whereHas('employeeInfo', function ($q) use ($passportId) {
                $q->where('passfort_no', $passportId);
            });
        }

        // Filter by date range
        if ($dateFrom && $dateTo) {
            $query->whereBetween('created_at', [$dateFrom, $dateTo]);
        } elseif ($dateFrom) {
            $query->where('created_at', '>=', $dateFrom);
        } elseif ($dateTo) {
            $query->where('created_at', '<=', $dateTo);
        }

        $tickets = $query->get();

        return response()->json([
            'status' => 200,
            'data' => $tickets
        ]);
    }

    // public function exportToPdf(Request $request)
    // {
    //     $from = $request->query('from');
    //     $to   = $request->query('to');
    //     $type = $request->query('type');

    //     $request->validate([
    //         'from' => 'required|date',
    //         'to' => 'required|date',
    //         'type' => 'required|in:1,2',
    //     ]);

    //     $tickets = Ticket::with(['employeeInfo', 'createdBy', 'updatedBy'])
    //     ->when($from && $to, function ($query) use ($from, $to) {
    //         $query->whereBetween('created_at', [$from, $to]);
    //     })
    //         ->orderBy('created_at', 'desc')
    //         ->get();

    //     // Prepare Data for the Report
    //     $data = [];
    //     $totalAmount = 0;

    //     foreach ($tickets as $key => $ticket) {
    //         $data[] = [
    //             'S/N'            => $key + 1,
    //             'Employee_ID'    => $ticket->employeeInfo->employee_id ?? '-',
    //             'Employee_Name'  => $ticket->employeeInfo->employee_name ?? '-',
    //             'Passport_No'    => $ticket->employeeInfo->passfort_no ?? '-',
    //             'Ticket_Date'    => Carbon::parse($ticket->confirm_date)->format('d/m/Y'),
    //             'Ticket_Number'  => $ticket->ticket_number,
    //             'Ticket_Type'    => $ticket->ticket_type,
    //             'Reference_By'   => $ticket->reference_by,
    //             'Quantity'       => $ticket->qty,
    //             'Unit_Price'     => $ticket->unit_price,
    //             'Total_Price'    => $ticket->total_price,
    //             'Remarks'        => $ticket->remarks,
    //             'Created_By'     => $ticket->createdBy->name ?? '-',
    //             'Updated_By'     => $ticket->updatedBy->name ?? '-',
    //         ];

    //         $totalAmount += $ticket->total_price;
    //     }

    //     $company = (new CompanyDataService())->findCompanryProfile();
    //     $view = $type == 1
    //         ? 'expense::pages.report.ticket_list'
    //         : 'expense::pages.report.ticket_summary';

    //     return view($view, [
    //         'items'       => $data,
    //         'totalAmount' => $totalAmount,
    //         'company' => $company,
    //         'from'    => Carbon::parse($from)->format('d/m/Y'),
    //         'to'      => Carbon::parse($to)->format('d/m/Y'),
    //     ]);
    // }


    public function exportToPdf(Request $request)
    {
        $from = $request->query('from');
        $to   = $request->query('to');
        $type = $request->query('type');

        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
            'type' => 'required|in:1,2',
        ]);

        $tickets = Ticket::with(['employeeInfo'])
            ->when($from && $to, function ($query) use ($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        if ($type == 2) {
            // Prepare summary data
            $summary = $tickets->groupBy(function ($ticket) {
                return Carbon::parse($ticket->created_at)->format('F, Y');
            })->map(function ($group) {
                return [
                    'month_year' => Carbon::parse($group->first()->created_at)->format('F, Y'),
                    'no_of_employees' => $group->unique('employeeInfo.employee_id')->count(),
                    'no_of_tickets' => $group->count(),
                    'total_amount' => $group->sum('total_price'),
                ];
            });

            $totalEmp = $summary->sum('no_of_employees');
            $totalTicket = $summary->sum('no_of_tickets');
            $totalAmount = $summary->sum('total_amount');
        } else {
            // Prepare detailed data
            $data = [];
            $totalAmount = 0;

            foreach ($tickets as $key => $ticket) {
                $data[] = [
                    'S/N'            => $key + 1,
                    'Employee_ID'    => $ticket->employeeInfo->employee_id ?? '-',
                    'Employee_Name'  => $ticket->employeeInfo->employee_name ?? '-',
                    'Passport_No'    => $ticket->employeeInfo->passfort_no ?? '-',
                    'Ticket_Date'    => Carbon::parse($ticket->confirm_date)->format('d/m/Y'),
                    'Ticket_Number'  => $ticket->ticket_number,
                    'Ticket_Type'    => $ticket->ticket_type,
                    'Reference_By'   => $ticket->reference_by,
                    'Quantity'       => $ticket->qty,
                    'Unit_Price'     => $ticket->unit_price,
                    'Total_Price'    => $ticket->total_price,
                    'Remarks'        => $ticket->remarks,
                ];

                $totalAmount += $ticket->total_price;
            }
        }

        $company = (new CompanyDataService())->findCompanryProfile();
        $view = $type == 1
            ? 'expense::pages.report.ticket_list'
            : 'expense::pages.report.ticket_summary';

        return view($view, [
            'items'       => $type == 1 ? $data : $summary,
            'totalEmp'    => $type == 2 ? $totalEmp : null,
            'totalTicket' => $type == 2 ? $totalTicket : null,
            'totalAmount' => $totalAmount,
            'company'     => $company,
            'from'        => Carbon::parse($from)->format('d/m/Y'),
            'to'          => Carbon::parse($to)->format('d/m/Y'),
        ]);
    }


}
