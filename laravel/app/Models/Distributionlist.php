<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Distributionlist extends Model {
    
    protected $table = 'distributionlists';
    protected $fillable = ['code', 'project_id', 'list_name', 'list_status'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
    public function distributionlistuser() {
      return $this->belongsTo('App\Models\Distributionlistuser');
    }
    
  }