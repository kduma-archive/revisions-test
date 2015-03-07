<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model {

    function club(){
        return $this->belongsTo('\App\Club');
    }

    function tracks(){
        return $this->belongsToMany('\App\Track')
            ->withPivot('track_revision');
    }

}
