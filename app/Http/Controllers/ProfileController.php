<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class ProfileController extends Controller
{
    //@desc Update profile info
    //@route PUT /profile
    public function update(Request $request): RedirectResponse
    {
        //Get logged in user 
        /**
        * @var \App\Models\User $user
        */
        $user = Auth::user();

        //Validated data
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png|max:2048'
        ]);

        //Get user name and email
        $user->name=$request->input('name');
        $user->email=$request->input('email');

         // Handle avatar upload
         if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = 'avatars/' . uniqid() . '.' . $file->getClientOriginalExtension();
            $fileContent = file_get_contents($file);

            // Upload file to Supabase
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                'Content-Type' => $file->getMimeType(),
                'x-upsert' => 'true'
            ])->send('PUT', env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_STORAGE_BUCKET') . "/" . $fileName, [
                'body' => $fileContent
            ]);

            if ($response->failed()) {
                return redirect()->back()->with('error', 'Failed to upload avatar');
            }

            // Save the public Supabase URL in the database
            $user->avatar = env('SUPABASE_URL') . "/storage/v1/object/public/" . env('SUPABASE_STORAGE_BUCKET') . "/" . $fileName;
        }

        //Update user info
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Profile info updated');
    }
}