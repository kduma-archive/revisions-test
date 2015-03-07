<?php
namespace App\Eloquent;
use Illuminate\Database\Eloquent\Model as BaseModel;


class RevisionsModel extends BaseModel {
    protected $revisioned_model;
    protected $revisioned_accessor;

    protected $guarded = ['id', 'track_id'];
//    protected $hidden = ['id', 'created_at', 'updated_at', 'user_id', 'pivot'];

    public function __construct(array $attributes = array())
    {
        if($this->revisioned_model == ''){
            $class = get_called_class();
            if(substr($class, -8) == 'Revision'){
                $class = substr($class, 0, -8);
            }
            $class = str_singular($class);
            $class = '\\'.$class;
            $this->revisioned_model = $class;
        }
        if($this->revisioned_accessor == ''){
            $accessor = explode('\\', $this->revisioned_model);
            $accessor = end($accessor);
            $this->revisioned_accessor = snake_case($accessor);
        }

        parent::__construct($attributes);



        $this->fillable = [];
        $this->guarded += ['id', $this->revisioned_accessor.'_id'];

    }

    protected static function boot()
    {
        parent::boot();
    }

    public function __call($method, $parameters)
    {
        if($method == $this->revisioned_accessor)
        {
            return $this->belongsTo($this->revisioned_model, $this->revisioned_accessor.'_id');
        }

        return parent::__call($method, $parameters);
    }
}