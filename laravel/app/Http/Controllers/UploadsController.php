<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;
  use Auth;
  use DB;
  use Response;
  use Aws\S3\S3Client;
  
class UploadsController extends Controller {

  // Files
  public function uploadFile() {
    
    $verifyToken = md5('unique_salt' . Input::get('timestamp'));
    
    if(Input::hasFile('Filedata') && Input::get('token') == $verifyToken) {
      
      require_once __DIR__ . '/../../../vendor/autoload.php';
      
      // Get file size & temp location
      $file_location_temp = Input::file('Filedata')->getRealPath();
      $file_size = Input::file('Filedata')->getSize();

      // Remove spaces from file name
      $file_name_upload = str_replace(str_split(':*?&"<>|'), '', Input::file('Filedata')->getClientOriginalName());
      $file_name = str_replace(' ', '-', $file_name_upload);
      
      $ext = strtolower(substr($file_name, strrpos($file_name, '.') + 1));
      $allowed = array("jpg","JPG","png","PNG","jpeg","JPEG","gif","GIF","pdf","PDF","doc","DOC","docx","DOCX","xls","XLS","xlsx","XLSX","ppt","PPT","pptx","PPTX","txt","TXT","dwg","DWG","dxf","DXF","zip","ZIP");
               
      if(in_array($ext, $allowed)) {
        $s3bucket = 'aecore-cdn';
        $s3path = Auth::User()->id . '/' . time();

        // Instantiate the S3 client with your AWS credentials
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => '',
                'secret' => '',
            )
        ));
  
        // Upload the images to s3
        $s3Client->putObject(array(
          'ACL'                 => 'public-read',
          'Bucket'              => $s3bucket,
          'ContentDisposition'  => 'attachment',
          'Key'                 => $s3path . '/' . $file_name,
          'SourceFile'          => $file_location_temp,
        ));

