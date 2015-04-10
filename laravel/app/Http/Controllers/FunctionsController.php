<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;


class FunctionsController extends Controller {
  
  public static function RandomString($length) {
    return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
  }
  
  public function formatBytes($size, $precision = 2) { 
    $base = log($size, 1024);
    $suffixes = array('', ' KB', ' MB', ' GB', ' TB');   

    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
  }
  
  public function display_file_icon($file_name) {
    $ext = $this->file_type_icon(strtolower(substr($file_name, strrpos($file_name, '.') + 1)));
    switch($ext) {
      case "pdf":
        echo '<span class="filetype-sprite-small pdf"></span>';
      break;
      case "img":
        echo '<span class="filetype-sprite-small img"></span>';
      break;
      case "txt":
        echo '<span class="filetype-sprite-small txt"></span>';
      break;
      case "doc":
      case "docx":
        echo '<span class="filetype-sprite-small doc"></span>';
      break;
      case "xls":
      case "xlsx":
        echo '<span class="filetype-sprite-small xls"></span>';
      break;
      case "ppt":
      case "pptx":
        echo '<span class="filetype-sprite-small ppt"></span>';
      break;
      case "dwg":
      case "dxf":
        echo '<span class="filetype-sprite-small dwg"></span>';
      break;
    }
  }
  
   //Loads the correct icon by file type
   function file_type_icon($ext) {
      $extensions = array(
                     "png"  => "img",
                     "PNG"  => "img",
                     "jpeg" => "img",
                     "JPEG" => "img",
                     "jpg"  => "img",
                     "JPG"  => "img",
                     "gif"  => "img",
                     "GIF"  => "img",
                     "tiff"  => "img",
                     "TIFF"  => "img",

                     "pdf"  => "pdf",
                     "PDF"  => "pdf",

                     "doc"  => "doc",
                     "DOC"  => "doc",
                     "docx" => "doc",
                     "DOCX" => "doc",
          
                     "txt"  => "txt",
                     "TXT"  => "txt",

                     "xls"  => "xls",
                     "XLS"  => "xls",
                     "xlsx" => "xlsx",
                     "XLSX" => "xlsx",

                     "ppt"  => "ppt",
                     "PPT"  => "ppt",
                     "pptx" => "ppt",
                     "PPTX" => "ppt",
          
                     "dwg"  => "dwg",
                     "DWG"  => "dwg",
                     "dxf"  => "dxf",
                     "DXF"  => "dxf",
          
                     "zip"  => "zip",
                     "ZIP"  => "zip"
          
                   );

      if(isset($extensions[$ext])){
         switch($extensions[$ext]){
            case 'img':
               $type = 'img';
               break;
            case 'pdf':
               $type = 'pdf';
               break;
            case 'doc':
               $type = 'doc';
               break;
            case 'xls':
               $type = 'xls';
               break;
            case 'xlsx':
               $type = 'xlsx';
               break;       
            case 'ppt':
               $type = 'ppt';
               break;             
            case 'dwg':
               $type = 'dwg';
               break;
            case 'dxf':
               $type = 'dxf';
               break;             
            case 'txt':
               $type = 'txt';
               break;
            case 'zip':
               $type = 'zip';
               break;           
         }
         return $type;
      }

   return 'blank';
   } //END FUNCTION
   
  public function getHashtags($string) {  
    $hashtags = FALSE;  
    preg_match_all("/(#\w+)/u", $string, $matches);  
    if($matches) {
      $hashtagsArray = array_count_values($matches[0]);
      $hashtags = array_keys($hashtagsArray);
    }
    return $hashtags;
  }  
}