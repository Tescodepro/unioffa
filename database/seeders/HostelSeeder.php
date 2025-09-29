<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hostel;
use Illuminate\Support\Str;

class HostelSeeder extends Seeder
{
    public function run()
    {
        $hostels = [
            [
                'name' => 'Hostel A',
                'category' => 'male',
                'price' => 1000000, // 1M
                'total_rooms' => 10,
                'capacity_per_room' => 4,
            ],
            [
                'name' => 'Hostel B',
                'category' => 'female',
                'price' => 1000000,
                'total_rooms' => 15,
                'capacity_per_room' => 2,
            ],
            [
                'name' => 'Hostel C',
                'category' => 'male',
                'price' => 1300000,
                'total_rooms' => 10,
                'capacity_per_room' => 4,
            ],
            [
                'name' => 'Hostel D',
                'category' => 'female',
                'price' => 1300000,
                'total_rooms' => 15,
                'capacity_per_room' => 2,
            ],
        ];

        foreach ($hostels as $data) {
            $hostel = Hostel::create([
                'id' => Str::uuid(),
                'name' => $data['name'],
                'category' => $data['category'],
                'price' => $data['price'],
                'total_rooms' => $data['total_rooms'],
                'capacity_per_room' => $data['capacity_per_room'],
            ]);

            // 🔹 Auto-create rooms (Room 1, Room 2...)
            for ($i = 1; $i <= $data['total_rooms']; $i++) {
                $hostel->rooms()->create([
                    'id' => Str::uuid(),
                    'room_number' => 'Room ' . $i,
                ]);
            }
        }
    }
}
