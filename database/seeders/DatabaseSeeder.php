<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('ALTER TABLE job_listings DISABLE TRIGGER ALL;');
        DB::statement('ALTER TABLE users DISABLE TRIGGER ALL;');
        //Truncate tables
        DB::table('job_listings')->delete();
        DB::table('users')->delete();
        DB::table('job_user_bookmarks')->delete();
        DB::table('applicants')->delete();

        DB::statement('ALTER TABLE job_listings ENABLE TRIGGER ALL;');
        DB::statement('ALTER TABLE users ENABLE TRIGGER ALL;');
        DB::statement('ALTER TABLE job_user_bookmarks ENABLE TRIGGER ALL;'); 
        DB::statement('ALTER TABLE applicants ENABLE TRIGGER ALL;'); 

        DB::statement('ALTER SEQUENCE users_id_seq RESTART WITH 1;');
        DB::statement('ALTER SEQUENCE job_listings_id_seq RESTART WITH 1;');
        DB::statement('ALTER SEQUENCE job_user_bookmarks_id_seq RESTART WITH 1;');
        DB::statement('ALTER SEQUENCE applicants_id_seq RESTART WITH 1;');

        $this->call(TestUserSeeder::class);
        $this->call(RandomUserSeeder::class);
        $this->call(JobSeeder::class);
        $this->call(BookmarkSeeder::class);
    }
}
