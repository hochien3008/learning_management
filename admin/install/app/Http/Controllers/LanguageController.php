<?php

namespace App\Http\Controllers;

use App\Http\Requests\LanguageRequest;
use App\Models\Language;
use App\Repositories\LanguageRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class LanguageController extends Controller
{
    public function index()
    {
        $folderPath = base_path('lang');

        if (File::isDirectory($folderPath)) {
            $files = File::allFiles($folderPath);

            $fileNames = [];
            foreach ($files as $file) {
                $fileName = $file->getFilenameWithoutExtension();
                if ($fileName != 'installer_messages') {
                    $fileNames[] = $fileName;
                }
            }

            LanguageRepository::checkFileExitsOrNot($fileNames);

            $allLanguages = LanguageRepository::getAll();

            return view('language.index', compact('allLanguages'));
        }
    }

    public function create()
    {
        return view('language.create');
    }

    public function store(LanguageRequest $request)
    {
        $newFile = base_path('lang/') . $request->name . '.json';

        if (!file_exists($newFile)) {

            $response = LanguageRepository::storeByRequest($request);

            if ($response['type'] == 'error') {
                return back()->with('alertError', [
                    'message' => $response['message'],
                    'message2' => 'Please change your lang folder permission and try again',
                ]);
            }

            return to_route('language.index')->withSuccess(__('Created Successfully'));
        }

        return back()->withError(__('File already exists'));
    }

    public function edit(Language $language)
    {
        $filePath = base_path('lang/') . $language->name . '.json';

        if (file_exists($filePath)) {
            $languageData = json_decode(file_get_contents($filePath)) ?? [];

            return view('language.edit', compact('languageData', 'language'));
        }

        return back()->withError(__('File does not exist'));
    }

    public function update(LanguageRequest $request, Language $language)
    {
        $language->update([
            'title' => $request->title,
        ]);

        Cache::forget('languages');

        return back()->withSuccess(__('Updated Successfully'));
    }

    public function delete($langId)
    {
        $language = LanguageRepository::query()->where('id', $langId)->first();

        $langName = $language?->name;
        $filePath = base_path('lang/') . $langName . '.json'; // directory

        if ($language) {
            LanguageRepository::query()->where('name', $langName)->delete();
        } else {
            return back()->withError(__('Language not found'));
        }

        if (file_exists($filePath) && $langName != 'en') {
            try {
                unlink($filePath);
            } catch (\Throwable $th) {
                return back()->with('alertError', [
                    'message' => $th->getMessage(),
                    'message2' => 'Please change your lang folder permission and try again set permission to 0777',
                ]);
            }
        }

        Cache::forget('languages');

        return to_route('language.index')->withSuccess(__('Deleted Successfully'));
    }



    public function export($langId)
    {
        $language = LanguageRepository::query()->where('id', $langId)->first();

        if ($language) {
            $langName = $language->name;
            $filePath = base_path('lang/') . $langName . '.json'; // directory

            if (file_exists($filePath)) {
                return response()->download($filePath);
            }
        }

        return back()->withError(__('File does not exist'));
    }

    public function import($langId, Request $request)
    {
        // if (app()->environment() == 'local') {
        //     return back()->with('demoMode', 'You can not update the language in demo mode');
        // }

        $language = LanguageRepository::query()->where('id', $langId)->first();

        $requestFile = $request->file('file');

        $fileContent = file_get_contents($requestFile->getRealPath());
        $data = json_decode($fileContent, true);

        if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
            return back()->withError(__('Please upload a valid json file'));
        }

        if ($language) {

            $langName = $language->name;
            $filePath = base_path('lang/') . $langName . '.json'; // directory

            if (file_exists($filePath)) {

                File::delete($filePath);

                $requestFile->move(base_path('lang/'), $langName . '.json');

                try {
                    chmod(base_path('lang/') . $langName . '.json', 0777);
                } catch (\Throwable $th) {
                }

                return back()->with('successAlert', __('Language imported successfully and language file is updated'));
            } else {
                return back()->withError(__('File does not exist') . ' path: ' . $filePath);
            }
        }

        return back()->withError(__('File does not exist'));
    }


    public function setDefaultLanguage(Language $language)
    {
        $name = $language?->name ?? 'en';

        $response = $this->setEnv('APP_LOCALE', $name);

        if ($response['type'] == 'error') {
            return back()->with('alertError', [
                'message' => $response['message'],
                'message2' => 'Please change your .env file permission and try again. set permission to 0777',
            ]);
        }

        session()->put('locale', $name);

        Artisan::call('config:clear');

        return to_route('language.index')->withSuccess(__('Language set successfully'));
    }
}
