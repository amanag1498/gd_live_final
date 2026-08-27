<?php

namespace Database\Seeders;

use App\Models\EntryPack;
use Illuminate\Database\Seeder;

class EntryPackSeeder extends Seeder
{
    public function run(): void
    {
        $packs = [
            [
                'name' => 'CAR',
                'price_coins' => 3000,
                'svg_url' => 'https://api.gdlive.in/media/entry-pack/entry-packs/b128e66d-3e5e-44c0-a807-16058bee7ddb.svga',
                'animation_style' => 'fullscreen',
                'priority' => 1,
                'duration_ms' => 6000,
                'duration_days' => 7,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'CAR 2',
                'price_coins' => 3000,
                'svg_url' => 'https://api.gdlive.in/media/entry-pack/entry-packs/b64c9d60-14c7-49a9-92fb-a55044bb2b89.svga',
                'animation_style' => 'fullscreen',
                'priority' => 2,
                'duration_ms' => 6000,
                'duration_days' => 7,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'CAR 3',
                'price_coins' => 4000,
                'svg_url' => 'https://api.gdlive.in/media/entry-pack/entry-packs/8579d855-ec65-457a-b827-ca95cdd194c7.svga',
                'animation_style' => 'fullscreen',
                'priority' => 3,
                'duration_ms' => 6000,
                'duration_days' => 7,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Royal Entry',
                'price_coins' => 500,
                'svg_url' => 'https://upload.wikimedia.org/wikipedia/commons/6/6b/Bitmap_VS_SVG.svg',
                'animation_style' => 'fullscreen',
                'priority' => 3,
                'duration_ms' => 3500,
                'duration_days' => 30,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'SPACESHIP',
                'price_coins' => 3000,
                'svg_url' => 'https://api.gdlive.in/media/entry-pack/entry-packs/da9e8462-b110-46bd-bbf5-c62cc3b02c30.svga',
                'animation_style' => 'fullscreen',
                'priority' => 1,
                'duration_ms' => 6000,
                'duration_days' => 7,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'DRAGON',
                'price_coins' => 6000,
                'svg_url' => 'https://api.gdlive.in/media/entry-pack/entry-packs/25d17c4c-a0c4-4e4a-a4ce-aef42bbe57aa.svga',
                'animation_style' => 'fullscreen',
                'priority' => 1,
                'duration_ms' => 6000,
                'duration_days' => 7,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Leopard',
                'price_coins' => 7000,
                'svg_url' => 'https://api.gdlive.in/media/entry-pack/entry-packs/cf4bc3dd-0f95-461f-bc34-27cb3bd133e7.svga',
                'animation_style' => 'fullscreen',
                'priority' => 1,
                'duration_ms' => 6000,
                'duration_days' => 7,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($packs as $pack) {
            EntryPack::query()->updateOrCreate(
                ['name' => $pack['name']],
                $pack,
            );
        }
    }
}
