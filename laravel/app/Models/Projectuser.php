<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Projectuser extends Model {
    
    protected $table = 'projectusers';
    protected $fillable = ['project_id', 'user_id', 'access', 'role', 'status'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
  }