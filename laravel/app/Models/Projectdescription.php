<?php

  class Projectdescription extends Model {
    
    protected $table = 'projectdescriptions';
    protected $fillable = ['project_id', 'description'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
  }