<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Projectdate extends Model {
    
    protected $table = 'projectdates';
    protected $fillable = ['project_id', 'company_id', 'date_start', 'date_finish', 'date_complete'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
  }