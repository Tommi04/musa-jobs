<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes;   
    protected $fillable = [
        'name',
        'description',
        'city',
        'website',
        'logo',
        'category_id',
    ];

    //questo per far appendere alla risposta API l'accessor qua sotto
    protected $appends = [
        'company_logo_full_url'
    ];

    //questo è un accessor, get all'inizio e Attribute alla fine sono fondamentali
    public function getCompanyLogoFullUrlAttribute(){
        //APP_URL sta nell'ENV
        //gli stiamo tornando la path del logo, che non andrà in risposta all'API a meno di non fare l'append tramite $appends
        return $this->logo !== '' ? env('APP_URL') . '/storage/' . $this->logo : null;
    }

    public function user(){
        return $this->morphOne('App\Models\User', 'details');
    }

    public function category(){
        //, un'azienda dipende da una categoria
        //modello relazionato, chiave esterna su compani, chiave proprietario,owner key
        return $this->belongsTo('App\Models\Category','category_id', 'id');
    }

    public function jobOffers(){
        return $this->hasMany('App\Models\JobOffer', 'company_id', 'id');
    }
    

}
