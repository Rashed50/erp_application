<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class FundTransferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'credit_account_id' => $this->credit_account_id,
            'credit_account_name' => $this->whenLoaded('creditAccount', fn () => $this->creditAccount?->name),
            'credit_account_number' => $this->whenLoaded('creditAccount', fn () => $this->creditAccount?->account_number),
            'debit_account_id' => $this->debit_account_id,
            'debit_account_name' => $this->whenLoaded('debitAccount', fn () => $this->debitAccount?->name),
            'debit_account_number' => $this->whenLoaded('debitAccount', fn () => $this->debitAccount?->account_number),
            'receipt_no' => $this->receipt_no,
            'transfer_date' => $this->transfer_date?->toDateString(),
            'amount' => (float) $this->amount,
            'bank_charge' => (float) $this->bank_charge,
            'vat' => (float) $this->vat,
            'total_amount' => (float) $this->total_amount,
            'remarks' => $this->remarks,
            'attachment_url' => $this->attachment ? Storage::disk('public')->url($this->attachment) : null,
            'created_by' => $this->created_by,
            'created_by_name' => $this->whenLoaded('creator', fn () => $this->creator?->name),
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
