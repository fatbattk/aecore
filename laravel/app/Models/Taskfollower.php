<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;
  
  class Taskfollower extends Model {
    
    protected $table = 'taskfollowers';
    protected $fillable = ['task_id', 'user_id', 'status'];
    
    // relation
    public function task() {
      return $this->belongsTo('App\Models\Task');
    }
    
  }