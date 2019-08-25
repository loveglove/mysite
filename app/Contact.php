<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
	protected $table = 'contacts';
    protected $guarded = [];


    public function building()
    {
        return $this->hasOne('App\Building', 'id', 'building_id'); // model, foreign_key, local_key
    }

}
