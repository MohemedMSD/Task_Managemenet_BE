<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TaskStatus;

class createTaskStatus extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $status = [
            ['name' => 'Pending', 'disabled' => 0],
            ['name' =>  'Progress', 'disabled' => 0],
            ['name' => 'Completed', 'disabled' => 0],
            ['name' => 'Cancelled', 'disabled' => 1],
            ['name' => 'Expired', 'disabled' => 1],
            ['name' => 'Suspended', 'disabled' => 1],
            ['name' => 'Postponed', 'disabled' => 1],
            ['name' => 'Failed', 'disabled' => 0]
        ];

        foreach($status as $s){
            TaskStatus::create([
                'name' => $s['name'],
                'disabled' => $s['disabled']
            ]);
        }
    }
}
