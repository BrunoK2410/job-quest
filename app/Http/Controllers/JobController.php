<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Barryvdh\DomPDF\Facade\Pdf;

class JobController extends Controller
{
    use AuthorizesRequests;

    // @desc Show all job listings
    // @route GET /jobs
    public function index(): View
    {
        $jobs = Job::latest()->paginate(9);
        return view('jobs.index', compact('jobs'));
    }

    // @desc Show create job form
    // @route GET /jobs/create
    public function create(): View
    {
        return view('jobs.create');
    }

    // @desc Save job to database
    // @route POST /jobs
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary' => 'required|integer',
            'tags' => 'nullable|string',
            'job_type' => 'required|string',
            'remote' => 'required|boolean',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zipcode' => 'nullable|string',
            'contact_email' => 'required|string',
            'contact_phone' => 'nullable|string',
            'company_name' => 'required|string',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'company_website' => 'nullable|url'
        ]);

        // Assign the logged-in user
        $validatedData['user_id'] = Auth::id();

        // ✅ Handle logo upload to Supabase using `body`
        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $fileName = 'avatars/' . uniqid() . '-' . $file->getClientOriginalName();

            // 🔹 Upload to Supabase with `body`
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                'Content-Type' => $file->getMimeType(),
                'x-upsert' => 'true'
            ])->send('POST', env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_STORAGE_BUCKET') . "/$fileName", [
                'body' => fopen($file->getRealPath(), 'r'), // ✅ Corrected file streaming
            ]);

            if ($response->successful()) {
                $validatedData['company_logo'] = env('SUPABASE_URL') . "/storage/v1/object/public/" . env('SUPABASE_STORAGE_BUCKET') . "/$fileName";
            } else {
                return redirect()->back()->with('error', 'Failed to upload logo to Supabase.');
            }
        }

        // Save to database
        Job::create($validatedData);
        return redirect()->route('jobs.index')->with('success', 'Job listing created successfully!');
    }


    // @desc Display a single job listing 
    // @route GET /jobs/{job}
    public function show(Job $job): View
    {
        return view('jobs.show', compact('job'));
    }

    // @desc Show edit job form 
    // @route GET /jobs/{job}/edit
    public function edit(Job $job): View
    {
        $this->authorize('update', $job);
        return view('jobs.edit', compact('job'));
    }

    // @desc Update job listing
    // @route PUT /jobs/{job}
    public function update(Request $request, Job $job): RedirectResponse
    {
        $this->authorize('update', $job);

        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'salary' => 'required|integer',
            'tags' => 'nullable|string',
            'job_type' => 'required|string',
            'remote' => 'required|boolean',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'address' => 'nullable|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zipcode' => 'nullable|string',
            'contact_email' => 'required|string',
            'contact_phone' => 'nullable|string',
            'company_name' => 'required|string',
            'company_description' => 'nullable|string',
            'company_logo' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
            'company_website' => 'nullable|url'
        ]);

        // Handle logo replacement
        if ($request->hasFile('company_logo')) {
            $file = $request->file('company_logo');
            $fileName = 'avatars/' . uniqid() . '-' . $file->getClientOriginalName();
    
            // 🔹 Delete the old logo from Supabase
            if (!empty($job->company_logo)) {
                $oldFileName = basename($job->company_logo);
                Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                ])->delete(env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_STORAGE_BUCKET') . "/avatars/$oldFileName");
            }
    
            // 🔹 Upload the new file to Supabase
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                'Content-Type' => $file->getMimeType(),
                'x-upsert' => 'true'
            ])->send('POST', env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_STORAGE_BUCKET') . "/$fileName", [
                'body' => fopen($file->getRealPath(), 'r'), // ✅ Using 'body' properly
            ]);
    
            if ($response->failed()) {
                return redirect()->back()->with('error', 'Failed to upload logo to Supabase.');
            }
    
            // ✅ Store the correct Supabase public URL
            $validatedData['company_logo'] = env('SUPABASE_URL') . "/storage/v1/object/public/" . env('SUPABASE_STORAGE_BUCKET') . "/$fileName";
        }
    

        // Update job
        $job->update($validatedData);
        return redirect()->route('jobs.index')->with('success', 'Job listing updated successfully!');
    }

    // @desc Delete a job listing
    // @route DELETE /jobs/{job}
    public function destroy(Job $job): RedirectResponse
    {
        $this->authorize('delete', $job);

        // Delete logo from Supabase
        if ($job->company_logo) {
            $logoPath = str_replace(env('SUPABASE_URL') . "/storage/v1/object/public/" . env('SUPABASE_STORAGE_BUCKET') . "/", '', $job->company_logo);
            Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
            ])->delete(env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_STORAGE_BUCKET') . "/$logoPath");
        }

        // Delete job
        $job->delete();

        return redirect()->route('jobs.index')->with('success', 'Job listing deleted successfully!');
    }

    // @desc Search job listings
    // @route GET /jobs/search
    public function search(Request $request): View
    {
        $keywords = strtolower($request->input('keywords'));
        $location = strtolower($request->input('location'));

        $query = Job::query();
        if ($keywords) {
            $query->whereRaw('LOWER(title) LIKE ?', ["%$keywords%"])
                  ->orWhereRaw('LOWER(description) LIKE ?', ["%$keywords%"])
                  ->orWhereRaw('LOWER(tags) LIKE ?', ["%$keywords%"]);
        }

        if ($location) {
            $query->whereRaw('LOWER(city) LIKE ?', ["%$location%"])
                  ->orWhereRaw('LOWER(state) LIKE ?', ["%$location%"]);
        }

        $jobs = $query->paginate(12);
        return view('jobs.index', compact('jobs'));
    }

    public function downloadPdf(Job $job)
    {
        $pdf = Pdf::loadView('jobs.pdf', compact('job'));
        return $pdf->download('Job_Details.pdf');
    }

}
