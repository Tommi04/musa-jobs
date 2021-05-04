<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJobOfferRequest;
use App\Http\Requests\JobOffersBySkillRequest;
use App\Models\JobOffer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobOffersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /*TRASFORMO IN POST
    public function jobOffersBySkill($skill_id){
        $job_offers = JobOffer::with([
            'company',
            'skills'
        ])->whereHas('skills', function($q) use($skill_id){
            $q->where('skill_id', $skill_id);
        })->get();
        return response()->json(['result' => $job_offers, 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);
    }
    */

    public function jobOffersbySkill(JobOffersBySkillRequest $request){
        //prendiamo tutte le skills
        $skills_input = $request->get('skills');

        // facciamo il join tra company e skills
        $job_offers_query = JobOffer::with([
            'company',
            'skills'
        ]);
        
        //cicliamo sulle skills trovate e facciamo un join prendendolo se non è nullo e trovando le giuste corrispondenze
        foreach ($skills_input as $si => $skill) {
          $job_offers_query->whereHas('skills', function($query) use ($skill){
            $query->where('skill_id', $skill['id']);
            $query->where('min_level', '<=', $skill['min_lvl']);
            $query->where('max_level', '<=', $skill['max_lvl']);
            $query->where('min_experience_years', '<=', $skill['min_exp']);
            });
        }

        //prendiamo le offerte di lavoro con quelle skills
        $job_offers = $job_offers_query->get();

        return response()->json(['result' => $job_offers, 'code' => JsonResponse::HTTP_OK], JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateJobOfferRequest $request)
    {
        //viene tradotto in un elemento di un array
        // dd($request->input('name'));
        // dd($request->all());

        $now = Carbon::now();

        try {
            DB::beginTransaction();

            //creo la job offer mergando la $request con lo status
            $job_offer = JobOffer::create(array_merge($request->all(), ['status' => 1]));
    
            //attach() con lo status 1 che è quello pubblicato
            $job_offer->statusHistory()->attach(1, ['last' => true, 'from' => $now, 'to' => null]);
    
            $job_offer_skills = $request->get('skills');
    
            foreach ($job_offer_skills as $jos => $job_offer_skill) {
                $job_offer_skill_extras = [
                    'min_level'             => $job_offer_skill['min_level'],
                    'max_level'             => $job_offer_skill['max_level'],
                    'min_experence_years'   => $job_offer_skill['min_years'],
                    // 'max_experence_years'   => $job_offer_skill['min_years'],
                ];
                $job_offer->skills()->attach($job_offer_skill['id'], $job_offer_skill_extras);
            }

            DB::commit();

            return response()->json(['result' => 'Offerta di lavoro creata correttamente.', 'code' => JsonResponse::HTTP_CREATED], JsonResponse::HTTP_CREATED);
            
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['errors' => true, 'message' => 'Errore di creazione dell\'offerta di lavoro', 'code' => JsonResponse::HTTP_INTERNAL_SERVER_ERROR], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
