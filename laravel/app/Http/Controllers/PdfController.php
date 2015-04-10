<?php

  namespace App\Http\Controllers;

  use Illuminate\Routing\Controller;
  use Illuminate\Support\Facades\Validator;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Support\Facades\Redirect;
  use Auth;
  use Carbon;
  use DateTime;
  use Timezone;
  use Session;
  use Hash;
  use DB;
  use URL;
  use Response;
  use TCPDF;
  
  use App\Models\Companyavatar;
  
  
class MYPDF extends TCPDF {
    
  //Page header
  public function Header() {
      
      global $page_title;
      global $page_subtitle;
            
      // Company logo
      $logo = new companyavatar();
      $image_file = $logo->getCompanyLogo(Auth::User()->company_id);
      list($width, $height) = getimagesize($image_file);
      if($width > ($height*3)) { $img_ht = "0.5"; } elseif($width > ($height*2)) { $img_ht = "0.65"; } else { $img_ht = "0.75"; }

      $this->Image($image_file, '', '', '', $img_ht, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
      // Set font
      $this->SetFont('helvetica', 'B', 20);
      // Title
      //$this->SetFillColor(240,50,20);
      //Cell($w,$h = 0,$txt = '',$border = 0,$ln = 0,$align = '',$fill = false,$link = '',$stretch = 0,$ignore_min_height = false,$calign = 'T',$valign = 'M')
      $this->Cell(0, 0, $page_title, 0, false, 'R', false, '', 0, false, 'T', 'T');
      $this->Ln();
      $this->SetFont('helvetica', 'R', 12);
      $this->Cell(0, 0, $page_subtitle, 0, false, 'R', false, '', 0, false, 'T', 'T');
      $this->Ln();
  }

  // Page footer
  public function Footer() {
      // Position at 15 mm from bottom
      $this->SetY(-0.5);
      // Set font
      $this->SetFont('helvetica', 'I', 8);
      // Page number
      $this->Cell(3, 0.3, Session::get('company_name'), 0, false, 'L', false, '', 0, false, 'T', 'M');
      $this->Cell(0, 0.3, 'Page ' . $this->PageNo() . ' of ' . $this->getNumPages(), 0, false, 'R', false, '', 0, false, 'T', 'M');
  }
}

class PdfController extends Controller {
  
  public function pdfTaskList() {
    
    global $page_title;
    $page_title = 'My Tasks';
    
    global $page_subtitle;
    $page_subtitle = Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'F d, Y');
    
    $file_name = Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'Y-m-d') . ' Task List';
    
    $pdf = new MYPDF('P', 'in', 'LETTER', true, 'UTF-8', false);
    
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(Auth::User()->name);
    $pdf->SetTitle($file_name);
    
    // Extend page margin based on logo size
    $logo = new companyavatar();
    $image_file = $logo->getCompanyLogo(Auth::User()->company_id);
    list($width, $height) = getimagesize($image_file);
    if($width > ($height*3)) { $img_ht = "0.5"; } elseif($width > ($height*2)) { $img_ht = "0.65"; } else { $img_ht = "0.75"; }
    
    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, 0.75+$img_ht, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $listcode = null;
    $filter = 'active';
    
    $tasks= DB::table('tasks')
            ->select(['tasks.task', 'tasks.status', 'tasks.due_at', 'tasks.code', 'tasks.priority', DB::raw('tasks.due_at IS NULL AS due_atNull')])
            ->leftjoin('tasklist_tasks', 'tasks.id', '=', 'tasklist_tasks.task_id')
            ->leftjoin('tasklists', 'tasklist_tasks.tasklist_id', '=', 'tasklists.id')
            ->leftjoin('tasklistrefreshdates', 'tasks.user_id', '=', 'tasklistrefreshdates.user_id')
            ->where('tasks.user_id', '=', Auth::User()->id)
            ->where(function($query) use ($listcode) {
              if($listcode != null) {
               $query->where('tasklists.listcode', '=', $listcode);
               $query->where('tasklist_tasks.status', '=', 'active');
              }
            })
            ->where(function($query_a) use ($filter) {
              $query_a->where('tasks.status', '=', $filter);
              $query_a->orwhere(function($query_b) {
                $query_b->where('tasks.status', '=', 'complete');
                $query_b->where(DB::raw('date_format(tasklistrefreshdates.refresh_date, \'%Y%m%d%H%i%s\')'), '<', DB::raw('date_format(tasks.completed_at, \'%Y%m%d%H%i%s\')'));
              });
            })
            ->orderby('tasks.priority', 'desc')
            ->orderBy('due_atNull')
            ->orderby('tasks.due_at', 'asc')
            ->groupby('tasks.id')
            ->get();
    
