<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Log extends Model
{
    protected $table = 'logs';
    protected $guarded = [];

    public function building()
    {
        return $this->hasOne('App\Building', 'id', 'building_id');
    }
    
    public function mode()
    {
        $mode = "Unknown Value";
        switch($this->data)
        {
            case 1:
                $mode = "Multi Dial";
            break;
            case 2:
                $mode = "Auto Open";
            break;
            case 3:
                $mode = "Passcode";
            break;
            default:
            break;
        }
        return $mode;
    }



}
