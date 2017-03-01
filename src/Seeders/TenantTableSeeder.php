<?php

namespace Protosofia\Ben10ant\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tenants')->insert([
            'uuid' => '0123456789',
            'name' => 'Ben10ant',
            'keyname' => 'ben10ant',
            'database' => json_encode([
                'driver' => 'sqlite',
                'database' => env('DB_DATABASE', database_path('tenants/ben10ant.sqlite')),
                'prefix' => '',
            ]),
            'storage' => json_encode([
                'driver' => 'local',
                'root' => storage_path('app/tenants/ben10ant')
            ]),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);

        DB::table('tenants')->insert([
            'uuid' => '9876543210',
            'name' => '4n0th3r',
            'keyname' => '4n0th3r',
            'database' => json_encode([
                'driver' => 'sqlite',
                'database' => env('DB_DATABASE', database_path('tenants/4n0th3r.sqlite')),
                'prefix' => '',
            ]),
            'storage' => json_encode([
                'driver' => 'local',
                'root' => storage_path('app/tenants/4n0th3r')
            ]),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now()
        ]);
    }
}
