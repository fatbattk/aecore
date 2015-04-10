<?php

namespace App\Models;

use App\Models\Company;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

use DB;
use App;
use Auth;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'Users';
  protected $fillable = ['name','email','username','password','title','identifier','timezone','status','signup_step','company_id', 'company_join_type', 'company_join_status'];
  
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
      
  public function getGravatarAttribute() {
    $image = DB::table('useravatars')
            ->leftjoin('s3files', 'useravatars.file_id_lg', '=', 's3files.id')
            ->where('useravatars.user_id', '=', Auth::user()->id)
            ->first();
    
//    if(count($image > 0)) {
//      $s3 = AWS::get('s3');
//      return $s3->getObjectUrl($image->file_bucket, $image->file_path . '/' . $image->file_name);
//    } else {
//      $hash = md5(strtolower(trim($this->attributes['email'])));
//      return 'http://www.gravatar.com/avatar/' . $hash . '?d=identicon';
//    }
    
    // Defaulting to gravatar until storage is finalized
    $hash = md5(strtolower(trim($this->attributes['email'])));
    return 'http://www.gravatar.com/avatar/' . $hash . '?d=identicon';
  }
  
  // Relations
  public function userphone() {
    return $this->hasOne('App\Models\Userphone');
  }
  public function useravatar() {
    return $this->hasOne('App\Models\Useravatar');
  }
  public function s3file() {
    return $this->hasMany('App\Models\S3file');
  }
  public function company() {
    return $this->belongsTo('App\Models\Company');
  }
  public function task() {
    return $this->hasMany('App\Models\Task');
  }
  public function tasklist() {
    return $this->hasMany('App\Models\Tasklist');
  }
}
