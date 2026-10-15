<?php

namespace App\Services\Attendance;

use App\Models\AttendanceDevice;
use App\Models\AttendanceLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdmsAttendanceService
{
    public function ingest(string $serialNumber, string $table, string $payload): array
    {
        $device = AttendanceDevice::query()
            ->where('serial_number', $serialNumber)
            ->where('is_active', true)
            ->first();

        if (!$device) {
            throw new \RuntimeException('Unknown or inactive attendance device.');
        }

        $device->forceFill(['last_seen_at' => now()])->save();

        if (strtolower($table) !== 'rtlog') {
            return ['accepted' => 0, 'ignored_table' => $table];
        }

        $accepted = 0;
        $duplicates = 0;
        $lines = preg_split('/\r\n|\r|\n/', trim($payload));

        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            $fields = [];
            foreach (preg_split('/\t+/', trim($line)) as $part) {
                $pair = explode('=', $part, 2);
                if (count($pair) === 2) {
                    $fields[trim($pair[0])] = trim($pair[1]);
                }
            }

            if (empty($fields['time']) || !array_key_exists('pin', $fields)) {
                continue;
            }

            try {
                $eventTime = Carbon::parse($fields['time'], $device->timezone)->timezone(config('app.timezone'));
            } catch (\Throwable $e) {
                continue;
            }

            $eventIndex = isset($fields['index']) && $fields['index'] !== '' && is_numeric($fields['index'])
                ? (int) $fields['index'] : null;

            if ($eventIndex !== null && AttendanceLog::query()
                ->where('attendance_device_id', $device->id)
                ->where('event_index', $eventIndex)->exists()) {
                $duplicates++;
                continue;
            }

            try {
                AttendanceLog::query()->create([
                    'attendance_device_id' => $device->id,
                    'pin' => (string) $fields['pin'],
                    'event_time' => $eventTime,
                    'event' => isset($fields['event']) && is_numeric($fields['event']) ? (int) $fields['event'] : null,
                    'inoutstatus' => isset($fields['inoutstatus']) && is_numeric($fields['inoutstatus']) ? (int) $fields['inoutstatus'] : null,
                    'verifytype' => isset($fields['verifytype']) && is_numeric($fields['verifytype']) ? (int) $fields['verifytype'] : null,
                    'eventaddr' => isset($fields['eventaddr']) && is_numeric($fields['eventaddr']) ? (int) $fields['eventaddr'] : null,
                    'cardno' => $fields['cardno'] ?? null,
                    'event_index' => $eventIndex,
                    'sitecode' => $fields['sitecode'] ?? null,
                    'linkid' => $fields['linkid'] ?? null,
                    'maskflag' => $fields['maskflag'] ?? null,
                    'temperature' => isset($fields['temperature']) && is_numeric($fields['temperature']) ? $fields['temperature'] : null,
                    'convtemperature' => isset($fields['convtemperature']) && is_numeric($fields['convtemperature']) ? $fields['convtemperature'] : null,
                    'raw_data' => $line,
                    'processing_status' => 'pending',
                    'received_at' => now(),
                ]);
                $accepted++;
            } catch (\Illuminate\Database\QueryException $e) {
                // A concurrent device retry may race with the existence check.
                if ($eventIndex !== null) {
                    $duplicates++;
                    continue;
                }
                throw $e;
            }
        }

        return ['accepted' => $accepted, 'duplicates' => $duplicates];
    }
}
