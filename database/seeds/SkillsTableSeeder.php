<?php

use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $skills_array = [];
        $skills = fopen(storage_path('app/skills.csv'), "r");
        $header = true;
        while ($csvLine = fgetcsv($skills, 1000, ',')) {
            if ($header){
                $header = false;
            }else{
                $skills_array[] = [
                    'label' => $csvLine[0],
                    // 'code'  => Str::slug($csvLine[0]),
                    //slug estrometto # e ++ quindi C, C++ e C# passati a slug diventano c e .NET e NET diventano NET
                    //Perciò viene errore di: Integrity constraint violation: 1062 Duplicate entry 'c' for key 'skills_code_unique'"
                    //perciò nel csv ci va un'altra colonna che rappresenta il code
                    'code'  => isset($csvLine[1]) ? $csvLine[1] : Str::slug($csvLine[0]),
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }

        }
        Skill::insert($skills_array);

        /* usato il csv
        for ($i=0; $i < 50; $i++) { 
            $skills[] = [
                'label' => 'Skill' . $i,
                'code'  => 'skill-' . $i,
                'created_at' => $now,
                'updated_at' => $now
                
            ];
        }
        
        Skill::insert($skills);
        */
    }
}
