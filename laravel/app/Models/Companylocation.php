<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Companylocation extends Model {
    
    protected $table = 'companylocations';
    protected $fillable = ['company_id', 'street', 'city', 'state', 'country', 'zipcode'];
    
    // relation
    public function company() {
      return $this->belongsTo('App\Models\Company');
    }
    
  }