<?php
namespace App\Eloquent;
use Illuminate\Database\Eloquent\Model as BaseModel;

class Model extends BaseModel {
    protected static function boot()
    {
        parent::boot();


        static::saved(function ($model) {
            $model->postSave();
        });
        static::saving(function ($model) {
            $model->preSave();
        });
    }

    public function __construct(array $attributes = array())
    {
        if($this->revisions_model == '')
            $this->revisions_model = '\\'.str_plural(get_called_class()).'Revision';

        parent::__construct($attributes);

        if($this->revisionable){
            $this->appends += $this->revisionable;
        }
    }


    public function getAttribute($key)
    {
        if(in_array($key, $this->revisionable)){
            return $this->revision->{$key};
        }
        return parent::getAttribute($key);
    }

    public function __call($method, $parameters)
    {
        $isAttr = substr($method, 0, 3) == 'get' && substr($method, -9) == 'Attribute';
        $attr = snake_case(substr($method, 3, -9));
        if($isAttr || in_array($attr, $this->revisionable)){
            return $this->getAttribute($attr);
        }
        if($method == 'revision'){
//            if($this->pivot->track_revision)
//                dd($this->pivot->track_revision);
            return $this->hasOne($this->revisions_model)->latest();
            //->where('id', $this->pivot->track_revision);
        }
        if($method == 'revisions')
            return $this->hasMany($this->revisions_model);




        return parent::__call($method, $parameters);
    }

    protected $revisionable = [];
    protected $revisions_model;
    protected $revision_data = [];

    public function newQuery()
    {
        if(!empty($this->revisionable))
            return parent::newQuery()->with('revision');
        return parent::newQuery();
    }

    public function preSave()
    {
        $this->revision_data = array_intersect_key($this->attributes, array_flip($this->revisionable));
        $this->attributes = array_filter($this->attributes, function($key){
            return !in_array($key, $this->revisionable);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function postSave()
    {
        $this->revisions()->create($this->revision_data);
    }

    public function scopeRevisionId($query, $id)
    {
        return $query->whereHas('revision', function($query) use ($id)
        {
            $query->where('id', $id);

        })->with(['revision' => function($query) use ($id)
        {
            $query->where('id', $id);

        }]);
    }

}