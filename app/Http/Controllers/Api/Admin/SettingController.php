<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = AppSetting::all()->groupBy('group');
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function update(Request $request)
    {
        $data = $request->except(['_token', '_method']);

        foreach ($data as $key => $value) {
            $setting = AppSetting::where('key', $key)->first();
            if (!$setting)
                continue;

            if ($setting->type === 'image') {
                if ($request->hasFile($key)) {
                    $file = $request->file($key);
                    $path = \App\Helpers\ImageHelper::uploadAndConvert($file, 'control');
                    $setting->update(['value' => $path]);
                }
            } else {
                $setting->update(['value' => $value]);
            }
        }

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }
}
