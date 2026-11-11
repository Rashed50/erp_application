<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmployeeFilesSeeder extends Seeder
{
    public function run(): void
    {
        $employeeId = DB::table('employee_info')->where('employee_code', 'DEMO-EMP-101')->value('id');
        if (!$employeeId) {
            $this->command?->warn('Skipping employee file: demo employee missing.');
            return;
        }
        // Create a real harmless placeholder file, never a fake path to an absent document.
        $path = 'employee-documents/demo-emp-101-note.txt';
        \Illuminate\Support\Facades\Storage::disk('public')->put(
            $path, "Demo employee document. Not an identity document.\n"
        );
        DB::table('employee_files')->updateOrInsert(
            ['employee_id' => $employeeId, 'file_path' => $path],
            [
                'document_type' => 'other', 'title' => 'Demo note',
                'file_name' => 'demo-emp-101-note.txt', 'mime_type' => 'text/plain',
                'file_size' => strlen("Demo employee document. Not an identity document.\n"),
                'updated_at' => now(),
            ]
        );
    }
}
