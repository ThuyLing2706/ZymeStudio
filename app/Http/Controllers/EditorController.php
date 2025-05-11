<?php

namespace App\Http\Controllers;

use App\Models\File;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EditorController extends Controller
{
    /**
     * Display the editor index page with user's files.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get all request parameters
        $param = $request->all();

        // Get the ID of the currently authenticated user
        $userId = Auth::user()->id;

        // Query files belonging to the current user
        $files = File::where('user_id', $userId)
            // Add conditional filter by month if specified in request
            ->where(function ($query) use ($param) {
                if (isset($param['month']) && !is_null($param['month'])) {
                    $monthNow = Carbon::create($param['month']);
                    return $query->whereMonth('created_at', $monthNow);
                } else {
                    // Get current month
                    $monthNow = new Carbon();
                    return $query->whereMonth('created_at', $monthNow);
                }
            })
            ->get()
            // Map over results to add text representation of priority
            ->map(function ($item) {
                $item->txt_priority = File::CONVERT_PRIORITY_TXT[$item->priority];
                return $item;
            });

        // dd($files, $userId);

        // Return HTML view, passing the fetched and processed files
        return view('editors.index', compact('files'));
    }

    public function update(Request $request, $id)
    {
        $param = $request->all();
        $file = File::find($id);
        if (isset($param['file']) && !is_null($param['file'])) {
            // Processing upload file
            // Use strrpos instead of strpos
            $lastDot = strrpos($file->filename, '.');
            $name = substr($file->filename, 0, $lastDot);
            // Extract the file extension
            $extension = substr($file->filename, $lastDot + 1);
            // Generate a new filename with a timestamp to avoid duplicates
            $fileName = $name . "_done." . $extension;
            // Move file to folder
            $folder = explode('@', Auth::user()->email)[0];
            $path = public_path('uploads\\' . $folder . '\\' . $fileName);
            $fileUpload = $request->file('file');
            move_uploaded_file($fileUpload, $path);
            return redirect()->back();
            
        }
        $file->status = isset($param['status']) ? File::STATUS_CONFIRM : File::STATUS_ASSIGN;
        $file->save();
        return redirect()->back();
    }

    public function download(Request $request, $id) {

        $folder = explode('@', Auth::user()->email)[0];
        $file = File::find($id);
      
        if(!$file){
            return "404 not found";
        }
          $path = public_path('uploads/'.$folder.'/'.$file->filename);

        if(!file_exists($path)){
            return "404 not found";
        }

        return response()->download($path);
    }
}