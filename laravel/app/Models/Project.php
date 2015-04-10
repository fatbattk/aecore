<?php

  namespace App\Models;
  use Illuminate\Database\Eloquent\Model;

  class Project extends Model {
    
    protected $table = 'projects';
    protected $fillable = ['company_id', 'code', 'name', 'type', 'size', 'size_type', 'image_id', 'submittal_code', 'status'];
      
    // relation
    public function projectlocation() {
      return $this->hasOne('App\Models\Projectlocation');
    }
    public function projectdate() {
      return $this->hasOne('App\Models\Projectdate');
    }
    public function projectdescription() {
      return $this->hasMany('App\Models\Projectdescription');
    }
    public function projectnumber() {
      return $this->hasMany('App\Models\Projectnumber');
    }
    public function projectvalue() {
      return $this->hasMany('App\Models\Projectvalue');
    }
    public function projectuser() {
      return $this->hasMany('App\Models\Projectuser');
    }
    public function distributionlist() {
      return $this->hasMany('App\Models\Distributionlist');
    }
    
  }