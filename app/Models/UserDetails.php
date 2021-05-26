<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserDetails extends Model
{

    use SoftDeletes;
    
    protected $fillable = [
        'bio',
        'birth_date',
        'phone',
        'gender',
        'city',
        'logo'
    ];
    public function user(){
        //per specificare noi i nome delle colonne della tabella, altrimenti nella migration
        //per creare la tabella degli utenti, dovremo assegnare alle colonne il nome della 
        //relationship... details_type e details_id
        // return $this->morphOne('App\Models\User', 'details', 'profile_type', 'profile_id', 'id');

        //così dovremo assegnare le colonne come sopra, details_type e details_id
        return $this->morphOne('App\Models\User', 'details');
    }
    

    public function skills(){
        //gli ultimi due parametri sono chiave di questo modello dentro la tabella pivot, chiave del modello da relazionare dentro la tabella pivot
        return $this
            ->belongsToMany('App\Models\Skill', 'skill_user', 'user_id', 'skill_id')
            //con withTimeStamps() viene fatto il touch delle colonne di timetamps() created_at e updated_at
            //non servono più, solo in prova
            // ->withTimestamps() 
            //da usare poi per recuperare i parametri in UserJobOfferSeeder
            ->withPivot(['level', 'experience_year']);
    }

    public function jobOffers(){
        return $this->belongsToMany('App\Models\JobOffer', 'job_offers_users', 'user_id', 'job_offer_id');
    }
}
