<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    protected $fillable = [
        'label',
        'code'
    ];

    public function users(){
        //gli ultimi due parametri sono chiave di questo modello dentro la tabella pivot, chiave del modello da relazionare dentro la tabella pivot
        return $this->belongsToMany('App\Models\UserDetails', 'skill_user', 'skill_id', 'user_id');
    }

    public function jobOffers(){
        //gli ultimi due parametri sono chiave di questo modello dentro la tabella pivot, chiave del modello da relazionare dentro la tabella pivot
        return $this->belongsToMany('App\Models\JobOffer', 'job_offer_skill', 'job_offer_id', 'skill_id');
    }
}