<?php namespace App;

use KDuma\Eloquent\Model;

class Track extends Model {
    protected $revisionable = ['artist', 'title'];
}
