<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            CmsSeeder::class,
            DocumentTypeSeeder::class,
            RecruitmentSeeder::class,
            VisaWorkflowSeeder::class,
            LmsSeeder::class,
        ]);
    }
}
