<?php

  class Taskrefresh extends Model {
    
    protected $table = 'tasklistrefreshdates';
    protected $fillable = ['user_id', 'refresh_date'];
    
    // relation
    public function user() {
      return $this->belongsTo('App\Models\User');
    }
    
  }