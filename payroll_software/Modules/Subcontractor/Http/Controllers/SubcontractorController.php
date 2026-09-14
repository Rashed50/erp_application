<?php

namespace Modules\Subcontractor\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
// use App\Http\Controllers\Admin\Helper\UploadDownloadController;
use Modules\Subcontractor\Entities\SubcontractorInfo;
use Illuminate\Support\Facades\Storage;
use App\Models\Country;
use App\Models\Division;

// use App\Http\Controllers\DataServices\EmployeeDataService;
// use App\Http\Controllers\DataServices\CompanyDataService;
// use App\Http\Controllers\DataServices\SalaryProcessDataService;
use App\Http\Controllers\DataServices\EmployeeRelatedDataService;
use App\Http\Controllers\DataServices\ProjectDataService;

use Modules\Subcontractor\Services\SubcontractorDataService;

// Models
// use Modules\Subcontractor\Entities\SubcontractorInfo;
// use Modules\Subcontractor\Entities\SubcontractorService;
// use Modules\Subcontractor\Entities\SubcontractorPayment;

class SubcontractorController extends Controller
{
    protected $subcontractorService;
    //ALTER TABLE `subcontractor_infos` ADD `opening_balance` INT NOT NULL DEFAULT '0' AFTER `iqama_file`;

