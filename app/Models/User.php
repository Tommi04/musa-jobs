<?php

//ho messo user dentro Models. Devo andare in config > auth.php a modificare dove cerca User
namespace App\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 
        'last_name', 
        'email', 
        'password',
        'username',
        'privacy',
        'role_id',
        'city'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //array in autojoin, sconsigliata perchè magari non ne abbiamo bisogno sempre
    // protected $with = ['details'];

    public function getFullnameAttribute(){
        return $this->first_name . ' ' . $this->last_name;
    }

    //relazione a Role
    public function role(){
        //belongsTo = Un utente dipende da un ruolo
        //parametri: modello relazionato, chiave esterna quindi FK da User, chiave del proprietario id nella tabella Roles
        return $this->belongsTo('App\Models\Role', 'role_id', 'id');
    }

    public function details(){

        // dd($this->role_id);
        //non funziona questa cosa, perchè non abbiamo nulla, la classe viene istanziata al ->find() dentro il
        //modello e non al join (->with('details')). User::with('details')->find(2);
        //mentre qua dentro siamo al -with('details').
        //SERVE RELAZIONE POLIMORFICA
        /*if($this->role_id === 2){
            return $this->hasOne('App\Models\UserDetails', 'user_id', 'id');
        }else if ($this->role_id === 3){
            return $this->hasOne('App\Models\CompanyDetails', 'user_id', 'id');
        }else{
            return null;
        }*/

        //RELAZIONE POLIMORFA.
        // public function user(){
        //     return $this->morphOne('App\Models\User', 'details');
        // }
        //dobbiamo mettere questa relationship in CompanyDetails e in UserDetails
        return $this->morphTo();
    }

    public function scopeActive($query){
        return $query->where('email_verified_at', '<>', null);
        //uguali
        // return $query->whereNotNull('email_verified_at',);
    }

    public function scopeNotActive($query){
        // return $query->whereNull('email_verified_at');
        //uguali
        return $query->where('email_verified_at', null);
    }   

    //scope parametrico
    public function scopeByStatus($query, $status){
        if($status === true){
            return $query->whereNotNull('email_verified_at');
        }else{
            return $query->whereNull('email_verified_at');
        }
    }

    public function scopeByRole($query, $role_id){
        // dd($query, $role_id);
        return $query->where('role_id', $role_id);
    }

    public function hasRole($role){
        //role è la relationship di sopra
        return $this->role->code === $role;
    }

    public function scopeByType($query, $type = ''){
        if($type === 'user'){
            $query->where('details_type', 'App\Models\UserDetails');
        }else if ($type ==='company'){
            $query->where('details_type', 'App\Models\Company');
        }else if($type === 'admin'){
            $query->whereNull('details_type');
        }
        return $query;
    }

    public function scopeOrdered($query, $order = null){
        if(!is_null($order)){

            if($order === 'asc'){
                $query->orderBy('last_name', 'asc');
            }else if($order === 'desc'){
                $query->orderBy('last_name', 'desc');
            }
            $query->orderBy('first_name', 'asc');
        }
    }

    protected static function boot(){
        parent::boot();

        //usiamo un hook
        static::deleting(function($model){
            if($model->details_type === 'App\Models\UserDetails'){
                //vado a cancellare con la relazione polimorfa
                $model->details->jobOffers()->detach();
                $model->details()->delete();
            }else if($model->details_type === 'App\Models\Company'){
                //vado a cancellare con la relazione polimorfa
                $model->details->jobOffers()->delete();
                //vado a cancellare le righe
                $model->details()->delete();
            }
        });

        static::restoring(function($model){
            if ($model->details_type === 'App\Models\UserDetails'|| $model->details_type === 'App\Models\Company'){
                $model->details()->restore();
            }
        });
    }
}