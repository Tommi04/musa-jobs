<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOfferStatus extends Model
{

    //in questo modo non cercherà la tabella job_offer_statuses che non esiste perchè nella migration
    //abbiamo scritto job_offer_status e non statuses. Ma lui cerca comunque il plurale

    protected $table = 'job_offer_status';

    protected $fillable = [
        'label',
        'code'
    ];

    public function jobOffers(){
        return $this
                ->belongsToMany('App\Models\JobOffer', 'job_offer_has_status', 'job_offer_status_id', 'job_offer_id');
                //per fare automaticamente il touch delle date, andare a inserirla pure nell'inversa
            //non servono più, solo in prova
                // ->withTimestamps();
    }

    public function scopeLast($query){
        return $query->where('last', true);
    }

    public function scopeOrderByStatusHistory($query){
        $query->orderBy('from', 'DESC');
        $query->orderBy('to', 'DESC');
        return $query;
    }
}
