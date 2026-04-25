<?php

namespace Database\Seeders;

use App\Models\Field;
use App\Models\FieldAssignment;
use App\Models\FieldUpdate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ────────────────────────────────────────────────────
        $admin = User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@smartseason.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $agent1 = User::create([
            'name'     => 'Jane Mwangi',
            'email'    => 'agent@smartseason.com',
            'password' => Hash::make('password'),
            'role'     => 'agent',
        ]);

        $agent2 = User::create([
            'name'     => 'Brian Otieno',
            'email'    => 'agent2@smartseason.com',
            'password' => Hash::make('password'),
            'role'     => 'agent',
        ]);

        // ── Fields ───────────────────────────────────────────────────
        $field1 = Field::create([
            'name'          => 'North Plot A',
            'crop_type'     => 'Maize',
            'planting_date' => '2025-01-10',
            'stage'         => 'growing',
            'created_by'    => $admin->id,
        ]);

        $field2 = Field::create([
            'name'          => 'South Valley B',
            'crop_type'     => 'Wheat',
            'planting_date' => '2025-02-01',
            'stage'         => 'planted',
            'created_by'    => $admin->id,
        ]);

        $field3 = Field::create([
            'name'          => 'East Ridge C',
            'crop_type'     => 'Sorghum',
            'planting_date' => '2024-11-15',
            'stage'         => 'harvested',
            'created_by'    => $admin->id,
        ]);

        $field4 = Field::create([
            'name'          => 'West Lowlands D',
            'crop_type'     => 'Barley',
            'planting_date' => '2025-03-05',
            'stage'         => 'planted',
            'created_by'    => $admin->id,
        ]);

        // ── Assignments ──────────────────────────────────────────────
        FieldAssignment::create(['field_id' => $field1->id, 'agent_id' => $agent1->id]);
        FieldAssignment::create(['field_id' => $field2->id, 'agent_id' => $agent1->id]);
        FieldAssignment::create(['field_id' => $field3->id, 'agent_id' => $agent2->id]);
        FieldAssignment::create(['field_id' => $field4->id, 'agent_id' => $agent2->id]);

        // ── Field Updates (history) ───────────────────────────────────
        FieldUpdate::create([
            'field_id'  => $field1->id,
            'agent_id'  => $agent1->id,
            'new_stage' => 'growing',
            'notes'     => 'Crops looking healthy. Good rainfall recorded this week.',
        ]);

        FieldUpdate::create([
            'field_id'  => $field1->id,
            'agent_id'  => $agent1->id,
            'new_stage' => 'growing',
            'notes'     => 'Applied fertiliser. No signs of disease observed.',
        ]);

        FieldUpdate::create([
            'field_id'  => $field3->id,
            'agent_id'  => $agent2->id,
            'new_stage' => 'ready',
            'notes'     => 'Crop is ready for harvest. Awaiting equipment.',
        ]);

        FieldUpdate::create([
            'field_id'  => $field3->id,
            'agent_id'  => $agent2->id,
            'new_stage' => 'harvested',
            'notes'     => 'Harvest completed successfully. Good yield this season.',
        ]);
    }
}