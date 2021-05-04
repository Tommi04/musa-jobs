<?php

use App\Models\Company;
use App\Models\JobOffer;
use App\Models\Skill;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class JobOffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        $now = Carbon::now();

        $companies = Company::all();

        $skills = Skill::get()->pluck('id')->toArray();

        foreach ($companies as $c => $company) {
            $random_job_offers_count = $faker->numberBetween(1, 10);

            for($joc = 0; $joc < $random_job_offers_count; $joc++){

                try {
                    DB::beginTransaction();
                    
                   $job_offer_data = [
                       //sentence() tira fuori il lorem ipsum di quante parole gli passiamo come parametro
                       'role'         =>  $faker->sentence(3),
                       'description'  =>  $faker->text(200),
                       'status'       =>  1,
                    //    'company_id'   =>  $company->id,
                       'valid_from'   =>  $now,
                       'valid_to'     =>  null
                   ];
   
                   //con il tipo di relazione hasMany posso anche fare questa cosa, chiamare la relazione e poi creare l'offerta
                   //usando il metodo create in catena con la relazione, passandogli un array di dati, che non avrebbe il company_id sopra
                   //evitiamo di crare per sbaglio job offer per un'altra azienda, togliamo company_id da sopra
                   $job_offer = $company->jobOffers()->create($job_offer_data);
                   //Però questa tecnica restituisce l'istanza del modello del jobOffer
                   // dd($job_offer);
   
                   //questo ci obbliga a mettere nell'array il company_id per passarglielo, senza prenderlo dalla relation
                   // $job_offer = JobOffer::create($job_offer_data)
   
   
                   //per fare il touch dei campi
                   $job_offer->statusHistory()->attach(1, ['last' => true, 'from' => $now, 'to' => null]);
   
                   $random_skills = Arr::random($skills, $faker->numberBetween(3, 7));
   
                   for ($rs=0; $rs < count($random_skills); $rs++) { 
                       $random_min_level = $faker->randomElement([1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5]);
                       
                       $max_level_array = [];
   
                       for ($ml=$random_min_level; $ml <= 5; $ml+= 0.5) { 
                           $max_level_array[] = $ml;
                       }
                       $random_max_level = $faker->randomElement($max_level_array);
   
                       $random_min_exp = $faker->numberBetween(1, 10);
                    //    $random_max_exp = $faker->numberBetween(min($random_min_exp + 1, 10), 10);
   
                       $extra_data = [
                           'min_level'             => $random_min_level,
                           'max_level'             => $random_max_level,
                           'min_experience_years'   => $random_min_exp,
                        //    'max_experience_years'   => $random_max_exp
                       ];
                       $job_offer->skills()->attach($random_skills[$rs], $extra_data);

                    }
                    DB::commit();
                }catch(\Exception $e) {
                    DB::rollBack();
                }
            }
        }
    }
}