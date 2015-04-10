<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Distributionlistuser extends Model {
    
    protected $table = 'distributionlistusers';
    protected $fillable = ['list_id', 'user_id', 'status'];
    
    // relation    
    public function distributionlist() {
      return $this->belongsTo('App\Models\Distributionlist');
    }
    
  }