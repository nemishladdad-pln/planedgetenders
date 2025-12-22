<?php
namespace App\Repositories;

use App\Http\Requests\StoreSettingRequest;
use App\Http\Resources\SettingResource;
use App\Models\Setting;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SettingRepository implements SettingRepositoryInterface
{

    /**
     * @param mixed $request
     */
    public function all()
    {
        return SettingResource::make(Setting::get()->groupBy('setting_type_id'));

//         if (empty($settings)) {
//             return false;
//         }
//         $i = 0;
//         foreach ($settings as $setting) {
//             foreach ($setting as $index => $individualSetting) {
//                 foreach ($individualSetting as $newSetting) {
//                     $newArray[$index - 1][$newSetting->name] = $newSetting->value;
//                 }
//             }
//         }

// //dd($newArray);
//         return $newArray;
    }

    public function create($data)
    {

        // Define a mapping of setting type IDs to form data keys
        $settingTypeMap = [
            1 => 'basic_info',
            2 => 'payment_info',
            3 => 'sms_info',
            4 => 'mail_info',
        ];
        $validationRules = [
            'site_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',

        ];


        $validator = Validator::make($data, $validationRules);

        if ($validator->fails()) {

            throw new \InvalidArgumentException($validator->errors()->first());
        }
        foreach ($settingTypeMap as $settingTypeId => $formDataKey) {
            if ($data->has($formDataKey) && is_array($data->$formDataKey)) {
                foreach ($data->$formDataKey as $key => $value) {
                    dd($data->hasFile('site_logo'));
                    if ($data->hasFile('site_logo')) {
                        $uploadedFile = $data->file('site_logo');
                        $fileName = time() . '_' . $uploadedFile->getClientOriginalName();
                        $filePath = $uploadedFile->storeAs('site_logos', $fileName, 'public');
                        $validatedData=[];
                        $validatedData['site_logo'] = $filePath;
                    }
                      Setting::create([
                        'name' => $key,
                        'value' => $value,
                        'setting_type_id' => $settingTypeId,
                    ]);
                }
            }
        }

    }


    public function update($request, $setting)
    {
        if ($request->name === 'site_logo') {
            $validationRules = [
                'site_logo' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            ];

            $validator = Validator::make($request, $validationRules);

            if ($validator->fails()) {
                throw new \InvalidArgumentException($validator->errors()->first());
            }
            $request->value = save_image($request->file_url, 'settings');
        }

        $setting->value = $request->value;

        $setting->save();
        return $setting;
    }
}