    $pdf->SetFont('helvetica', 'R', 11);
    $html = '<table border="0" mobilepadding="3" mobilespacing="0">';
    foreach($tasks as $task) {
      $html = $html.'
        <tr>
          <td style="width:12px;"><img src="' . URL::asset('css/img/sprites/checkbox.png') . '"/></td>
          <td style="width:5px;"></td>
          <td style="width:515px;">' . $task->task . '</td>
        </tr>';
    }
    $html = $html.'</table>';
    
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false);
    
    // Output PDF document
    return Response::make($pdf->Output($file_name, 'I'), 200, array('Content-Type' => 'application/pdf'));
    
  }
  
  public function pdfTeam() {
    
    global $page_title;
    $page_title = 'Team Directory';
    
    global $page_subtitle;
    $page_subtitle = '#'.Session::get('project_number') . ' ' . Session::get('project_name');
    $file_name = Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'Y-m-d') . ' Team Directory';
    
    $pdf = new MYPDF('P', 'in', 'LETTER', true, 'UTF-8', false);
    
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(Auth::User()->name);
    $pdf->SetTitle($file_name);
    
    // Extend page margin based on logo size
    $logo = new companyavatar();
    $image_file = $logo->getCompanyLogo(Auth::User()->company_id);
    list($width, $height) = getimagesize($image_file);
    if($width > ($height*3)) { $img_ht = "0.5"; } elseif($width > ($height*2)) { $img_ht = "0.65"; } else { $img_ht = "0.75"; }
    
    // set margins
    $pdf->SetMargins(0.5, 0.75+$img_ht, 0.5);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $listcode = null;
    $filter = 'active';
    
    $members = DB::table('projectusers')
                ->leftjoin('users', 'projectusers.user_id', '=', 'users.id')
                ->leftjoin('companys', 'users.company_id', '=', 'companys.id')
                ->leftjoin('companylocations', 'companys.id', '=', 'companylocations.company_id')
                ->leftjoin('companyphones', 'companys.id', '=', 'companyphones.company_id')
                ->leftjoin('userphones', 'users.id', '=', 'userphones.user_id')
                ->where('projectusers.project_id', '=', '' . Session::get('project_id') . '')
                ->where('projectusers.status', '!=', 'disabled')
                ->where('users.status', '!=', 'disabled')
                ->orderby('users.name', 'asc')
                ->groupby('users.id')
                ->get(array(
                    'users.id',
                    'users.identifier',
                    'users.name',
                    'users.title',
                    'users.email',
                    'users.company_id',
                    'users.company_join_status',
                    'userphones.direct',
                    'userphones.mobile',
                    'projectusers.access AS projectuser_access',
                    'projectusers.status AS projectuser_status',
                    'companys.name AS company',
                    'companyphones.type AS cphone_type',
                    'companyphones.number AS cphone_number',
                    'companylocations.street',
                    'companylocations.city',
                    'companylocations.state',
                    'companylocations.zipcode',
                    'companylocations.country',
                ));
    
    $useravatar = new Useravatar;
    $pdf->SetFont('helvetica', 'R', 10);
    
    $html = '<p>' . Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'F d, Y') . '</p>';
    
    $html = $html.'<table border="0">';
    foreach($members AS $member) {
      $html = $html.'
        <tr>
          <td style="width:45px;"><img src="' . $useravatar->getUserAvatar($member->id, 'lg') . '" height="37px;" width="37px;" /></td>
          <td style="width:150px;">
            <strong>' . $member->name . '</strong><br>
            ' . $member->title . '<br>
            ' . '<a href="mailto:' . $member->email . '">' . $member->email . '</a>
          </td>
          <td style="width:180px;">
            ' . $member->company . '<br>
            ' . $member->street . '<br>
            ' . $member->city . ', ' . $member->state . ' ' . $member->zipcode . '
          </td>
          <td style="width:164px;">
            ' . 'Mobile: ' . $member->mobile . '<br>
            ' . 'Direct: ' . $member->direct . '<br>
            ' . 'Office: ' . $member->cphone_number . '
          </td>
        </tr>
        <tr><td colspan="4" style="border-bottom:1px solid #ddd;"></td></tr>
        <tr><td colspan="4"></td></tr>';
    }
    $html = $html.'</table>';
    
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false);
    
    // Output PDF document
    return Response::make($pdf->Output($file_name, 'I'), 200, array('Content-Type' => 'application/pdf'));
    
  }

  public function pdfDrawingLog() {
    
    global $page_title;
    $page_title = 'Drawing Log';
    
    global $page_subtitle;
    $page_subtitle = '';
    
    $file_name = Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'Y-m-d') . ' Drawing Log';
    
    $pdf = new MYPDF('P', 'in', 'LETTER', true, 'UTF-8', false);
    
    // set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor(Auth::User()->name);
    $pdf->SetTitle($file_name);
    
    // Extend page margin based on logo size
    $logo = new companyavatar();
    $image_file = $logo->getCompanyLogo(Auth::User()->company_id);
    list($width, $height) = getimagesize($image_file);
    if($width > ($height*3)) { $img_ht = "0.5"; } elseif($width > ($height*2)) { $img_ht = "0.65"; } else { $img_ht = "0.75"; }
    
    // set margins
    $pdf->SetMargins(0.5, 0.75+$img_ht, 0.5);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $listcode = null;
    $filter = 'active';
    
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
            ->get(array('t1.*', 'plansets.set_name'));
            
    $pdf->SetFont('helvetica', 'R', 10);
    
    $html = '<p>' . Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'F d, Y') . '</p>';
    $html = $html.'<p>Project #'.Session::get('project_number') . '<br>' . Session::get('project_name') . '</p>';
    
    $html = $html.'<table border="0" cellpadding="4" cellspacing="0">
        <thead>
          <tr style="background-color:#000; color:#FFF; font-weight:bold;">
            <th style="width:60px;">Number</th>
            <th style="width:215px;">Title</th>
            <th style="width:70px;">Date</th>
            <th style="width:30px;text-align:center;">Rev</th>
            <th style="width:163px;">Set Name</th>
          </tr>
        </thead>
        <tbody>';
    foreach($sheets AS $i => $sheet) {
      if(($i % 2 != 0)) {
        $background = 'background-color:#f2f2f2;';
      } else {
        $background = '';
      }
      
      $html = $html.'
          <tr>
            <td style="width:60px; ' . $background . '">' . $sheet->sheet_number . '</td>
            <td style="width:215px; ' . $background . '">' . $sheet->sheet_name . '</td>
            <td style="width:70px; ' . $background . '">' . date('m-d-Y', strtotime($sheet->sheet_date)) . '</td>
            <td style="width:30px;text-align:center; ' . $background . '">' . $sheet->sheet_revision . '</td>
            <td style="width:163px; ' . $background . '">' . $sheet->set_name . '</td>
          </tr>';
    }
    $html = $html.'</tbody></table>';
    
    $pdf->AddPage();
    $pdf->writeHTML($html, true, false, true, false);
    
    // Output PDF document
    return Response::make($pdf->Output($file_name, 'I'), 200, array('Content-Type' => 'application/pdf'));
    
  }  
}