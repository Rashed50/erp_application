<?php

namespace Modules\Tender\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Log, DB};
use Illuminate\Routing\Controller;
use Barryvdh\DomPDF\Facade\Pdf;


class TenderController extends Controller
{

    public function index()
    {
        return view('tender::pages.tender.create');
    }

    public function create()
    {
        return view('tender::pages.tender.create');
    }

    // public function tender_pdf(Request $request)
    // {
    //     $data = [
    //         'company_name' => 'ABC Company',
    //         'date' => now()->format('Y-m-d'),
    //         'company_logo' => public_path('modules/tender/img/header_image.png'),
    //         'footer_logo' => public_path('modules/tender/img/footer_image.png'),

    //         'company_representative_name' => 'ALI MOHAMMED ALI ALHORAIBI',
    //         'company_representative_designation' => 'Owner of the Company',
    //         'post_box' => '12222',
    //         'city' => 'Riyadh',
    //         'phone_number' => '0114589171',
    //         'fax_number' => '05555555',
    //         'cr_no' => '1084278439',
    //         'country' => 'Saudi Arabia',

    //         'ref_project' => 'The Avenues Mall, Riyadh',
    //         'letter_of_intent' => 'Installation CMU BLOCK & PLASTER Works as per attached BOQ',
    //     ];

    //     $pdf = Pdf::loadView('tender::pages.pdf.tender_pdf', ['data' => $data]);

    //     return $pdf->stream('tender_documents_' . $data['company_name'] . '.pdf');
    // }

    // public function tender_pdf(Request $request)
    // {
    //     // Simulated data for multiple pages
    //     $data = [
    //         'header' => [
    //             'date' => '07th Sep-2024',
    //             'ref' => 'ABC-AVR-AG-CMU-008',
    //             'company_logo' => public_path('modules/tender/img/header_image.png'),
    //         ],
    //         'footer' => [
    //             'footer_logo' => public_path('modules/tender/img/footer_image.png'),
    //         ],
    //         'content' => [
    //             [
    //                 'title' => 'Page 1 Title', 
    //                 'body' => 'This is the content for Page 1.'
    //             ],
    //             [
    //                 'title' => 'Page 2 Title', 
    //                 'body' => 'This is the content for Page 2.'
    //             ],
    //             [
    //                 'title' => 'Page 3 Title', 
    //                 'body' => 'This is the content for Page 3.'
    //             ],
    //         ],
    //     ];

    //     // Generate PDF
    //     $pdf = Pdf::loadView('tender::pages.pdf.tender_pdf', compact('data'))
    //         ->setPaper('a4', 'portrait'); // Page format (A4)

    //     return $pdf->stream('tender_document.pdf');
    // }


    public function tender_pdf(Request $request)
    {
        $data = [
            'company_logo' => public_path('modules/tender/img/header_image.png'),
            'footer_logo' => public_path('modules/tender/img/footer_image.png'),


            'company_name' => 'ABC Company',
            'date' => now()->format('Y-m-d'),
            'company_representative_name' => 'ALI MOHAMMED ALI ALHORAIBI',
            'company_representative_designation' => 'Owner of the Company',
            'post_box' => '12222',
            'city' => 'Riyadh',
            'phone_number' => '0114589171',
            'fax_number' => '05555555',
            'cr_no' => '1084278439',
            'country' => 'Saudi Arabia',
            'ref_project' => 'The Avenues Mall, Riyadh',
            'letter_of_intent' => 'Installation CMU BLOCK & PLASTER Works as per attached BOQ',
            'total_amount' => '608000.00',
            'total_amount_written_in_words' => 'Six hundred and eight thousand Saudi Riyal only',
            'advance_payment' => 0,
            'retention' => 0,
            'actual_executed_quantities' => 100,
            'actual_executed_quantities_word' => 'Hundred',
            'installation' => 'CMU BLOCK & PLASTER Works',
        ];

        // Generate PDF
        $pdf = Pdf::loadView('tender::pages.pdf.tender_pdf', compact('data'))
            ->setPaper('a4', 'portrait'); // Page format (A4)

        return $pdf->stream('tender_document.pdf');
    }



}
