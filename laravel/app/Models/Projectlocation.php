<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Projectlocation extends Model {
    
    protected $table = 'projectlocations';
    protected $fillable = ['project_id', 'street', 'city', 'state', 'country', 'zipcode'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
  }