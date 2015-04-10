<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Tasklisttask extends Model {
    
    protected $table = 'tasklist_tasks';
    protected $fillable = ['tasklist_id', 'task_id', 'status'];
    
    // relation
    public function task() {
      return $this->belongsToMany('App\Models\Task');
    }
    public function tasklist() {
      return $this->belongsToMany('App\Models\Tasklist');
    }
  }