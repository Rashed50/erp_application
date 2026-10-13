<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The free-text employee columns replaced by a foreign key: lookup table => old column.
     *
     * @var array<string, string>
     */
    private array $lookups = [
        'departments' => 'department',
        'designations' => 'designation',
    ];

    /**
     * Run the migrations.
     *
     * Department and designation become lookups (existing text values are
     * copied into the new tables), and the present and permanent addresses get
     * a division, district and thana (upazila).
     */
    public function up(): void
    {
        Schema::table('employee_info', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('address')->constrained('divisions')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('division_id')->constrained('districts')->nullOnDelete();
            $table->foreignId('upazila_id')->nullable()->after('district_id')->constrained('upazilas')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->after('last_working_date')->constrained('departments')->nullOnDelete();
            $table->foreignId('designation_id')->nullable()->after('department_id')->constrained('designations')->nullOnDelete();
        });

        Schema::table('emp_details', function (Blueprint $table) {
            $table->foreignId('permanent_division_id')->nullable()->after('permanent_address')->constrained('divisions')->nullOnDelete();
            $table->foreignId('permanent_district_id')->nullable()->after('permanent_division_id')->constrained('districts')->nullOnDelete();
            $table->foreignId('permanent_upazila_id')->nullable()->after('permanent_district_id')->constrained('upazilas')->nullOnDelete();
        });

        foreach ($this->lookups as $lookupTable => $column) {
            DB::table('employee_info')
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->distinct()
                ->pluck($column)
                ->each(function (string $name) use ($lookupTable, $column) {
                    $id = DB::table($lookupTable)->where('name', $name)->value('id')
                        ?? DB::table($lookupTable)->insertGetId(['name' => $name, 'status' => true, 'created_at' => now(), 'updated_at' => now()]);

                    DB::table('employee_info')->where($column, $name)->update(["{$column}_id" => $id]);
                });
        }

        Schema::table('employee_info', function (Blueprint $table) {
            $table->dropIndex(['department']);
            $table->dropIndex(['designation']);
            $table->dropColumn(['department', 'designation']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_info', function (Blueprint $table) {
            $table->string('department', 100)->nullable()->index()->after('last_working_date');
            $table->string('designation', 100)->nullable()->index()->after('department');
        });

        foreach ($this->lookups as $lookupTable => $column) {
            DB::table($lookupTable)->orderBy('id')->each(function (object $row) use ($column) {
                DB::table('employee_info')->where("{$column}_id", $row->id)->update([$column => $row->name]);
            });
        }

        Schema::table('emp_details', function (Blueprint $table) {
            $table->dropConstrainedForeignId('permanent_division_id');
            $table->dropConstrainedForeignId('permanent_district_id');
            $table->dropConstrainedForeignId('permanent_upazila_id');
        });

        Schema::table('employee_info', function (Blueprint $table) {
            $table->dropConstrainedForeignId('designation_id');
            $table->dropConstrainedForeignId('department_id');
            $table->dropConstrainedForeignId('upazila_id');
            $table->dropConstrainedForeignId('district_id');
            $table->dropConstrainedForeignId('division_id');
        });
    }
};
