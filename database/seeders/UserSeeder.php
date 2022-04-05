<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Carbon\Carbon;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(){
        for ($i=0; $i < 3; $i++) {
            User::create([
                'name' => 'subhan'.$i,
                'gender' => 'male',
                'email' => 'subhan'.$i.'@gmail.com',
                'password' => bcrypt('123456'),
                'type' => 1,
                'email_verified_at' => Carbon::now(),
                'profile_image' => 'profile_images/default.png',
                'age'=> 15,
                'height' =>12,
                'position' => 'striker',
                'country' => 'japan',
                'country_logo' => 'country_logos/default.png',
                'club' => 'Jaguar',
                'club_logo' => 'club_logos/default.png',
                'bio' => 'this is test bio',
            ]);
            User::create([
                'name' => 'sana'.$i,
                'gender' => 'female',
                'email' => 'sana'.$i.'@gmail.com',
                'password' => bcrypt('123456'),
                'type' => 2,
                'email_verified_at' => Carbon::now(),
                'profile_image' => 'profile_images/dhwlGDke1O.png',
                'age'=> 15,
                'height' =>12,
                'position' => 'striker',
                'country' => 'japan',
                'country_logo' => 'profile_images/dhwlGDke1O.png',
                'club' => 'Jaguar',
                'club_logo' => 'profile_images/dhwlGDke1O.png',
                'bio' => 'this is test bio',
            ]);
        }

    }
}
