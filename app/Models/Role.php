<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'label', 
        'code',
    ];

    // relazione a User
    public function users(){
        //hasMany un ruolo ha molti utenti
        //parametri: modello relazionato, chiave esterna quindi FK da Role, chiave del proprietario id nella tabella Users
        return $this->hasMany('App\Models\User', 'role_id', 'id');
    }
}
