<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffer extends Model
{
    protected $fillable = [
        'role',
        'description',
        'status',
        'company_id',
        'valid_from',
        'valid_to',
    ];

    public function company(){
        //modello, chiave esterna foreign key e local_key riscritte
        return $this->belongsTo('App\Models\Company', 'company_id', 'id');
    }

    public function skills(){
        //gli ultimi due parametri sono chiave di questo modello dentro la tabella pivot, chiave del modello da relazionare dentro la tabella pivot
        return $this->belongsToMany('App\Models\Skill', 'job_offer_skill', 'job_offer_id', 'skill_id');
    }

    public function statusHistory(){
        return $this
                ->belongsToMany('App\Models\JobOfferStatus', 'job_offer_has_status', 'job_offer_id', 'job_offer_status_id')
                //per fare automaticamente il touch delle date, andare a inserirla pure nell'inversa
            //non servono più, solo in prova
                // ->withTimestamps()
                //tiriamo dentro qualcosa
                ->withPivot('last', 'from','to');
    }
}