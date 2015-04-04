<?php

  class Plansetpdf extends Model {
    
    protected $table = 'plansetpdfs';
    protected $fillable = ['planset_id', 'file_id', 'status'];
      
    // relation
    public function planset() {
      return $this->belongsTo('App\Models\Planset');
    }
    
  }