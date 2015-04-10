<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;
  use Auth;  
  use DB;
  use Response;
  
class PlanroomController extends Controller {

  public function showPlanroom() {
    
    PlanroomController::findUnprocessedSets();
    PlanroomController::findUnprocessedSheets();
    
    $discipline = Input::get('discipline');
    $set = Input::get('set');
    
    if($discipline != '' && $discipline != 'All Disciplines') {
      Session::put('discipline_text', $discipline);
      $discipline_filter = $discipline;
    } else {
      Session::put('discipline_text', 'All Disciplines');
      $discipline_filter = '';
    }
    
    $currentset = DB::table('plansets')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('plansets.set_status', '=', 'active')
            ->where('plansets.set_code', '=', $set)
            ->first(array('set_code', 'set_name', 'set_date'));
            
    if($set != '' && $set != 'Current Set') {
      Session::put('set_text', date('m-d-Y', strtotime($currentset->set_date)) . ' ' . $currentset->set_name);
      Session::put('set_code', $currentset->set_code);
      $set_filter = $set;
    } else {
      Session::put('set_text', 'Current Set');
      Session::put('set_code', '');
      $set_filter = '';
    }
    
    $sets = DB::table('plansets')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('plansets.set_status', '=', 'active')
            ->orderby('set_date', 'desc')
            ->get(array('id', 'set_code', 'set_name', 'set_date'));
    
    $sheets = DB::table('plansetsheets AS t1')
            ->leftjoin('plansets', 'plansets.id', '=', 't1.planset_id')
            ->leftjoin('plansetsheets AS t2', function($query) {
              $query->on('t1.sheet_number','=','t2.sheet_number');
              $query->on('t1.sheet_revision','<','t2.sheet_revision');
            })
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('t1.sheet_status', '=', 'processed')
            ->where('t2.id', null)
            ->where(function($query) use ($discipline_filter) {
              if($discipline_filter != '') {
               $query->where('t1.sheet_discipline', '=', $discipline_filter);
              }
            })
            ->where(function($query) use ($set_filter) {
              if($set_filter != '') {
               $query->where('plansets.set_code', '=', $set_filter);
              }
            })
            ->orderby('t1.sheet_discipline', 'asc')
            ->orderby(DB::raw('LENGTH(t1.sheet_number) asc, t1.sheet_number'), 'asc')
            ->get(array('t1.*'));
            
    $disciplines = DB::table('plansetsheets')
            ->leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('plansetsheets.sheet_status', '=', 'processed')
            ->where('plansetsheets.sheet_status', '=', 'processed')
            ->orderby('plansetsheets.sheet_discipline', 'asc')
            ->groupby('plansetsheets.sheet_discipline')
            ->get(array('plansetsheets.sheet_discipline'));
    
    return view('planroom.grid')
            ->with(array(
                'sheets' => $sheets,
                'sets' => $sets,
                'disciplines' => $disciplines
            ));
  }
  
