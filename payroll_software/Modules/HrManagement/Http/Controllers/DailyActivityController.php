<?php

namespace Modules\HrManagement\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\{Auth, Session, Log, DB, Storage};

//? Helper Imports
use App\Helpers\FileUploadHelper;

//? Model Imports 
use Modules\HrManagement\Entities\DailyActivityType;
use Modules\HrManagement\Entities\DailyActivity;
use Modules\HrManagement\Entities\ResponsibleEmployee;

//? Request Validation Imports
use Modules\HrManagement\Http\Requests\CreateDailyActivityRequest;


class DailyActivityController extends Controller
{

    public function index()
    {
        $activity_types = DailyActivityType::all();

        $responsibles_emp = ResponsibleEmployee::getEmployees();

        return view('hrmanagement::pages.DailyActivity.activity', [
            'data' => [
                'activity_types'   => $activity_types,
                'responsibles_emp' => $responsibles_emp
            ]
        ]);
    }

    public function store(CreateDailyActivityRequest $request)
    {
        try {
            DB::beginTransaction();

            // Handle file upload
            $filePath = FileUploadHelper::uploadFile(
                $request->file('da_attached_file'),
                null,               //? old file path (for updates)
                'DailyActivities',  //? Provided Folder Path
                5,                  //? File Size MAX 5MB
                [                   //? allowed mime types
                    'image/jpeg',
                    'image/png',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                ]
            );

            $activityId = DB::table('daily_activities')->insertGetId([
                'da_subject'         => $request->da_subject,
                'da_details'         => $request->da_details,
                'da_type_id'         => $request->da_type_id,
                'da_for_emp_id'      => $request->da_for_emp_id,
                'da_created_by'      => auth()->id(),
                'da_responsible_emp' => $request->da_responsible_emp,
                'da_status'          => 1,
                'da_progress'        => 1,
                'da_attached_file'   => $filePath,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Daily activity created successfully',
                'data' => [
                    'id'  => $activityId,
                    'file_url' => $filePath ? FileUploadHelper::getFileUrl($filePath) : null
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            if (isset($filePath)) {
                FileUploadHelper::deleteFile($filePath);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to create daily activity',
                'error'   => $e->getMessage()
            ], 500);
        }
    }



    public function list(Request $request)
    {
        try {
            $query = DB::table('daily_activities')
                ->select(
                    'daily_activities.*',
                    'creator.name as created_by_name',
                    'employee.employee_name as for_emp_name',
                    // 'responsible.employee_name as responsible_emp_name', 
                    'type.da_type_name'
                )
                ->leftJoin('users as creator', 'daily_activities.da_created_by', '=', 'creator.id')
                ->leftJoin('employee_infos as employee', 'daily_activities.da_for_emp_id', '=', 'employee.emp_auto_id')
                // ->leftJoin('employee_infos as responsible', 'daily_activities.da_responsible_emp', '=', 'responsible.emp_auto_id')
                ->leftJoin('daily_activities_type as type', 'daily_activities.da_type_id', '=', 'type.id');

            // Search functionality
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = '%' . $request->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('daily_activities.da_subject', 'like', $searchTerm)
                        ->orWhere('daily_activities.da_details', 'like', $searchTerm)
                        ->orWhere('daily_activities.da_status_remarks', 'like', $searchTerm)
                        ->orWhere('employee.employee_name', 'like', $searchTerm)
                        ->orWhere('type.da_type_name', 'like', $searchTerm);
                });
            }

            // Filter by created by
            // if ($request->has('da_created_by') && !empty($request->da_created_by)) {
            //     $query->where('daily_activities.da_created_by', $request->da_created_by);
            // }

            // Filter by responsible employee
            // if ($request->has('da_responsible_emp') && !empty($request->da_responsible_emp)) {
            //     $query->where('daily_activities.da_responsible_emp', $request->da_responsible_emp);
            // }

            // Filter by type
            // if ($request->has('da_type_id') && !empty($request->da_type_id)) {
            //     $query->where('daily_activities.da_type_id', $request->da_type_id);
            // }

            // Filter by for employee
            // if ($request->has('da_for_emp_id') && !empty($request->da_for_emp_id)) {
            //     $query->where('daily_activities.da_for_emp_id', $request->da_for_emp_id);
            // }

            // Date range filter (optional)
            // if ($request->has('start_date') && !empty($request->start_date)) {
            //     $query->whereDate('daily_activities.created_at', '>=', $request->start_date);
            // }
            // if ($request->has('end_date') && !empty($request->end_date)) {
            //     $query->whereDate('daily_activities.created_at', '<=', $request->end_date);
            // }

            // Sorting
            // $sortField = $request->has('sort_field') ? $request->sort_field : 'created_at';
            // $sortOrder = $request->has('sort_order') ? $request->sort_order : 'desc';
            // $query->orderBy($sortField, $sortOrder);

            // // Pagination
            // $perPage = $request->has('per_page') ? (int)$request->per_page : 15;
            // $activities = $query->paginate($perPage);


            // Pagination
            $perPage = $request->per_page ?? 10; // Default to 10 items per page
            $currentPage = $request->page ?? 1;

            // Get total count
            $total = $query->count();

            // Apply pagination
            $results = $query->paginate($perPage, ['*'], 'page', $currentPage);

            // Responsible employee তথ্য যোগ করুন
            $activities = $results->items();
            foreach ($activities as $activity) {
                if ($activity->da_responsible_emp) {
                    $responsibleEmployee = ResponsibleEmployee::findById($activity->da_responsible_emp);

                    if ($responsibleEmployee) {
                        $activity->responsible_emp_name = $responsibleEmployee->employee_name;
                        $activity->responsible_emp_id = $responsibleEmployee->employee_id;
                    }
                }
            }

            return response()->json([
                'success'    => true,
                'message'    => 'Daily activities retrieved successfully',
                'data'       => $activities,
                'pagination' => [
                    'current_page'  => $results->currentPage(),
                    'last_page'     => $results->lastPage(),
                    'per_page'      => $results->perPage(),
                    'total'         => $results->total(),
                    'from'          => $results->firstItem(),
                    'to'            => $results->lastItem(),
                    'prev_page_url' => $results->previousPageUrl(),
                    'next_page_url' => $results->nextPageUrl(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve daily activities',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            Log::info($id);
            Log::info($request->all());

            $activity = DailyActivity::findOrFail($id);

            $activity->update([
                'da_responsible_emp' => $request->da_responsible_emp,
                'da_status' => $request->da_status,
                'da_status_remarks' => $request->da_status_remarks,
                'updated_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Daily activity updated successfully',
                'data' => $activity
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update daily activity',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
