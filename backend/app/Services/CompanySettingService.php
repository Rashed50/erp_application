<?php

namespace App\Services;

use App\Models\CompanySetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompanySettingService
{
    /**
     * @param  array{company_name: string, email?: ?string, phone?: ?string, address?: ?string, logo?: ?UploadedFile, remove_logo?: bool}  $data
     */
    public function update(array $data): CompanySetting
    {
        $setting = CompanySetting::current();
        $previousLogo = $setting->logo;

        $setting->fill([
            ...Arr::only($data, ['company_name', 'email', 'phone', 'address']),
            'updated_by' => Auth::id(),
        ]);

        if (isset($data['logo'])) {
            $setting->logo = $data['logo']->store('company', 'public');
        } elseif ($data['remove_logo'] ?? false) {
            $setting->logo = null;
        }

        $setting->save();

        if ($previousLogo && $previousLogo !== $setting->logo) {
            Storage::disk('public')->delete($previousLogo);
        }

        return $setting;
    }
}
