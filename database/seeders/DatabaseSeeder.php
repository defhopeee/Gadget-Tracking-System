<?php

namespace Database\Seeders;

use App\Models\LocationLog;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            ['name' => 'Demo Owner', 'password' => Hash::make(Str::random(16))]
        );

        // Fictional sample gadgets with generic public-landmark coordinates.
        $samples = [
            [
                'label' => 'Demo Phone', 'type' => 'phone', 'status' => 'active',
                'owner_name' => 'Demo Owner', 'phone_number' => '+1 555 0100', 'imei' => null,
                'trail' => [[51.507400, -0.127800], [51.509900, -0.118000], [51.514500, -0.101000]],
            ],
            [
                'label' => 'Demo Tablet', 'type' => 'tablet', 'status' => 'lost',
                'owner_name' => 'Front Desk', 'phone_number' => '+1 555 0123', 'imei' => null,
                'trail' => [[40.712800, -74.006000], [40.730000, -73.995000]],
            ],
            [
                'label' => 'Demo Laptop', 'type' => 'laptop', 'status' => 'active',
                'owner_name' => 'Field Team', 'phone_number' => null, 'imei' => null,
                'trail' => [[48.856600, 2.352200]],
            ],
            [
                'label' => 'Demo GPS Tag', 'type' => 'gps_tracker', 'status' => 'recovered',
                'owner_name' => 'Logistics', 'phone_number' => null, 'imei' => null,
                'trail' => [[52.520000, 13.405000], [52.516300, 13.377700]],
            ],
        ];

        foreach ($samples as $sample) {
            $trail = $sample['trail'];
            unset($sample['trail']);

            $phone = $user->phones()->create($sample);

            foreach ($trail as $i => [$lat, $lng]) {
                $isLatest = $i === count($trail) - 1;
                LocationLog::create([
                    'phone_id'   => $phone->id,
                    'latitude'   => $lat,
                    'longitude'  => $lng,
                    'accuracy'   => rand(5, 40),
                    'battery'    => rand(30, 95),
                    'source'     => $isLatest ? 'device' : 'manual',
                    'created_at' => Carbon::now()->subHours(count($trail) - $i),
                    'updated_at' => Carbon::now()->subHours(count($trail) - $i),
                ]);
            }
        }
    }
}
