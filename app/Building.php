<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Contact;

class Building extends Model
{
    protected $table = 'buildings';
    protected $guarded = [];

    public function contacts()
    {
        return $this->hasMany('App\Contact', 'building_id', 'id'); // model, foreign_key, local_key
    }

    public function contact()
    {
        return $this->hasOne('App\Contact', 'building_id', 'id'); // model, foreign_key, local_key
    }
    
    public function loftbotNumber() 
    {
        return "(".substr($this->loftbot_number, 0, 3).") ".substr($this->loftbot_number, 3, 3)."-".substr($this->loftbot_number,6);
    }
}
