@extends('layouts.admin-master')
@section('title', 'Employee Salary Sheet')
@section('content')

<style>
    :root {
        --bs-primary: #007bff;
        --bs-secondary: #6c757d;
        --bs-success: #28a745;
        --bs-danger: #dc3545;
        --bs-info: #17a2b8;
    }

    .container-fluid {
        padding: 0 15px;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1rem;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .form-label {
        font-weight: 500;
        color: #34495e;
        margin-bottom: 0.5rem;
        display: block;
    }

    .form-control, .form-select {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        transition: border-color 0.15s ease-in-out;
    }

    .form-control:focus, .form-select:focus {
        border-color: #80bdff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .card {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .card-header {
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem;
        background-color: #f8f9fa;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .btn-group {
        gap: 0.5rem;
    }

    .btn {
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-primary {
        background-color: #007bff;
        color: white;
    }

    .btn-primary:hover {
        background-color: #0056b3;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #545b62;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #218838;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #c82333;
    }

    .btn-info {
        background-color: #17a2b8;
        color: white;
    }

    .btn-info:hover {
        background-color: #138496;
    }

    .badge {
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        font-weight: 500;
        display: inline-block;
    }

    .badge-primary {
        background-color: #007bff;
        color: white;
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .table-light thead {
        background-color: black;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .alert {
        border-radius: 0.375rem;
        border: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .d-none {
        display: none !important;
    }

    .d-block {
        display: block !important;
    }

    .d-flex {
        display: flex !important;
    }

    .flex-wrap {
        flex-wrap: wrap !important;
    }

    .d-grid {
        display: grid !important;
    }

    .gap-2 {
        gap: 0.5rem !important;
    }

    .text-muted {
        color: #6c757d;
    }

    .text-danger {
        color: #dc3545;
    }

    @media (max-width: 768px) {
        .btn-group {
            flex-direction: column;
        }

        .btn-group .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .col-lg-6 {
            margin-bottom: 1.5rem;
        }
    }
</style>

<div class="container-fluid mt-4">

    {{-- Session Messages --}}
    @if(Session::has('success'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-check-circle"></i></strong> {{ Session::get('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    @if(Session::has('error'))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <strong><i class="fas fa-exclamation-triangle"></i></strong> {{ Session::get('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Navigation Buttons --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="btn-group d-flex flex-wrap" role="group">
                        <button
                            type="button"
                            onclick="showAddSalarySheetSection()"
                            class="btn btn-primary waves-effect"
                            id="btnAddSalarySheet"
                        >
                            <i class="fas fa-plus"></i> Add Salary Sheet
                        </button>

                        <button
                            type="button"
                            onclick="showSearchSalarySheetSection()"
                            class="btn btn-secondary waves-effect"
                            id="btnSearchSalarySheet"
                        >
                            <i class="fas fa-search"></i> Search Salary Sheet
                        </button>

                        @can('emp_mobile_bill_paper_upload')
                            <button
                                type="button"
                                onclick="showMobileBillUploadSection()"
                                class="btn btn-secondary waves-effect"
                                id="btnMobileBillUpload"
                            >
                                <i class="fas fa-mobile-alt"></i> Mobile Bill Upload
                            </button>
                        @endcan

                        @can('emp_mobile_bill_paper_show')
                            <button
                                type="button"
                                onclick="showMobileBillSearchSection()"
                                class="btn btn-secondary waves-effect"
                                id="btnMobileBillSearch"
                            >
                                <i class="fas fa-search"></i> Search Mobile Paper
                            </button>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Salary Sheet Section --}}
    <div class="row d-none" id="addSalarySheetSection">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-lg-6">
                            <form method="post" action="{{ route('salary-sheet.store') }}" enctype="multipart/form-data" id="salarySheetForm">
                                @csrf

                                <div class="form-group mb-3">
                                    <label class="form-label">Employee ID <span class="text-muted">(Optional)</span></label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        name="no_of_emp"
                                        placeholder="Enter Employee ID"
                                    >
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Salary Month <span class="text-danger">*</span></label>
                                    <select class="form-select" name="month" required>
                                        <option value="">Select Month</option>
                                        @foreach($month as $item)
                                            <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                                {{ $item->month_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Salary Year <span class="text-danger">*</span></label>
                                    <select class="form-select" name="year" required>
                                        <option value="">Select Year</option>
                                        @foreach(range(date('Y'), date('Y')-4) as $y)
                                            <option value="{{ $y }}">{{ $y }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Salary Date <span class="text-danger">*</span></label>
                                    <input
                                        type="date"
                                        name="salary_date"
                                        value="{{ date('Y-m-d') }}"
                                        class="form-control"
                                        required
                                    >
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label">Remarks</label>
                                    <textarea
                                        class="form-control"
                                        name="remarks"
                                        rows="3"
                                        placeholder="Enter any remarks"
                                    ></textarea>
                                </div>

                                <div class="form-group mb-4">
                                    <label class="form-label">Upload File <span class="text-danger">*</span></label>
                                    <input
                                        type="file"
                                        class="form-control"
                                        name="file_name"
                                        id="fileInput"
                                        required
                                        accept=".jpg,.jpeg,.png,.gif,.pdf"
                                    >
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-upload"></i> Submit
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Quick Info Section --}}
                        <div class="col-lg-6">
                            <div class="alert alert-info">
                                <h6 class="alert-heading mb-2">
                                    <i class="fas fa-info-circle"></i> Instructions
                                </h6>
                                <ul class="mb-0 small">
                                    <li>Select the appropriate month and year</li>
                                    <li>Set the salary payment date</li>
                                    <li>Upload the salary file in image or PDF format</li>
                                    <li>Optional: Add employee ID or remarks</li>
                                    <li>Click Submit to process the file</li>
                                </ul>
                            </div>
                            <div id="preview" class="mt-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search Salary Sheet Section --}}
    <div class="row d-none" id="searchSalarySheetSection">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    {{-- Search Form --}}
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="mb-3">Search Salary Sheets</h6>
                                    <form id="searchSalarySheetForm">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Employee ID</label>
                                                    <input
                                                        type="number"
                                                        id="searchEmployeeId"
                                                        class="form-control"
                                                        placeholder="Optional"
                                                    >
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Month</label>
                                                    <select class="form-select" id="searchMonth">
                                                        <option value="">Select Month</option>
                                                        @foreach($month as $item)
                                                            <option value="{{ $item->month_id }}">{{ $item->month_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Year</label>
                                                    <select class="form-select" id="searchYear">
                                                        <option value="">Select Year</option>
                                                        @foreach(range(date('Y'), date('Y')-4) as $y)
                                                            <option value="{{ $y }}">{{ $y }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">Date</label>
                                                    <input
                                                        type="date"
                                                        id="searchDate"
                                                        class="form-control"
                                                    >
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group mb-3">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button
                                                        type="button"
                                                        class="btn btn-primary w-100"
                                                        onclick="searchSalarySheets()"
                                                    >
                                                        <i class="fas fa-search"></i> Search
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Search Results Table --}}
                    <div class="row">
                        <div class="col-12">
                            <h6 class="mb-3">Salary Sheets</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover custom_table mb-0">
                                    <thead  style="background-color: black; color: white;">
                                        <tr>
                                            <th style="width: 5%">S.N</th>
                                            <th style="width: 15%">Employee ID</th>
                                            <th style="width: 20%">Month & Year</th>
                                            <th style="width: 15%">Salary Date</th>
                                            <th style="width: 25%">Remarks</th>
                                            <th style="width: 20%">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="salarySheetTableBody">
                                        {{-- Results will be populated here via JavaScript --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Salary Sheet Modal --}}
    <div class="modal fade" id="editSalarySheetModal" tabindex="-1" role="dialog" aria-labelledby="editSalarySheetLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSalarySheetLabel">
                        <i class="fas fa-edit"></i> Edit Salary Sheet
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editSalarySheetForm">
                        @csrf
                        <input type="hidden" id="editRecordId" name="record_id">

                        <div class="form-group mb-3">
                            <label class="form-label">Employee ID <span class="text-muted">(Optional)</span></label>
                            <input
                                type="number"
                                class="form-control"
                                id="editNoOfEmp"
                                name="no_of_emp"
                                placeholder="Enter Employee ID"
                            >
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Salary Month <span class="text-danger">*</span></label>
                            <select class="form-select" id="editMonth" name="month" required>
                                <option value="">Select Month</option>
                                @foreach($month as $item)
                                    <option value="{{ $item->month_id }}">{{ $item->month_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Salary Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="editYear" name="year" required>
                                <option value="">Select Year</option>
                                @foreach(range(date('Y'), date('Y')-4) as $y)
                                    <option value="{{ $y }}">{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Salary Date <span class="text-danger">*</span></label>
                            <input
                                type="date"
                                id="editSalaryDate"
                                name="salary_date"
                                class="form-control"
                                required
                            >
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Remarks</label>
                            <textarea
                                class="form-control"
                                id="editRemarks"
                                name="remarks"
                                rows="3"
                                placeholder="Enter any remarks"
                            ></textarea>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                <strong>Note:</strong> File cannot be changed through edit.
                                Delete and re-upload if you need to change the file.
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <button type="button" class="btn btn-success" id="saveEditBtn" onclick="saveSalarySheetEdit()">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Mobile Bill Upload Section --}}
    @can('emp_mobile_bill_paper_upload')
        <div class="row d-none" id="mobileBillUploadSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <form id="employeeMobileBillStoreForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="operation_type" value="1">

                                    <div class="form-group mb-3">
                                        <label class="form-label">Year <span class="text-danger">*</span></label>
                                        <select class="form-select" name="bill_year" id="bill_year" required>
                                            @foreach(range(date('Y'), date('Y')-1) as $y)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Month <span class="text-danger">*</span></label>
                                        <select class="form-select" name="bill_month" id="bill_month" required>
                                            <option value="">Select Month</option>
                                            @foreach($month as $item)
                                                <option value="{{ $item->month_id }}">{{ $item->month_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Project <span class="text-danger">*</span></label>
                                        <select class="form-select" name="bill_project_id" id="bill_project_id" required>
                                            <option value="">Select Project</option>
                                            @foreach($projectlist as $proj)
                                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label">Upload File <span class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="bill_paper" id="imgInp3" required accept=".pdf,.jpg,.jpeg,.png">
                                        <small class="form-text text-muted d-block mt-2">Accepted formats: PDF, JPG, PNG</small>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-success" id="employee-mobile-bill-submit-button">
                                            <i class="fas fa-upload"></i> Upload Bill
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <div class="col-lg-6">
                                <div id="uploadPreview">
                                    <img id="img-upload3" class="img-fluid rounded" style="display: none; max-height: 300px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan

    {{-- Mobile Bill Search Section --}}
    @can('emp_mobile_bill_paper_show')
        <div class="row d-none" id="mobileBillSearchSection">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-lg-6">
                                <form id="searching_mobile_bill_paper_form">
                                    @csrf
                                    <input type="hidden" name="operation_type" value="2">

                                    <div class="form-group mb-3">
                                        <label class="form-label">Year <span class="text-danger">*</span></label>
                                        <select class="form-select" name="year" id="search_year" required>
                                            @foreach(range(date('Y'), date('Y')-2) as $y)
                                                <option value="{{ $y }}">{{ $y }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label class="form-label">Month <span class="text-danger">*</span></label>
                                        <select class="form-select" name="month" id="search_month" required>
                                            <option value="">Select Month</option>
                                            @foreach($month as $item)
                                                <option value="{{ $item->month_id }}" {{ $item->month_id == Carbon\Carbon::now()->format('m') ? 'selected' : '' }}>
                                                    {{ $item->month_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mb-4">
                                        <label class="form-label">Project</label>
                                        <select class="form-select" name="project_id" id="search_project_id">
                                            <option value="">Select Project</option>
                                            @foreach($projectlist as $proj)
                                                <option value="{{ $proj->proj_id }}">{{ $proj->proj_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary" id="mobile_bill_search_bttn">
                                            <i class="fas fa-search"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Search Results Table --}}
                        <div class="row d-none" id="searching_result_table_section">
                            <div class="col-12">
                                <h6 class="mb-3">Search Results</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover custom_table mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 5%">S.N</th>
                                                <th style="width: 20%">Project Name</th>
                                                <th style="width: 15%">Month</th>
                                                <th style="width: 10%">Year</th>
                                                <th style="width: 20%">Uploaded By</th>
                                                <th style="width: 20%">Uploaded At</th>
                                                <th style="width: 10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="mobile_bill_searching_result_table_body">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
</div>

<script>
    /**
     * Section Toggle Functions
     */
    function showAddSalarySheetSection() {
        hideAllSections();
        document.getElementById('addSalarySheetSection').classList.remove('d-none');
        document.getElementById('addSalarySheetSection').classList.add('d-block');
        updateButtonStates('btnAddSalarySheet');
    }

    function showSearchSalarySheetSection() {
        hideAllSections();
        document.getElementById('searchSalarySheetSection').classList.remove('d-none');
        document.getElementById('searchSalarySheetSection').classList.add('d-block');
        updateButtonStates('btnSearchSalarySheet');
        // Load all salary sheets when showing search section
        loadAllSalarySheets();
    }

    function showMobileBillUploadSection() {
        hideAllSections();
        const section = document.getElementById('mobileBillUploadSection');
        if (section) {
            section.classList.remove('d-none');
            section.classList.add('d-block');
        }
        updateButtonStates('btnMobileBillUpload');
    }

    function showMobileBillSearchSection() {
        hideAllSections();
        const section = document.getElementById('mobileBillSearchSection');
        if (section) {
            section.classList.remove('d-none');
            section.classList.add('d-block');
        }
        updateButtonStates('btnMobileBillSearch');
    }

    function hideAllSections() {
        const sections = [
            'salarySheetSection',
            'addSalarySheetSection',
            'searchSalarySheetSection',
            'mobileBillUploadSection',
            'mobileBillSearchSection'
        ];

        sections.forEach(sectionId => {
            const section = document.getElementById(sectionId);
            if (section) {
                section.classList.add('d-none');
                section.classList.remove('d-block');
            }
        });
    }

    function updateButtonStates(activeButtonId) {
        const buttons = ['btnAddSalarySheet', 'btnSearchSalarySheet', 'btnMobileBillUpload', 'btnMobileBillSearch'];
        buttons.forEach(btnId => {
            const btn = document.getElementById(btnId);
            if (btn) {
                if (btnId === activeButtonId) {
                    btn.classList.add('btn-primary');
                    btn.classList.remove('btn-secondary');
                } else {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-secondary');
                }
            }
        });
    }

    /**
     * Search Salary Sheets Function
     */
    function searchSalarySheets() {
        const employeeId = document.getElementById('searchEmployeeId').value;
        const month = document.getElementById('searchMonth').value;
        const year = document.getElementById('searchYear').value;
        const date = document.getElementById('searchDate').value;

        // If all fields are empty, show all records
        if (!employeeId && !month && !year && !date) {
            loadAllSalarySheets();
            return;
        }

        // Send search request to backend
        const formData = new FormData();
        if (employeeId) formData.append('employee_id', employeeId);
        if (month) formData.append('month', month);
        if (year) formData.append('year', year);
        if (date) formData.append('date', date);

        $.ajax({
            type: 'POST',
            url: '/admin/employee/salary/sheet/search',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (res.status === 200) {
                    populateSalarySheetTable(res.data);
                } else {
                    showSweetAlertMessage('error', res.message || 'No records found');
                    populateSalarySheetTable([]);
                }
            },
            error: function(xhr) {
                showSweetAlertMessage('error', 'Search failed. Please try again.');
                console.error('Search error:', xhr);
            }
        });
    }

    /**
     * Load All Salary Sheets
     */
    function loadAllSalarySheets() {
        $.ajax({
            type: 'GET',
            url: '/admin/employee/salary/sheet/all',
            success: function(res) {
                if (res.status === 200) {
                    populateSalarySheetTable(res.data);
                } else {
                    populateSalarySheetTable([]);
                }
            },
            error: function(xhr) {
                showSweetAlertMessage('error', 'Failed to load salary sheets');
                console.error('Load error:', xhr);
            }
        });
    }

    /**
     * Populate Salary Sheet Table
     */
    function populateSalarySheetTable(sheets) {
        const tableBody = document.getElementById('salarySheetTableBody');

        if (!sheets || sheets.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i>
                        <p>No salary sheets found</p>
                    </td>
                </tr>
            `;
            return;
        }

        let rows = '';
        sheets.forEach((sheet, index) => {
            const monthName = getMonthName(sheet.month);
            const remarks = sheet.remarks || 'No remarks';
            const employeeId = sheet.no_of_emp || 'N/A';

            // View button
            let viewButton = '';
            if (sheet.file_path && sheet.file_path.trim() !== '') {
                viewButton = `
                    <button
                        type="button"
                        class="btn btn-sm btn-info"
                        title="View File"
                        onclick="viewSalarySheetFile('${sheet.file_path}')"
                        data-file-path="${sheet.file_path}"
                        data-file-name="${sheet.file_name || 'Salary Sheet'}"
                    >
                        <i class="fas fa-eye"></i>
                    </button>
                `;
            } else {
                viewButton = `
                    <span class="badge badge-secondary">
                        <i class="fas fa-exclamation-circle"></i> No File
                    </span>
                `;
            }

            // Edit button
            const editButton = `
                <button
                    type="button"
                    class="btn btn-sm btn-warning"
                    title="Edit Salary Sheet"
                    onclick="editSalarySheet(${sheet.ss_auto_id}, '${sheet.no_of_emp}', ${sheet.month}, ${sheet.year}, '${sheet.salary_date}', '${sheet.remarks || ''}')"
                    data-record-id="${sheet.ss_auto_id}"
                >
                    <i class="fas fa-edit"></i>
                </button>
            `;

            // Delete button
            const deleteButton = `
                <a
                    href="{{ route('salary-sheet.delete', '') }}/${sheet.ss_auto_id}"
                    class="btn btn-sm btn-danger"
                    title="Delete"
                    onclick="return confirm('Are you sure you want to delete this record?')"
                >
                    <i class="fas fa-trash"></i>
                </a>
            `;

            rows += `
                <tr>
                    <td>${index + 1}</td>
                    <td><span class="badge badge-primary">${employeeId}</td>
                    <td>${monthName}, ${sheet.year}</td>
                    <td>${sheet.salary_date}</td>
                    <td><small>${remarks}</small></td>
                    <td>${viewButton} ${editButton} ${deleteButton}</td>
                </tr>
            `;
        });

        tableBody.innerHTML = rows;
    }

    /**
     * Get Month Name from Number
     */
    function getMonthName(monthNum) {
        const months = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                       'July', 'August', 'September', 'October', 'November', 'December'];
        return months[monthNum] || 'N/A';
    }

    /**
     * Initialize - Show add salary sheet section by default
     */
    $(document).ready(function() {
        showAddSalarySheetSection();

        // Debug: Log file paths and S3 configuration
        const s3BucketUrl = "{{ config('app.aws_s3_bucket_url') }}";
        console.log('=== Salary Sheet File Debugging ===');
        console.log('S3 Bucket URL:', s3BucketUrl);
        console.log('S3 URL configured:', s3BucketUrl ? 'YES ✓' : 'NO ✗');

        // Count salary sheet records with/without file paths
        const viewButtons = document.querySelectorAll('[data-file-path]');
        const noFileRecords = document.querySelectorAll('.badge-secondary');
        console.log('Total records with file paths:', viewButtons.length);
        console.log('Total records without file paths:', noFileRecords.length);

        // Log each file path for debugging
        if (viewButtons.length > 0) {
            console.log('File paths found:');
            viewButtons.forEach((btn, index) => {
                const filePath = btn.getAttribute('data-file-path');
                const fileName = btn.getAttribute('data-file-name');
                const fullUrl = s3BucketUrl + filePath;
                console.log(`  ${index + 1}. File: ${fileName}`);
                console.log(`     Path: ${filePath}`);
                console.log(`     Full URL: ${fullUrl}`);
            });
        }
    });

    /**
     * Edit Salary Sheet
     */
    function editSalarySheet(recordId, noOfEmp, month, year, salaryDate, remarks) {
        // Populate form with record data
        document.getElementById('editRecordId').value = recordId;
        document.getElementById('editNoOfEmp').value = noOfEmp || '';
        document.getElementById('editMonth').value = month;
        document.getElementById('editYear').value = year;
        document.getElementById('editSalaryDate').value = salaryDate;
        document.getElementById('editRemarks').value = remarks || '';

        // Show the modal
        $('#editSalarySheetModal').modal('show');
    }

    /**
     * Save Salary Sheet Edit
     */
    function saveSalarySheetEdit() {
        // Validate required fields
        const recordId = document.getElementById('editRecordId').value;
        const month = document.getElementById('editMonth').value;
        const year = document.getElementById('editYear').value;
        const salaryDate = document.getElementById('editSalaryDate').value;

        if (!month || !year || !salaryDate) {
            showSweetAlertMessage('error', 'Please fill in all required fields');
            return;
        }

        const saveBtn = document.getElementById('saveEditBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        // Prepare form data
        const formData = new FormData(document.getElementById('editSalarySheetForm'));

        // Send AJAX request to update record
        $.ajax({
            type: 'POST',
            url: '/admin/employee/salary/sheet/update',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';

                if (res.status === 200) {
                    showSweetAlertMessage('success', res.message || 'Salary sheet updated successfully');
                    $('#editSalarySheetModal').modal('hide');

                    // Reload the page to show updated data
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showSweetAlertMessage('error', res.message || 'Failed to update salary sheet');
                }
            },
            error: function(xhr) {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save"></i> Save Changes';

                let errorMessage = 'Operation failed. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showSweetAlertMessage('error', errorMessage);
            }
        });
    }

    /**
     * Form Validation and Submission
     */
    $(document).ready(function() {
        // Salary Sheet Form Validation
        if ($("#salarySheetForm").length) {
            $("#salarySheetForm").validate({
                rules: {
                    month: { required: true },
                    year: { required: true },
                    salary_date: { required: true, date: true },
                    file_name: { required: true }
                },
                messages: {
                    month: "Please select a month",
                    year: "Please select a year",
                    salary_date: "Please select a valid date",
                    file_name: "Please select a file to upload"
                }
            });
        }

        // Mobile Bill Form Validation
        if ($("#employeeMobileBillStoreForm").length) {
            $("#employeeMobileBillStoreForm").validate({
                rules: {
                    bill_month: { required: true },
                    bill_year: { required: true },
                    bill_project_id: { required: true },
                    bill_paper: { required: true }
                },
                messages: {
                    bill_month: "Please select a month",
                    bill_year: "Please select a year",
                    bill_project_id: "Please select a project",
                    bill_paper: "Please select a file to upload"
                }
            });

            // Mobile Bill Upload Form Submission
            $('#employeeMobileBillStoreForm').submit(function(event) {
                event.preventDefault();

                if (!$(this).valid()) {
                    return;
                }

                const saveBtn = document.getElementById('employee-mobile-bill-submit-button');
                const formData = new FormData(this);

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Uploading...';

                $.ajax({
                    type: 'POST',
                    url: '/admin/employee/mobile-bill/information/manage',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Bill';

                        if (res.status === 200) {
                            showSweetAlertMessage('success', res.message);
                            $('#employeeMobileBillStoreForm')[0].reset();
                            showAddSalarySheetSection();
                            $('#img-upload3').hide();
                        } else {
                            showSweetAlertMessage('error', res.message);
                        }
                    },
                    error: function(xhr) {
                        saveBtn.disabled = false;
                        saveBtn.innerHTML = '<i class="fas fa-upload"></i> Upload Bill';
                        showSweetAlertMessage('error', 'Operation failed. Please try again.');
                    }
                });
            });
        }

        // Search Mobile Bill Form Validation
        if ($("#searching_mobile_bill_paper_form").length) {
            $("#searching_mobile_bill_paper_form").validate({
                rules: {
                    year: { required: true },
                    month: { required: true }
                },
                messages: {
                    year: "Please select a year",
                    month: "Please select a month"
                }
            });

            // Mobile Bill Search Form Submission
            $('#searching_mobile_bill_paper_form').submit(function(event) {
                event.preventDefault();

                if (!$(this).valid()) {
                    return;
                }

                const searchBtn = document.getElementById('mobile_bill_search_bttn');
                const formData = new FormData(this);

                searchBtn.disabled = true;
                searchBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

                $.ajax({
                    type: 'POST',
                    url: '/admin/employee/mobile-bill/information/manage',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        searchBtn.disabled = false;
                        searchBtn.innerHTML = '<i class="fas fa-search"></i> Search';

                        if (res.status !== 200) {
                            showSweetAlertMessage('error', res.message);
                            return;
                        }

                        if (!res.data || res.data.length === 0) {
                            showSweetAlertMessage('error', 'No records found');
                            return;
                        }

                        displaySearchResults(res.data);
                        document.getElementById('searching_result_table_section').classList.remove('d-none');
                    },
                    error: function(xhr) {
                        searchBtn.disabled = false;
                        searchBtn.innerHTML = '<i class="fas fa-search"></i> Search';
                        showSweetAlertMessage('error', 'Operation failed. Please try again.');
                    }
                });
            });
        }

        /**
         * Display Search Results
         */
        function displaySearchResults(records) {
            let rows = '';
            records.forEach((value, index) => {
                rows += `<tr>
                    <td>${index + 1}</td>
                    <td>${value.proj_name || 'N/A'}</td>
                    <td>${getMonthNameByNumber(value.month)}</td>
                    <td>${value.year}</td>
                    <td>${value.created_by || 'N/A'}</td>
                    <td>${new Date(value.created_at).toLocaleDateString('en-US')}</td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="openFileFromLocation('${value.bill_payment_paper}')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </td>
                </tr>`;
            });
            document.getElementById('mobile_bill_searching_result_table_body').innerHTML = rows;
        }

        /**
         * Helper Functions
         */
        function getMonthNameByNumber(mn) {
            const months = ['', 'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];
            return months[mn] || 'N/A';
        }
    });

    /**
     * Open File from S3 Location with Enhanced Error Handling
     */
    function openFileFromLocation(filepath) {
        if (!filepath || filepath.trim() === '') {
            showSweetAlertMessage('error', 'File path is not available for this record');
            console.error('File path is empty or not provided');
            return;
        }

        const baseUrl = "{{ config('app.aws_s3_bucket_url') }}";

        if (!baseUrl || baseUrl.trim() === '') {
            showSweetAlertMessage('error', 'AWS S3 bucket URL is not configured');
            console.error('AWS S3 bucket URL not configured in app.aws_s3_bucket_url');
            return;
        }

        const url = baseUrl + filepath;

        // Log the URL being opened for debugging
        console.log('Opening file:', url);

        // Verify the URL looks valid before opening
        if (!url.includes('http')) {
            showSweetAlertMessage('error', 'Invalid file URL: ' + url);
            console.error('Invalid file URL:', url);
            return;
        }

        // Try to open the file
        const newWindow = window.open(url, '_blank');

        if (newWindow) {
            // Successfully opened
            console.log('File opened successfully');
        } else {
            // Popup was blocked or failed
            showSweetAlertMessage('error', 'Could not open file. Please check if popups are blocked or try again.');
            console.warn('Failed to open file - popup may have been blocked');
        }
    }

    /**
     * View Salary Sheet File (Enhanced)
     */
    function viewSalarySheetFile(filePath) {
        openFileFromLocation(filePath);
    }

    /**
     * Show Sweet Alert Message
     */
    function showSweetAlertMessage(type, message) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: type,
                title: type === 'success' ? 'Success' : 'Error',
                text: message,
                confirmButtonText: 'OK'
            });
        } else {
            alert(message);
        }
    }

    /**
     * Image Preview on Upload
     */
    $(document).on('change', '#imgInp3', function() {
        const file = this.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('img-upload3');
                img.src = e.target.result;
                img.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });

    /**
     * File Preview for Salary Sheet Upload
     */
    document.getElementById('fileInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const preview = document.getElementById('preview');
            preview.innerHTML = '';
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                img.style.maxWidth = '100%';
                img.style.maxHeight = '300px';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '0.375rem';
                preview.appendChild(img);
            } else if (file.type === 'application/pdf') {
                const iframe = document.createElement('iframe');
                iframe.src = URL.createObjectURL(file);
                iframe.style.width = '100%';
                iframe.style.height = '300px';
                iframe.style.border = '1px solid #ddd';
                iframe.style.borderRadius = '0.375rem';
                preview.appendChild(iframe);
            } else {
                preview.innerHTML = '<p class="text-muted">Preview not available for this file type.</p>';
            }
        }
    });
</script>

@endsection
