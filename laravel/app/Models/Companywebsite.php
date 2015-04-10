<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Companywebsite extends Model {
    
    protected $table = 'companywebsites';
    protected $fillable = ['company_id', 'url'];
    
    // relation
    public function company() {
      return $this->belongsTo('App\Models\Company');
    }
    
  }