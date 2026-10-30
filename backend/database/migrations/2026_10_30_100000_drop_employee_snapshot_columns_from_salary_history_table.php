<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * The employee code, name, department and designation already live on
     * employee_info (through employee_id), so salary_history no longer keeps
     * its own copy of them.
     */
    public function up(): void
    {
        Schema::table('salary_history', function (Blueprint $table) {
            $table->dropIndex(['department']);
            $table->dropColumn(['employee_code', 'employee_name', 'department', 'designation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_history', function (Blueprint $table) {
            $table->string('employee_code', 50)->default('')->after('salary_month');
            $table->string('employee_name')->default('')->after('employee_code');
            $table->string('department', 100)->nullable()->index()->after('employee_name');
            $table->string('designation', 100)->nullable()->after('department');
        });

        DB::table('salary_history')
            ->join('employee_info', 'employee_info.id', '=', 'salary_history.employee_id')
            ->leftJoin('departments', 'departments.id', '=', 'employee_info.department_id')
            ->leftJoin('designations', 'designations.id', '=', 'employee_info.designation_id')
            ->update([
                'salary_history.employee_code' => DB::raw('employee_info.employee_code'),
                'salary_history.employee_name' => DB::raw('employee_info.name'),
                'salary_history.department' => DB::raw('departments.name'),
                'salary_history.designation' => DB::raw('designations.name'),
            ]);
    }
};
