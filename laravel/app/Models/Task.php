<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;
  
  class Task extends Model {
    
    protected $table = 'tasks';
    protected $fillable = ['code', 'user_id', 'created_by', 'task', 'priority', 'status', 'due_at', 'completed_at'];
    
    // relation
    public function user() {
      return $this->belongsTo('App\Models\User');
    }
    public function taskfollower() {
      return $this->hasMany('App\Models\Taskfollower');
    }
    public function taskattachment() {
      return $this->hasMany('App\Models\Taskattachment');
    }
    public function taskcomment() {
      return $this->hasMany('App\Models\Taskcomment');
    }
    public function tasklist() {
      return $this->belongsToMany('App\Models\Tasklist', 'tasklist_tasks', 'tasklist_id', 'task_id');
    }
    public function tasklisttask() {
      return $this->belongsToMany('App\Models\Tasklisttask');
    }
  }