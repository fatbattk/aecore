<?php

  class Projectuser extends Model {
    
    protected $table = 'projectusers';
    protected $fillable = ['project_id', 'user_id', 'access', 'role', 'status'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
  }