<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Taskrefresh extends Model {
    
    protected $table = 'tasklistrefreshdates';
    protected $fillable = ['user_id', 'refresh_date'];
    
    // relation
    public function user() {
      return $this->belongsTo('App\Models\User');
    }
    
  }