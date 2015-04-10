<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;
  
  class Taskcomment extends Model {
    
    protected $table = 'taskcomments';
    protected $fillable = ['task_id', 'user_id', 'comment_type', 'comment', 'status'];
    
    // relation
    public function task() {
      return $this->belongsTo('App\Models\Task');
    }
    
  }