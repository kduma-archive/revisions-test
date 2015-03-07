<?php namespace App;

use App\Eloquent\Model;

class Club extends Model {

    protected $revisionable = ['name', 'city', 'postal_code', 'address'];

    function reports(){
        return $this->hasMany('\App\Report');
    }
}
