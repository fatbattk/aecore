<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Tasklist extends Model {
    
    protected $table = 'tasklists';
    protected $fillable = ['listcode', 'list', 'user_id', 'status'];
    
    // relation
    public function task() {
      return $this->belongsToMany('App\Models\Task', 'tasklist_tasks', 'tasklist_id', 'task_id');
    }
    public function user() {
      return $this->belongsTo('App\Models\User');
    }
    public function tasklisttask() {
      return $this->belongsToMany('App\Models\Tasklisttask');
    }    
  }