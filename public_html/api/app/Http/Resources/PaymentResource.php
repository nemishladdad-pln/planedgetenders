<?php

namespace App\Http\Resources;

use App\Models\Contractor;
use App\Models\Setting;
use App\Models\Tender;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Number;
use NumberFormatter;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $modelName = '';
        if (isset($this->model) && $this->model != null) {
            $tmp = explode('/', $this->model);
            if (isset($tmp[2])) {
                $modelName = $tmp[2];
            } else {
                $modelName = $this->model;
            }
        }
        $setting = Setting::get()->where('setting_type_id', 1)->pluck('value', 'name');

        $invoiceTextMessage = "";
        $contractorInfo = [];
        $amountWithoutTax = 0;
        if ($modelName == 'Tender') {
            if ($this->model_id) {
                $tender = Tender::findOrFail($this->model_id);
                $invoiceTextMessage = "Tender Fee paid for Tender id: ".$tender->tender_uid;
                $amountWithoutTax = $tender->tender_fee;
            }
        }
        if ($modelName == 'Contractor') {
            $invoiceTextMessage = "Registration Fee paid.";
            $amountWithoutTax = (int)$setting['basic_contractor_registration_fees'];
        }

        if ($this->user_id) {
            $contractorInfo = ContractorResource::make(Contractor::where('user_id', $this->user_id)->first());
        }

        $sgstAmount = ((int)$setting['sgst_percent'] / 100) * (int)$amountWithoutTax;
        $cgstAmount = ((int)$setting['cgst_percent'] / 100) * (int)$amountWithoutTax;
        $actualAmount = (int)$this->amount;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user_name' => $this->user_id ? $this->user->name: null,
            'model_id' => $this->model_id,
            'model' => $this->model ? $this->model : null,
            'model_name' => $modelName,
            'invoice_text_message' => $invoiceTextMessage,
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'amount' => Number::format($this->amount, maxPrecision: 2),
            'currency' => $this->currency,
            'order_id' => $this->order_id,
            'method' => $this->method,
            'amount_refunded' => $this->amount_refunded,
            'bank' => $this->bank,
            'wallet' => $this->wallet,
            'entity' => $this->entity,
            'refund_Date' => $this->refund_Date,
            'bank_transaction_id' => $this->bank_transaction_id,
            'refund_id' => $this->refund_id,
            'created_at' => Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-M-Y'),
            'updated_at' => $this->updated_at,
            'contractor_info' => $contractorInfo,
            'administration_email_address' => $setting['administration_email_address'],
            'sgst_percentage' => (int)$setting['sgst_percent'],
            'cgst_percentage' => (int)$setting['cgst_percent'],
            'sgst_amount' => Number::format($sgstAmount, maxPrecision: 2) ,
            'cgst_amount' => Number::format($cgstAmount, maxPrecision: 2),
            'actual_amount' => Number::format($actualAmount, maxPrecision: 2),
            'amount_without_tax' => Number::format($amountWithoutTax, maxPrecision: 2),
            'actual_amount_in_words' => ucwords((new NumberFormatter('en_IN', NumberFormatter::SPELLOUT))->format($actualAmount)) . ' Rupees Only',
            'company_info' => [
                'company_pan' => env('COMPANY_PAN', 'PAN INFO NOT AVAILABLE'),
                'company_gst' => env('COMPANY_GST', 'GST INFO NOT AVAILABLE'),
                'company_tan' => env('COMPANY_TAN', 'TAN INFO NOT AVAILABLE'),
            ],
        ];
    }
}
