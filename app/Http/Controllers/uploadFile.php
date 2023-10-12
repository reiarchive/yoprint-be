<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use App\Jobs\ProcessFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class uploadFile extends Controller
{
    public function processFile(Request $request)
    {

        $this->validate($request, [
            'file'          => 'required'
        ]);

        $file = $request->file('file');

        // Mengambil nama file
        $file_name = $file->getClientOriginalName();

        // Memindahkan file ke folder tujuan
        $file_path = $file->storeAs('public', $file_name);
        $full_path = storage_path('app/' . $file_path);

        // Calculate the hash of the uploaded file
        $file_hash = hash_file('sha256', $full_path);

        $filtered_records = Upload::where('hash', $file_hash)->get();

        if ($filtered_records->count() > 0) {
            return response()->json([
                "error" => 1,
                "message" => "the same file already been uploaded"
            ]);
        }

        $upload = new Upload;
        $upload->file_name = $file_name;
        $upload->file_path = $file_path;
        $upload->hash = $file_hash;

        // Menyimpan data ke database
        $upload->save();

        broadcast(new \App\Events\fileStatus([
            "id" => $upload->id, 
            "time" => $upload->created_at,
            "file_name" => $file_name,
            "status" => $upload->status,
            "isNew" => true
        ]));

        ProcessFile::dispatch(["id" => $upload->id, "file_path" => $full_path, "hash" => $file_hash]);

        

        return true;
    }
}
