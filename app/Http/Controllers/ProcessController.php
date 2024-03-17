<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Import the File facade
use App\Models\Process;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Import the QrCode facade
use Illuminate\Support\Facades\Storage; // Add this line



class ProcessController extends Controller
{


    public function index()
    {
        $processes = Process::all();

        if (request()->expectsJson()) {
            return response()->json($processes);
        }

        return view('process.index', compact('processes'));
    }



    public function create()
    {
        return view('process.create');
    }



    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'process_name' => 'required|string|max:255',
            'process_owner' => 'required|string|max:255',
            'prcdept_name' => 'required|string|max:255',
            'prc_desc' => 'nullable|string',
            'prc_doc' => 'nullable|file|mimes:pdf,doc,docx', // Validate that 'prc_doc' is a file with allowed MIME types
        ]);

        // Store the document file in the public disk and get its path
        if ($request->hasFile('prc_doc')) {
            $file = $request->file('prc_doc');
            if ($file->isValid()) {
                // Store the file in the 'public/documents' directory
                $filePath = $file->storeAs('public/documents', $file->getClientOriginalName());

                // Save the file path to the database
                $validatedData['prc_doc'] = $filePath;
            } else {
                return response()->json(['error' => 'Invalid file.'], 400);
            }
        }

        // Generate QR code based on process data
        $qrCodeData = $validatedData['process_name'] . ', ' . $validatedData['process_owner']; // Example data for QR code
        $qrCode = QrCode::format('png')->size(200)->generate($qrCodeData); // Generate QR code dynamically

        // Save the QR code to a file
        $qrCodePath = 'qrcodes/' . $validatedData['process_name'] . '.png'; // QR code file path
        if (!file_put_contents(public_path($qrCodePath), $qrCode)) {
            return response()->json(['error' => 'Failed to save QR code.'], 500);
        }

        // Save the process data in the database
        $process = Process::create($validatedData);

        // Return a response indicating success
        return response()->json(['message' => 'Process created successfully', 'process' => $process], 201);
    }




    public function show(string $id)
    {
        //
    }


    public function edit($id)
    {
        $process = Process::findOrFail($id);
        return response()->json($process);
    }

//    public function update(Request $request, $id)
//    {
//        $validatedData = $request->validate([
//            'process_name' => 'nullable|string|max:255',
//            'process_owner' => 'nullable|string|max:255',
//            'prcdept_name' => 'nullable|string|max:255',
//            'prc_desc' => 'nullable|string',
//            'prc_doc' => 'nullable|file|mimes:pdf,doc,docx',
//        ]);
//
//        // Update process data in the database
//        $process = Process::findOrFail($id);
//
//        // Check if prc_doc file is provided in the request
//        if ($request->hasFile('prc_doc')) {
//            // If a new document is uploaded, delete the old one
//            if ($process->prc_doc) {
//                Storage::delete($process->prc_doc);
//            }
//
//            // Store the new document and update the file path
//            $validatedData['prc_doc'] = $request->file('prc_doc')->store('public/documents');
//
//            // Update the process with the new file path
//            $process->update($validatedData);
//
//            // Return a response indicating success
//            return response()->json(['message' => 'Process updated successfully', 'process' => $process]);
//        } else {
//            // If prc_doc is not provided, retain the existing file path
//            $validatedData['prc_doc'] = $process->prc_doc;
//
//            // Update the process with the existing file path
//            $process->update($validatedData);
//
//            // Return a response indicating success
//            return response()->json(['message' => 'Process updated successfully', 'process' => $process]);
//        }
//    }


    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'process_name' => 'nullable|string|max:255',
            'process_owner' => 'nullable|string|max:255',
            'prcdept_name' => 'nullable|string|max:255',
            'prc_desc' => 'nullable|string',
            'prc_doc' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        // Retrieve the process to update
        $process = Process::findOrFail($id);

        // Update the process data with the validated data
        $process->fill($validatedData);

        // Check if prc_doc file is provided in the request
        if ($request->hasFile('prc_doc')) {
            // If a new document is uploaded, delete the old one
            if ($process->prc_doc) {
                Storage::delete($process->prc_doc);
            }

            // Store the new document and update the file path
            $process->prc_doc = $request->file('prc_doc')->store('public/documents');
        }

        // Save the updated process data
        $process->save();

        // Return a response indicating success
        return response()->json(['message' => 'Process updated successfully', 'process' => $process]);
    }



    public function destroy($id)
    {
        $process = Process::findOrFail($id);

        // Perform any necessary checks or validations

        // Delete the process
        $process->delete();

        // Return a response indicating success
        return response()->json(['message' => 'Process deleted successfully']);
    }

}
