<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Projectnumber extends Model {
    
    protected $table = 'projectnumbers';
    protected $fillable = ['project_id', 'company_id', 'number'];
    
    // relation
    public function project() {
      return $this->belongsTo('App\Models\Project');
    }
    
  }