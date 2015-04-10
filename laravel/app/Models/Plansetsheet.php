<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Plansetsheet extends Model {
    
    protected $table = 'plansetsheets';
    protected $fillable = ['planset_id', 'sheet_code', 'sheet_number', 'sheet_name', 'sheet_date', 'sheet_discipline', 'sheet_s3_path', 'sheet_status'];
      
    // relation
    public function planset() {
      return $this->belongsTo('App\Models\Planset');
    }
    
  }