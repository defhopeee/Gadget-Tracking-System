<?php

namespace Database\Seeders;

use App\Models\LocationLog;
use App\Models\Phone;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['email' => 'demo@example.com'],
            ['name' => 'Demo User', 'password' => Hash::make('password')]
        );

        // A few sample devices with a small location trail each.
        $samples = [
            [
                'label' => 'Work iPhone', 'type' => 'phone', 'brand' => 'Apple', 'model' => 'iPhone 15',
                'status' => 'active', 'owner_name' => 'Demo User',
                'trail' => [[51.5074, -0.1278], [51.5099, -0.1180], [51.5145, -0.1010]],
            ],
            [
                'label' => 'Field Tablet', 'type' => 'tablet', 'brand' => 'Samsung', 'model' => 'Galaxy Tab S9',
                'status' => 'lost', 'owner_name' => 'Field Team',
                'trail' => [[40.7128, -74.0060], [40.7300, -73.9950]],
            ],
            [
                'label' => 'Dev Laptop', 'type' => 'laptop', 'brand' => 'Dell', 'model' => 'XPS 13',
                'status' => 'active', 'owner_name' => 'Demo User',
                'trail' => [[52.5200, 13.4050]],
            ],
            [
                'label' => 'Fleet GPS Tag', 'type' => 'gps_tracker', 'brand' => 'Tile', 'model' => 'Pro',
                'status' => 'recovered', 'owner_name' => 'Logistics',
                'trail' => [[48.8566, 2.3522], [48.8606, 2.3376]],
            ],
        ];

        foreach ($samples as $sample) {
            $trail = $sample['trail'];
            unset($sample['trail']);

            $phone = $user->phones()->create($sample);

            foreach ($trail as $i => [$lat, $lng]) {
                LocationLog::create([
                    'phone_id'  => $phone->id,
                    'latitude'  => $lat,
                    'longitude' => $lng,
                    'accuracy'  => rand(5, 50),
                    'battery'   => rand(20, 100),
                    'source'    => $i === count($trail) - 1 ? 'device' : 'manual',
                    'created_at'=> Carbon::now()->subHours(count($trail) - $i),
                    'updated_at'=> Carbon::now()->subHours(count($trail) - $i),
                ]);
            }
        }
    }
}