        // Save image info in the database
        $file_data = array (
          'file_bucket' => $s3bucket,
          'file_path' => $s3path,
          'file_name' => $file_name,
          'file_size' => $file_size
        );
        $id = Auth::user()->s3file()->create($file_data);
        return $id->id;
      }
    }
  }

  // Company logos
  public function uploadLogo() {
    
    $verifyToken = md5('unique_salt' . Input::get('timestamp'));
    
    if(Input::hasFile('Filedata') && Input::get('token') == $verifyToken) {
      
      require_once __DIR__ . '/../../../vendor/autoload.php';
      
      // Get file size & temp location
      $file_location_temp = Input::file('Filedata')->getRealPath();
      $file_size = Input::file('Filedata')->getSize();

      // Remove spaces from file name
      $file_name_upload = str_replace(str_split(':*?&"<>|'), '', Input::file('Filedata')->getClientOriginalName());
      $file_name = str_replace(' ', '-', $file_name_upload);
      
      $ext = strtolower(substr($file_name, strrpos($file_name, '.') + 1));
      $allowed = array("jpg","JPG","png","PNG","jpeg","JPEG");
               
      if(in_array($ext, $allowed)) {
        $s3bucket = 'aecore-cdn';
        $s3path = 'logos/'.Auth::User()->company_id;

        // Instantiate the S3 client with your AWS credentials
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => '',
                'secret' => '',
            )
        ));
  
        // Upload the images to s3
        $s3Client->putObject(array(
          'ACL'                 => 'public-read',
          'Bucket'              => $s3bucket,
          'ContentDisposition'  => 'attachment',
          'Key'                 => $s3path . '/' . $file_name,
          'SourceFile'          => $file_location_temp,
        ));

        // Save image info in the database
        $file_data = array (
          'file_bucket' => $s3bucket,
          'file_path' => $s3path,
          'file_name' => $file_name,
          'file_size' => $file_size
        );
        $id = Auth::user()->s3file()->create($file_data);
        
        DB::table('companyavatars')
          ->where(['company_id' => Auth::user()->company['id']])
          ->update(['file_id_logo'=>$id->id]);
        
        return $id->id;
      }
    }
  }
  
  // User or company avatars
  public function uploadAvatar($type) {
    
    $verifyToken = md5('unique_salt' . Input::get('timestamp'));
    
    if(Input::hasFile('Filedata') && Input::get('token') == $verifyToken) {
      
      require_once __DIR__ . '/../../../vendor/autoload.php';
      
      // Get file size & temp location
      $file_location_temp = Input::file('Filedata')->getRealPath();

      // Remove spaces from file name
      $file_name_upload = str_replace(str_split(':*?&"<>|'), '', Input::file('Filedata')->getClientOriginalName());
      $file_name = str_replace(' ', '-', $file_name_upload);
         
      $ext = strtolower(substr($file_name, strrpos($file_name, '.') + 1));
      //$allowed = array("jpg", "png", "jpeg", "gif", "pdf", "doc", "docx", "xls", "xlsx", "ppt", "pptx", "txt", "dwg", "dxf", "zip");
      $allowed_img = array("jpg", "JPG", "png", "PNG", "jpeg", "JPEG", "gif", "GIF");
         
      if(in_array($ext, $allowed_img)){
        
        $file_location_new = __DIR__ . '/../../public/uploads';
        if($type == 'profile') {
          $file_name_new = Auth::User()->id . '-profile-avatar-original.jpg';
        } elseif($type == 'company') {
          $file_name_new = Auth::User()->company['id'] . '-company-avatar-original.jpg';
        }
        
        $img = Image::make($file_location_temp);
        // resize the image to a width of 300 and constrain aspect ratio (auto height)
        $img->resize(600, null, function ($constraint) {
          $constraint->aspectRatio();
        });
        $img->save($file_location_new . '/' . $file_name_new);
      }
    }
  }
  
  public function cropAvatar($type) {
    
    // Get info about image
    $file_location_temp = __DIR__ . '/../../public/uploads';
    
    // Crop size & coordinates
    $w = Input::get('w');
    $h = Input::get('h');
    $x = Input::get('x');
    $y = Input::get('y');
    
    // Make image
    $img_size = array('lg' => 250, 'sm' => 50);
    if($type == 'profile') {
      $img = Image::make($file_location_temp . '/' . Auth::User()->id . '-profile-avatar-original.jpg');
    } elseif($type == 'company') {
      $img = Image::make($file_location_temp . '/' . Auth::User()->company['id'] . '-company-avatar-original.jpg');
    }
    $img->crop($w, $h, $x, $y);
        
    foreach($img_size as $name => $size) {
      
      // Resize & save each temp image
      $file_name = Auth::User()->id . '-' . $name . '-' . time() . '.jpg';
      $img->resize($size, null, function ($constraint) {
        $constraint->aspectRatio();
      });
      $img->save($file_location_temp . '/' . $file_name);
      $file_size = $img->filesize();
      
      $s3bucket = 'aecore-cdn';
      if($type == 'profile') {
        $s3path = 'avatars/' . Auth::User()->id;
      } elseif($type == 'company') {
        $s3path = 'companyavatars/' . Auth::User()->company['id'];
      }
      
        // Instantiate the S3 client with your AWS credentials
        $s3Client = S3Client::factory(array(
            'credentials' => array(
                'key'    => '',
                'secret' => '',
            )
        ));
  
        // Upload the images to s3
        $s3Client->putObject(array(
        'ACL'                 => 'public-read',
        'Bucket'              => $s3bucket,
        'ContentDisposition'  => 'attachment',
        'Key'                 => $s3path . '/' . $file_name,
        'SourceFile'          => $file_location_temp. '/' . $file_name,
      ));
      
      // Save image info in the database
      $image_data = array (
        'file_bucket' => $s3bucket,
        'file_path' => $s3path,
        'file_name' => $file_name,
        'file_size' => $file_size
      );
      $id = Auth::user()->s3file()->create($image_data);
      $file_id_insert = $id->id;
      
      $avatar_data = array (
        'file_id_' . $name => $file_id_insert
      );
      if($type == 'profile') {
        Auth::user() -> useravatar() -> update($avatar_data);
      } elseif($type == 'company') {
        DB::table('companyavatars')
          ->where([ 'company_id' => Auth::user()->company['id'] ])
          ->update($avatar_data);
      }

      //Remove resized image
      File::delete($file_location_temp . '/' . $file_name);
    }
    
    // Delete original file
    if($type == 'profile') {
      File::delete($file_location_temp . '/' . Auth::User()->id . '-profile-avatar-original.jpg');
    } elseif($type == 'company') {
      File::delete($file_location_temp . '/' . Auth::User()->company['id'] . '-company-avatar-original.jpg');
    }    
    
    // Return to profile page
    return Redirect::to('settings/'.$type)
              ->with('UpdateSuccess', '<strong>Success!</strong> Your avatar has been updated.');
  }
  
}