<?php

namespace App\Http\Controllers\Api\Attendance;

use App\Http\Controllers\Controller;
use App\Services\Attendance\AdmsAttendanceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdmsController extends Controller
{
    public function cdata(Request $request, AdmsAttendanceService $service)
    {
        $serial = (string) $request->query('SN', '');
        $table = (string) $request->query('table', '');
        if ($serial === '' || $table === '') {
            return response('ERROR', Response::HTTP_BAD_REQUEST)->header('Content-Type', 'text/plain');
        }

        try {
            $result = $service->ingest($serial, $table, (string) $request->getContent());
        } catch (\RuntimeException $e) {
            // Avoid revealing whether a serial number is registered.
            return response('ERROR', Response::HTTP_FORBIDDEN)->header('Content-Type', 'text/plain');
        }

        return response('OK', Response::HTTP_OK)->header('Content-Type', 'text/plain');
    }
}
