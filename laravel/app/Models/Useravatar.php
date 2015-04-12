<?php

  namespace App\Models;
  use Illuminate\Auth\Authenticatable;
  use Illuminate\Database\Eloquent\Model;

  use App;
  use DB;
  
  class Useravatar extends Model {
    
    protected $table = 'useravatars';
    protected $fillable = ['file_id_sm', 'file_id_lg'];
    
    // relation
    public function user() {
      return $this->belongsTo('App\Models\User');
    }
    
    public function getUserAvatar($id, $size) {
      $image = DB::table('users')
              ->leftjoin('useravatars', 'users.id', '=', 'useravatars.user_id')
              ->leftjoin('s3files', 'useravatars.file_id_'.$size, '=', 's3files.id')
              ->where('users.id', '=', $id)
              ->first(array('users.id', 'users.email', 's3files.file_bucket', 's3files.file_path', 's3files.file_name'));
      
      //if($image->file_bucket != null) {
      //  $s3 = AWS::get('s3');
      //  return $s3->getObjectUrl($image->file_bucket, $image->file_path . '/' . $image->file_name);
      //} else {
        $hash = md5(strtolower(trim($image->email)));
        return 'http://www.gravatar.com/avatar/' . $hash . '?d=identicon';
      //}
    }
    
  }