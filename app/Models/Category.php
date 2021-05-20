<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
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
    
    public function companies(){
        //una categoria è un hasMany rispetto ad una company
        return $this->hasMany('App\Models\CompanyDetails', 'category_id', 'id');
    }

    public function scopeOrdered($query){
        return $query->orderBy('label', 'ASC');
    }
}
