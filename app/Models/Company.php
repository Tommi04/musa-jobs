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
