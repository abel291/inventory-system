<?php

namespace Database\Seeders;

use App\Enums\ContactTypesEnum;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Contact::truncate();


        Contact::factory()->state(['type' => ContactTypesEnum::CLIENT->value])->count(20)->create();
        Contact::factory()->state(['type' => ContactTypesEnum::PROVIDER->value])->count(20)->create();
        Contact::factory()->state(['type' => ContactTypesEnum::CLIENT_PROVIDER->value])->count(10)->create();

        Contact::factory()->state(['type' => ContactTypesEnum::CLIENT->value])->create([
            'name' => 'Cliente Anonimo',
            'nit' => '222222222',
            'email' => 'client@client.com'
        ]);
    }
}
