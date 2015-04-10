<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;
  
  class Taskattachment extends Model {
    
    protected $table = 'taskattachments';
    protected $fillable = ['task_id', 'file_id', 'user_id', 'status'];
    
    // relation
    public function task() {
      return $this->belongsTo('App\Models\Task');
    }
    
  }