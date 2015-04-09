<?php
  
  namespace App\Models;
  use Illuminate\Auth\Authenticatable;
  use Illuminate\Database\Eloquent\Model;

  class Userphone extends Model {
    
    protected $table = 'userphones';
    protected $fillable = ['mobile', 'direct'];
    
    // relation
    public function user() {
      return $this->belongsTo('App\Models\User');
    }
    
  }