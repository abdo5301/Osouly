<?php

use Illuminate\Database\Seeder;



class ImagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (range(1, 200) as $index) {
            DB::table('images')->insert([
                'sign_type' => 'App\Models\Property',
                'sign_id' => rand(1, 500),
                'path' => 'property/image (' . rand(1, 110) . ').jpg',
            ]);
        }


    }
}
