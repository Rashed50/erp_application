<?php

namespace App\Services;

use App\Models\CompanySetting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class CompanySettingService
{
    public function __construct(private readonly FileStorageService $fileStorage) {}

    /**
     * @param  array{company_name: string, email?: ?string, phone?: ?string, address?: ?string, logo?: ?UploadedFile, remove_logo?: bool}  $data
     */
    public function update(array $data): CompanySetting
    {
        $setting = CompanySetting::current();

        $setting->fill([
            ...Arr::only($data, ['company_name', 'email', 'phone', 'address']),
            'updated_by' => Auth::id(),
        ]);

        $setting->logo = $this->fileStorage->replace($setting->logo, $data['logo'] ?? null, $data['remove_logo'] ?? false, 'company');

        $setting->save();

        return $setting;
    }
}
