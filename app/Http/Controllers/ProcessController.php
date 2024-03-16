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


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('process.create');
    }

    /**
     * Store a newly created resource in storage.
     */

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



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
