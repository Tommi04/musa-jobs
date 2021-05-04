<?php

use App\Models\Skill;
use App\Models\UserDetails;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class UsersSkillsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        $users_details = UserDetails::all();
        $skills = Skill::get()->pluck('id')->toArray();

        foreach ($users_details as $ud => $user) {

            try {
                DB::beginTransaction();

                $random_skills = Arr::random($skills, $faker->numberBetween(5, 10));
                for ($i=0; $i < count($random_skills); $i++) { 
    
                    $random_level = $faker->randomElement( [ 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5 ] );
                    
                    $random_experience = $faker ->numberBetween(1, 10);
                    
                    $extras =  [
                        'level' => $random_level, 
                        'experience_year' => $random_experience
                    ];
                    //il secondo parametro rappresenta gli attributes, i default value delle colonne level e experience_year
                    $user->skills()->attach($random_skills[$i], $extras);

                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollBack();
            }

        }
    }
}
