<?php

namespace App\Http\Controllers;

use App\Mail\JobApplied;
use App\Models\Applicant;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ApplicantController extends Controller
{

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

        //Handle resume upload
        if ($request->hasFile('resume')) {
            $path = $request->file('resume')->store('resumes', 'public');
            $validatedData['resume_path'] = $path;
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
