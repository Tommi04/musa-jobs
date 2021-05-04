<?php

use App\Models\JobOffer;
use App\Models\UserDetails;
use Illuminate\Database\Seeder;

class UserJobOffersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = UserDetails::with('skills')->get();

        foreach ($users as $u => $user) {
            foreach ($user->skills as $s => $skill) {
                $skill_id       = $skill->id;
                $skill_level    = $skill->pivot->level;
                $skill_exp      = $skill->pivot->experience_year;

                if($skill_level > 2){
                    //con questo tipo di query non si usa with ma whereHas. 
                    //Però whereHas  non prende un array ma il callback della relazione e un parametro da eseguire
                    $whereData =[
                        ['skill_id', $skill_id],
                        ['min_level', '>=', $skill_level],
                        ['max_level', '<=', $skill_level],
                        ['min_experience_years', '>=', $skill_exp],
                    ];
                    $job_offers = JobOffer::whereHas(
                        'skills', function($q) use ($skill_id, $skill_level, $skill_exp, $whereData){
                            $q->where($whereData);
                            /*quando abbiamo così tanti where
                            $q->where('skill_id', $skill_id);
                            $q->where('min_level', '>=', $skill_level);
                            $q->where('max_level', '<=', $skill_level);
                            $q->where('min_experience_years', '>=', $skill_exp);
                            */
                        })->get();
                        //la differenze tra with e whereHas
                        //whereHas non fa il with, se vogliamo tirare fuori le jobOffers con le skill e fare questa query
                        //dopo il whereHas dovremmo anche metterci un with('skill')
                        //whereHas sarebbe il cerca
                        /*
                    $job_offers = JobOffer::with([
                        'skills' => function($q) use ($skill_id, $skill_level, $skill_exp){
                            $q->where('skill_id', $skill_id);
                            $q->where('min_level', '>=', $skill_level);
                        }
                    ])->get();
                    */

                    // dd($job_offers->count());
                    $job_offers_ids = $job_offers->pluck('id')->toArray();
                    // dd($job_offers_ids);
                    
                    $user->jobOffers()->attach($job_offers_ids);
                    
                }
            }
        }
    }
}
