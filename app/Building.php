<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Building extends Model
{
    protected $table = 'buildings';
    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany('App\Contact', 'building_id', 'id'); // model, foreign_key, local_key
    }

}