  public function showSheet($code) {
    
    $sheet = DB::table('plansetsheets')
            ->select(DB::raw('
                    sheet_code, 
                    sheet_number, 
                    sheet_name,
                    max(sheet_revision) as sheet_revision
                  '))
            ->leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('sheet_code', '=', $code)
            ->where('sheet_status', '=', 'processed')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->groupby('sheet_number')
            ->first();
    
    $sheets = DB::table('plansetsheets AS t1')
            ->leftjoin('plansets', 'plansets.id', '=', 't1.planset_id')
            ->leftjoin('plansetsheets AS t2', function($query) {
              $query->on('t1.sheet_number','=','t2.sheet_number');
              $query->on('t1.sheet_revision','<','t2.sheet_revision');
            })
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('t1.sheet_status', '=', 'processed')
            ->where('t2.id', null)
            ->orderby('t1.sheet_discipline', 'asc')
            ->orderby(DB::raw('LENGTH(t1.sheet_number) asc, t1.sheet_number'), 'asc')
            ->get(array('t1.*'));
    
    foreach($sheets as $key => $sheetnp) {
      if($sheetnp->sheet_code == $sheet->sheet_code) {
        $previous = prev($sheets);
        $previous = prev($sheets);
      }
    }
    foreach($sheets as $key => $sheetnp) {
      if($sheetnp->sheet_code == $sheet->sheet_code) {
        $next = current($sheets);
      }
    }
            
    return view('planroom.sheet')
            ->with(array(
                'sheet' => $sheet,
                'previous' => $previous,
                'next' => $next
            ));
  }
  
  public function reviewSheet() {
    $sheetcount = Plansetsheet::leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('sheet_status', '=', 'unprocessed')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->count();
    
    $sheet = Plansetsheet::leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('sheet_status', '=', 'unprocessed')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->first();
        
//    $tesseract = new TesseractOCR(__DIR__ . '/../../public/images/test.png');
//    $tesseract->setTempDir(__DIR__ . '/../../public/uploads');
//    echo $tesseract->recognize();

    return view('planroom.review')
            ->with(array(
                'sheet' => $sheet,
                'sheetcount' => $sheetcount
            ));
  }
  
  public function publishSheet() {
    
    // Get post data
    $date = new DateTime(Input::get('sheet_date'));
    $sheet_date = $date->format('Y-m-d H:i:s');
    
    $sheetdata = array (
      'sheet_number' => Input::get('sheet_number'),
      'sheet_name' => Input::get('sheet_name'),
      'sheet_date' => $sheet_date,
      'sheet_discipline' => Input::get('sheet_discipline'),
      'sheet_revision' => Input::get('sheet_revision'),
      'sheet_status' => 'processed'
    );
    
    Plansetsheet::where('sheet_code', '=', Input::get('sheet_code'))
            ->update($sheetdata);
            
    $sheetcount = Plansetsheet::leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('sheet_status', '=', 'unprocessed')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->count();
    
    if($sheetcount > 0) {
      return Redirect::to('planroom/sheets/review');
    } else {
      return Redirect::to('planroom');
    }
  }
  
  public function checkRevision() {  
    
    $sheet_number = Input::get('sheet_number');
    
    $sheetdata = DB::table('plansetsheets')
            ->select(DB::raw('sheet_name, max(sheet_revision) as sheet_revision'))
            ->leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('sheet_status', '=', 'processed')
            ->where('sheet_number', '=', $sheet_number)
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->first();
    
    if($sheetdata->sheet_revision != null) {
      $revision = $sheetdata->sheet_revision + 1;
    } else {
      $revision = 0;
      $sheetdata->sheet_name = "";
    }
    
    return Response::json(array(
      'sheet_name' => $sheetdata->sheet_name,
      'sheet_revision' => $revision
    ));
  }
  
  public function uploadModal() {
    $sets = DB::table('plansets')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('plansets.set_status', '=', 'active')
            ->orderby('set_date', 'asc')
            ->get(array('set_code', 'set_name', 'set_date'));
    
    return view('planroom.modal.upload')->with(array(
                'sets' => $sets
            ));
  }
  
  public function uploadSet() {
    
    // Check if using an existing set
    if(Input::get('addtoset') == 'yes') {
      $planset = DB::table('plansets')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('plansets.set_code', '=', Input::get('set_code'))
            ->first(array('id'));
    } else {
      // Get post data
      $date = new DateTime(Input::get('set_date'));
      $datestamp = $date->format('Y-m-d H:i:s');

      $setdata = array (
        'user_id' => Auth::User()->id,
        'company_id' => Session::get('company_id'),
        'project_id' => Session::get('project_id'),
        'set_code' => Controller::RandomString('15'),
        'set_name' => Input::get('set_name'),
        'set_date' => $datestamp,
        'status' => 'active'
      );
      $planset = Planset::create($setdata);
    }
    
    // Files  
    $file_id = Input::get('file_id');
    foreach($file_id as $key => $i) {
      $attachment_data = array (
        'planset_id' => $planset->id,
        'file_id' => $file_id[$key],
        'status' => 'unprocessed'
      );
      Plansetpdf::create($attachment_data);
    }
    
    return Redirect::to('planroom');
  }

  public function findUnprocessedSets() {
    
    $setpdfs = Plansetpdf::leftjoin('plansets', 'plansets.id', '=', 'plansetpdfs.planset_id')
            ->where('status', '=', 'unprocessed')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->count(array('plansetpdfs.id'));
    if($setpdfs > 0) {
      Session::put('unprocessed_plansets', true);
    } else {
      Session::put('unprocessed_plansets', false);
    }
  }

  public function findUnprocessedSheets() {
    
    $setsheets = Plansetsheet::leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('sheet_status', '=', 'unprocessed')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->count(array('plansetsheets.id'));
    if($setsheets > 0) {
      Session::put('unprocessed_plansheets', true);
    } else {
      Session::put('unprocessed_plansheets', false);
    }
  }
  
  public function processModal() {
    return view('planroom.modal.process');
  }
  
  public function processSets() {
    
    // No execution timeout
    ini_set('max_execution_time', 0);
    
    // Get unprocessed pdfs
    $setpdfs = Plansetpdf::leftjoin('plansets', 'plansets.id', '=', 'plansetpdfs.planset_id')
            ->leftjoin('s3files', 's3files.id', '=', 'plansetpdfs.file_id')
            ->where('plansetpdfs.status', '=', 'unprocessed')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->get(array(
                'plansetpdfs.id AS pdf_id',
                'plansets.id AS planset_id',
                's3files.id AS file_id',
                's3files.file_bucket',
                's3files.file_path',
                's3files.file_name'
              ));
    
    // Define path to save tiles
    $s3bucket = 'tiles.aecore.com';
    // Temp tile location for processing
    $file_location_temp = __DIR__ . '/../../public/uploads';
    $dir_temp = $file_location_temp . '/' . Controller::RandomString('15');
    mkdir($dir_temp);
    
    // Make s3 object
    $s3 = AWS::get('s3');
    
    header( 'Content-type: text/html; charset=utf-8' );
    // Progress bar update
    for($a=0; $a<=5; $a++){
      echo 1;
      flush();
      ob_flush();
    }
    
    $pdf_count = count($setpdfs);
    
    foreach($setpdfs AS $pdf) {
      
      // Get authenticated url to pdf file
      $authpath = $s3->getObjectUrl($pdf->file_bucket, $pdf->file_path . '/' . $pdf->file_name);
      
      // Create a local copy of the pdf file
      copy($authpath, $dir_temp . '/' . $pdf->file_name);
          
      // Check pdf size
      $end_directory = $dir_temp;
      $new_path = preg_replace('/[\/]+/', '/', $end_directory.'/'.substr($pdf->file_name, 0, strrpos($pdf->file_name, '/')));      
      $fpdi = new \fpdi\FPDI();
      $pagecount = $fpdi->setSourceFile($dir_temp . '/' . $pdf->file_name); // How many pages?

      // Split each page into a new PDF
      for ($i = 1; $i <= $pagecount; $i++) {
        $new_pdf = new \fpdi\FPDI();
        $new_pdf->AddPage();
        $new_pdf->setSourceFile($dir_temp . '/' . $pdf->file_name);
        $new_pdf->useTemplate($new_pdf->importPage($i), null, null, 0, 0, true);

        // Save the file
        $new_filename = $end_directory. '/' . str_replace('.pdf', '', $pdf->file_name).'_'.$i.".pdf";
        $new_pdf->Output($new_filename, "F"); 
      }
            
      // Progress bar update
      for($b=0; $b<=9; $b++){
        echo 1;
        flush();
        ob_flush();
      }
      
      // Process the PDF's
      for ($i = 1; $i <= $pagecount; $i++) {
        
        $sheet_code = Auth::User()->company_id . Controller::RandomString('13');
        
        // Save pdf to s3 for downloading sheets
        $s3 = AWS::get('s3');
        $s3->putObject(array(
          'ACL'                 => 'public-read',
          'Bucket'              => 'tiles.aecore.com',
          'ContentDisposition'  => 'attachment',
          'Key'                 => $sheet_code . '/' . $sheet_code . '.pdf',
          'SourceFile'          => $dir_temp . '/' . str_replace('.pdf', '', $pdf->file_name).'_'.$i.'.pdf',
        ));
    
        $sheetcount = Plansetsheet::leftjoin('plansets', 'plansets.id', '=', 'plansetsheets.planset_id')
            ->where('plansets.project_id', '=', Session::get('project_id'))
            ->where('plansetsheets.sheet_code', '=', $sheet_code)
            ->count();
        if($sheetcount > 0) {
          // Pick a new random string, one try
          $sheet_code = Auth::User()->company_id . Controller::RandomString('13');;
        }
        
        $sub_dir_temp = $dir_temp . '/' . $sheet_code;
        mkdir($sub_dir_temp);
    
        // Convert PDF to PNG, place in temp folder
        exec("convert -density 150 " . $dir_temp . "/" . str_replace('.pdf', '', $pdf->file_name)."_".$i.".pdf -flatten -quality 100 " . $sub_dir_temp . "/" . str_replace('.pdf', '', $pdf->file_name)."_".$i.".png");
        
        // Progress bar update
        for($c=0; $c<=(14/$pagecount); $c++){
          echo 1;
          flush();
          ob_flush();
        }
      
        // Location to base png image in subdirectory for each sheet
        $png_file_path = $sub_dir_temp . '/' . str_replace('.pdf', '', $pdf->file_name).'_'.$i.'.png';
        
        // Make tiles
        $map_tiler = new MaptilerController($png_file_path, $sheet_code, array(
          'tiles_path' => $sub_dir_temp . '/',
          'zoom_min' => 1,
          'zoom_max' => 3
        ));
        $map_tiler->process(true);
        
        // Progress bar update
        for($d=0; $d<=(60/$pagecount); $d++){
          echo 1;
          flush();
          ob_flush();
        }
      
      Plansetpdf::where('planset_id', '=', $pdf->planset_id)
            ->update(['status'=>'processed']);
        
      // Update sheet path
      $sheet_data = array (
          'planset_id' => $pdf->planset_id,
          'sheet_code' => $sheet_code,
          'sheet_status' => 'unprocessed'
        );
      Plansetsheet::create($sheet_data);
      }
      
      // Removes cycled references and closes the file handles of the parser objects
      $fpdi->cleanUp();
      
      // Recursively delete a directory
      File::deleteDirectory($dir_temp);
    }
  }
  
}