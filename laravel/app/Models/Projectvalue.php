<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Projectvalue extends Model {
    
    protected $table = 'projectvalues';
    protected $fillable = ['project_id', 'company_id', 'value'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
  }