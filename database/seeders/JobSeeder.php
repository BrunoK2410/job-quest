<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        $jobListings = include database_path('seeders/data/job_listings.php');

        $testUserId = User::where('email', 'test@test.com')->value('id');
        $userIds = User::where('email', '!=', 'test@test.com')->pluck('id')->toArray();

        $finalJobListings = [];

        foreach ($jobListings as $index => $listing) {
            $listing['user_id'] = ($index < 2) ? $testUserId : $userIds[array_rand($userIds)];

            // ✅ Change the logo path to `storage/public/logos/`
            $logoPath = storage_path("app/public/logos/" . basename($listing['company_logo']));

            if (file_exists($logoPath)) {
                dump("✅ File found: {$logoPath}");

                // Upload logo to Supabase
                $fileName = 'avatars/' . uniqid() . '-' . basename($listing['company_logo']);
                $fileContent = file_get_contents($logoPath);

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . env('SUPABASE_KEY'),
                    'Content-Type' => mime_content_type($logoPath),
                    'x-upsert' => 'true'
                ])->send('PUT', env('SUPABASE_URL') . "/storage/v1/object/" . env('SUPABASE_STORAGE_BUCKET') . "/" . $fileName, [
                    'body' => $fileContent
                ]);

                if ($response->successful()) {
                    dump("✅ Supabase Upload Success for {$fileName}");
                    $listing['company_logo'] = env('SUPABASE_URL') . "/storage/v1/object/public/" . env('SUPABASE_STORAGE_BUCKET') . "/" . $fileName;
                } else {
                    dump("❌ Supabase Upload Failed for {$fileName}");
                    dump($response->json());
                    $listing['company_logo'] = null;
                }
            } else {
                dump("❌ File NOT found: {$logoPath}");
                $listing['company_logo'] = null;
            }

            $listing['created_at'] = now();
            $listing['updated_at'] = now();

            $finalJobListings[] = $listing;
        }

        DB::table('job_listings')->insert($finalJobListings);
        echo "✅ Jobs and logos uploaded successfully!\n";
    }
}