    public function __construct(SubcontractorDataService $subcontractorService)
    {
        $this->subcontractorService = $subcontractorService;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $countries = (new EmployeeRelatedDataService())->getAllCountryForDropdownList();
        $sponsors = (new EmployeeRelatedDataService())->getAllSponserInfoForDropdown();
        $projects = (new ProjectDataService())->getAllActiveProjectListForDropdown(1);
        $subcontractors = (new SubcontractorDataService())->getAllActiveSubcontractorsForDropdownList();

        // If no countries found, set divisions to empty array
        if (count($countries) == 0) {
            $divisions = [];
        } else
            $divisions = (new EmployeeRelatedDataService())->getDivisionByCountryId($countries[0]->id);
        return view('subcontractor::pages.subcontractor.index', [
            'data_for_subcontractor_form' => [
                'countries' => $countries,
                'divisions'      => $divisions,
                'sub_con_sponsors' => $sponsors,
                'projects' => $projects,
            ]
        ]);
    }





    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('subcontractor::pages.subcontractor.create');
    }





    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */

    public function store(Request $request)
    {

        // return response()->json(['message' => 'Subcontractor saved successfully!', 'data' => $request->all()], 201);


        $validator = $this->subcontractorService->validateData($request->all());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'data' => $request->all()], 422);
        }

        // Handle file uploads
        $data = $request->all();
        $iqama_file = null;
        if ($request->hasFile('iqama_file') && is_file($request->file('iqama_file'))) {
            $iqama_file = $this->subcontractorService->handleSingleFileUpload('sc', [$request->file('iqama_file')]);
        }
        $data['iqama_file'] = $iqama_file;

        $passport_file = null;
        if ($request->hasFile('passport_file') && is_file($request->file('passport_file'))) {
            $passport_file = $this->subcontractorService->handleSingleFileUpload('sc', [$request->file('passport_file')]);
        }
        $data['passport_file'] = $passport_file;

        $contract_paper = null;
        if ($request->hasFile('contract_paper') && is_file($request->file('contract_paper'))) {
            $contract_paper = $this->subcontractorService->handleSingleFileUpload('sc', [$request->file('contract_paper')]);
        }
        $data['contract_paper'] = $contract_paper;
        // Store subcontractor
        $subcontractor = $this->subcontractorService->storeSubcontractor($data);

        return response()->json(['message' => 'Subcontractor saved successfully!', 'data' => $subcontractor], 201);
    }






    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        try {
            $subcontractor = SubcontractorInfo::where('subcon_auto_id', $id)->firstOrFail();

            return response()->json([
                'success' => true,
                'data' => $subcontractor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcontractor not found'
            ], 404);
        }
    }





    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('subcontractor::edit');
    }






    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Subcontractor updated successfully',
        //     'data'    => $request->all()
        // ]);

        try {
            $subcontractor = SubcontractorInfo::where('subcon_auto_id', $id)->firstOrFail();
            if ($subcontractor == null) {
                return response()->json([
                    'success' => false,
                    'errors'  => 'Subcontractor not found',
                ], 404);
            }

            // Validate the request
            $validator = $this->subcontractorService->UpdateValidateData($request->all());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors'  => $validator->errors()
                ], 422);
            }

            // Prepare the data for update
            $updateData = $this->subcontractorService->prepareUpdateData($request->all());

            // Update the subcontractor
            $subcontractor->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Subcontractor updated successfully',
                'data'    => $subcontractor
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating subcontractor: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            $subcontractor = SubcontractorInfo::where('subcon_auto_id', $id)->firstOrFail();

            // Get file paths before deletion
            $filesToDelete = [
                $subcontractor->iqama_file,
                $subcontractor->passport_file,
                $subcontractor->contract_paper
            ];

            // Delete the subcontractor record
            $deleted = $subcontractor->delete();

            if ($deleted) {
                // Delete associated files from storage
                foreach ($filesToDelete as $file) {
                    if ($file && Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Subcontractor and associated files deleted successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subcontractor'
            ], 500);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Subcontractor not found'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting subcontractor: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting subcontractor',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    public function updateFiles(Request $request, $id)
    {
        $subcontractor = SubcontractorInfo::where('subcon_auto_id', $id)->firstOrFail();

        $request->validate([
            'iqama_file'      => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'passport_file'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'contract_paper'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
        ]);

        if ($request->hasFile('iqama_file')) {
            if ($subcontractor->iqama_file && Storage::disk('public')->exists($subcontractor->iqama_file)) {
                Storage::disk('public')->delete($subcontractor->iqama_file);
            }
            $iqama_file = $this->subcontractorService->handleSingleFileUpload('sc', [$request->file('iqama_file')]);
            $subcontractor->iqama_file = $iqama_file;
        }

        if ($request->hasFile('passport_file')) {
            if ($subcontractor->passport_file && Storage::disk('public')->exists($subcontractor->passport_file)) {
                Storage::disk('public')->delete($subcontractor->passport_file);
            }
            $passport_file = $this->subcontractorService->handleSingleFileUpload('sc', [$request->file('passport_file')]);
            $subcontractor->passport_file = $passport_file;
        }

        if ($request->hasFile('contract_paper')) {
            if ($subcontractor->contract_paper && Storage::disk('public')->exists($subcontractor->contract_paper)) {
                Storage::disk('public')->delete($subcontractor->contract_paper);
            }
            $contract_paper = $this->subcontractorService->handleSingleFileUpload('sc', [$request->file('contract_paper')]);
            $subcontractor->contract_paper = $contract_paper;
        }

        $subcontractor->save();

        return response()->json([
            'message' => 'Subcontractor files updated successfully.',
            'data' => $subcontractor,
        ]);
    }






    /*
        =============================================================
        ========================= API ===============================
        =============================================================
    */

    public function searchSubContractor(Request $request)
    {
        // try{
        // $perPage  = (int)$request->query('per_page', 10);
        // $page     = (int)$request->query('page', 1);
        $searching_value   = $request->query('searching_value', '');


        $subcontractors = $this->subcontractorService->searchSubcontractors($searching_value);
        return response()->json($subcontractors);
        //  }catch(exception $ex){
        //  response(context)
        return response()->json(404);
        //  }
    }


    public function getDivisionsByCountry($countryId)
    {
        // Validate that the country exists
        $country = Country::find($countryId);

        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }

        // Get divisions for the country
        $divisions = Division::where('country_id', $countryId)->get();

        return response()->json([
            'success' => true,
            'country' => $country->country_name,
            'divisions' => $divisions
        ]);
    }


    /*
        =============================================================
        ========================= Report ============================
        =============================================================
    */

    public function  processSubcontractorReport(Request $request) {}
}
