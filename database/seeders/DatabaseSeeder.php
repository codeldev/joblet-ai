<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            attributes: [
                'email' => config(key: 'settings.contact'),
            ],
            values: [
                'name'     => 'JobletAI Support',
                'password' => bcrypt(value: str()->random(40)),
            ]
        );
    }
}
