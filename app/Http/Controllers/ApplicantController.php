<?php

namespace App\Http\Controllers;

use App\Mail\JobApplied;
use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class ApplicantController extends Controller
{

    public function downloadResume(Applicant $applicant)
{
    // Validate that the resume path is a valid Supabase URL
    if (!filter_var($applicant->resume_path, FILTER_VALIDATE_URL)) {
        return redirect()->back()->with('error', 'Invalid resume file URL.');
    }

    // 🔹 Fetch the file from Supabase storage
    $fileResponse = Http::get($applicant->resume_path);

    // 🔹 Check if the file exists and was retrieved successfully
    if ($fileResponse->failed()) {
        return redirect()->back()->with('error', 'Failed to download the resume.');
    }

    // 🔹 Extract file content
    $fileContent = $fileResponse->body();

    // 🔹 Get file name
    $fileName = basename(parse_url($applicant->resume_path, PHP_URL_PATH));

    // 🔹 Return file as a forced download
    return response($fileContent, Response::HTTP_OK, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
    ]);
}


    //@desc Store new job application
    //@route POST /jobs/{job}/apply
    public function store(Request $request, Job $job)
    {
        //Check if the user has already applied 
        $existingApplication = Applicant::where('job_id', $job->id)->where('user_id', Auth::id())->exists();
        if ($existingApplication) {
            return redirect()->back()->with('error', 'You have already applied to this job');
        }

        //Validate incoming data
        $validatedData = $request->validate([
            'full_name' => 'required|string',
            'contact_phone' => 'nullable|string',
            'contact_email' => 'required|string|email',
            'message' => 'nullable|string',
            'location' => 'nullable|string',
            'resume' => 'required|file|mimes:pdf|max:2048'
        ]);

        $validatedData['user_id'] = Auth::id();
        $validatedData['job_id'] = $job->id;

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $fileName = 'resumes/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);
        
            // ✅ Upload the file to Supabase
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                'Content-Type' => $file->getMimeType(),
                'x-upsert' => 'true'
            ])->send('PUT', env('SUPABASE_URL') . "/storage/v1/object/$fileName", [
                'body' => $fileContent,
            ]);
        
            if ($response->failed()) {
                return redirect()->back()->with('error', 'Failed to upload resume');
            }
        
            // ✅ Store only the Supabase public URL in the database
            $validatedData['resume_path'] = env('SUPABASE_URL') . "/storage/v1/object/public/$fileName";
        }
        
        


        //Store the application
        $application = Applicant::create($validatedData);

        //Send email to owner
        Mail::to($job->user->email)->send(new JobApplied($application, $job));

        return redirect()->back()->with('success', 'Your application has been submitted');
    }

    //@desc Delete job applicant
    //@route DELETE /applicants/{applicant}
    public function destroy(string $id)
    {
        $applicant = Applicant::findOrFail($id);
        $applicant->delete();
        return redirect()->route('dashboard')->with('success', 'Applicant deleted successfully!');
    }
}
