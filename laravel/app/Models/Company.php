<?php

  namespace App\Models;
  use Illuminate\Auth\Authenticatable;
  use Illuminate\Database\Eloquent\Model;

  class Company extends Model {
    
    protected $table = 'companys';
    protected $fillable = ['name', 'type', 'labor', 'account_type', 'status'];
      
    // relation
    public function user() {
      return $this->hasMany('App\Models\User');
    }
    public function companylocation() {
      return $this->hasMany('App\Models\Companylocation');
    }
    public function companyphone() {
      return $this->hasMany('App\Models\Companyphone');
    }
    public function companywebsite() {
      return $this->belongsTo('App\Models\Companywebsite');
    }
    public function companyavatar() {
      return $this->belongsTo('App\Models\Companyavatar');
    }
    
  }