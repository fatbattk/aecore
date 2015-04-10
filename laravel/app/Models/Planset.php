<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Planset extends Model {
    
    protected $table = 'plansets';
    protected $fillable = ['user_id', 'company_id', 'project_id', 'set_code', 'set_name', 'set_date', 'set_status'];
      
    // relation
    public function plansetpdf() {
      return $this->hasMany('App\Models\Plansetpdf');
    }
    
    public function plansetsheet() {
      return $this->hasMany('App\Models\Plansetsheet');
    }
  }