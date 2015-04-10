<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Csidivisions extends Model {
    
    protected $table = 'csidivisions';
    protected $fillable = ['csi_code', 'csi_description'];
    
  }