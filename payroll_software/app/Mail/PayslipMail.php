<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class PayslipMail extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeData;
    public $payrollData;
    public $pdfContent;

    /**
     * Create a new message instance.
     */
    public function __construct(array $employeeData, array $payrollData, string $pdfContent)
    {
        $this->employeeData = $employeeData;
        $this->payrollData = $payrollData;
        $this->pdfContent = $pdfContent;
    }
 
}

