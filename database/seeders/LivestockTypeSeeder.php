<?php

namespace Database\Seeders;

use App\Models\LivestockType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LivestockTypeSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $types = [
            [
                "name" => "Cattle",
                "code" => "cattle",
                "description" => "Bovine livestock for dairy, meat, and draft",
                "icon" => "🐄",
                "color" => "#8B4513",
                "is_active" => true,
            ],
            [
                "name" => "Sheep",
                "code" => "sheep",
                "description" => "Woolly livestock for wool, meat, and dairy",
                "icon" => "🐑",
                "color" => "#F5DEB3",
                "is_active" => true,
            ],
            [
                "name" => "Goat",
                "code" => "goat",
                "description" => "Agile livestock for meat and dairy",
                "icon" => "🐐",
                "color" => "#D3D3D3",
                "is_active" => true,
            ],
            [
                "name" => "Pig",
                "code" => "pig",
                "description" => "Livestock for meat production",
                "icon" => "🐷",
                "color" => "#FFB6C1",
                "is_active" => true,
            ],
            [
                "name" => "Chicken",
                "code" => "chicken",
                "description" => "Poultry for eggs and meat",
                "icon" => "🐔",
                "color" => "#DAA520",
                "is_active" => true,
            ],
            [
                "name" => "Duck",
                "code" => "duck",
                "description" => "Waterfowl for eggs and meat",
                "icon" => "🦆",
                "color" => "#00CED1",
                "is_active" => true,
            ],
            [
                "name" => "Horse",
                "code" => "horse",
                "description" => "Equine livestock for work and transportation",
                "icon" => "🐴",
                "color" => "#8B4513",
                "is_active" => true,
            ],
        ];

        foreach ($types as $type) {
            LivestockType::firstOrCreate(
                ["code" => $type["code"]],
                $type
            );
        }
    }
}
