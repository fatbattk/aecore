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

  use App\Models\User;
  use App\Models\Task;
  use App\Models\Taskattachment;
  use App\Models\Taskcomment;
  use App\Models\Taskfollower;
  use App\Models\Taskrefresh;
  use App\Models\Tasklist;
  use App\Models\Tasklisttask;

class TasksController extends Controller {
  
  public function listTasks($listcode=NULL) {
    
    $allowed = array('active', 'complete');
    $filter = in_array(Input::get('filter'), $allowed) ? Input::get('filter') : 'active'; // if user type in the url a column that doesnt exist app will default to active
    if($filter == 'active') {
      Session::put('filter_text', 'Open Tasks');
    } elseif($filter == 'complete') {
      Session::put('filter_text', 'Completed Tasks');
    } else {
      Session::put('filter_text', 'Open Tasks');
    }
    
    $lists = DB::table('tasklists')
              ->where('user_id', '=', Auth::User()->id)
              ->where('status', '=', 'active')
              ->orderby('list', 'asc')
              ->get(array('tasklists.id', 'tasklists.listcode', 'tasklists.list'));
    
    $following = DB::table('taskfollowers')
              ->leftjoin('tasks', 'taskfollowers.task_id', '=', 'tasks.id')
              ->leftjoin('users', 'tasks.user_id', '=', 'users.id')
              ->where('taskfollowers.user_id', '=', Auth::User()->id)
              ->where('tasks.status', '=', $filter)
              ->where('tasks.user_id', '!=', Auth::User()->id)
              ->groupby('tasks.user_id')
              ->orderby('users.name', 'asc')
              ->get(array('tasks.user_id', 'users.identifier', 'users.name'));
    
    $mytasks= DB::table('tasks')
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
    
    $completed_count = 0;
    foreach($mytasks as $mytask) {
      
      // Completed tasks
      if($mytask->status == 'complete') {
        $completed_count++;
      }
      
      // Format due date
      if($mytask->due_at == null) {
        $mytask->due_at = '';
      } elseif(Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'Y-m-d') > date('Y-m-d', strtotime($mytask->due_at))) {
        $mytask->due_at = '<span class="text-danger bold">Overdue</span>';
      } elseif(Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'Y-m-d') == date('Y-m-d', strtotime($mytask->due_at))) {
        $mytask->due_at = '<span class="text-success bold">Today</span>';
      } elseif(Timezone::convertFromUTC(Carbon::now()->addDay(), Auth::user()->timezone, 'Y-m-d') == date('Y-m-d', strtotime($mytask->due_at))) {
        $mytask->due_at = 'Tomorrow';
      } else {
        $mytask->due_at = date('D m/d', strtotime($mytask->due_at));
      }
    }
    
    // Get current list name
    $listname = DB::table('tasklists')->where('tasklists.listcode', '=', $listcode)->first();
    if(count($listname) == 0) {
      $listname = 'All Tasks';
      Session::put('listcode', '');
    } else {
      $listname = $listname->list;
      Session::put('listcode', $listcode);
    }
            
    return view('tasks.list')
            ->with(array(
              'mytasks' => $mytasks,
              'lists' => $lists,
              'following' => $following,
              'listname' => $listname,
              'completed_count' => $completed_count
            ));
  }
  
  public function listFollowingTasks($identifier) {
    
    $lists = DB::table('tasklists')
              ->where('user_id', '=', Auth::User()->id)
              ->where('status', '=', 'active')
              ->orderby('list', 'asc')
              ->get(array('tasklists.id', 'tasklists.listcode', 'tasklists.list'));
    
    $following = DB::table('taskfollowers')
              ->leftjoin('tasks', 'taskfollowers.task_id', '=', 'tasks.id')
              ->leftjoin('users', 'tasks.user_id', '=', 'users.id')
              ->where('taskfollowers.user_id', '=', Auth::User()->id)
              ->where('tasks.status', '=', 'active')
              ->where('tasks.user_id', '!=', Auth::User()->id)
              ->groupby('tasks.user_id')
              ->orderby('users.name', 'asc')
              ->get(array('tasks.user_id', 'users.identifier', 'users.name'));
    
    // Get user's id
    $user = DB::table('users')
          ->where('users.identifier', '=', $identifier)
          ->first(array('id','name')); 
    
    
    $theirtasks = DB::table('tasks')
            ->select(['tasks.task', 'tasks.status', 'tasks.due_at', 'tasks.code', 'tasks.priority', DB::raw('tasks.due_at IS NULL AS due_atNull')])
            ->leftjoin('taskfollowers', 'taskfollowers.task_id', '=', 'tasks.id')
            ->leftjoin('tasklist_tasks', 'tasks.id', '=', 'tasklist_tasks.task_id')
            ->leftjoin('tasklists', 'tasklist_tasks.tasklist_id', '=', 'tasklists.id')
            ->leftjoin('tasklistrefreshdates', 'tasks.user_id', '=', 'tasklistrefreshdates.user_id')
            ->where('tasks.user_id', '=', $user->id)
            ->where('taskfollowers.user_id', '=', Auth::User()->id)
            ->where(function($query_a) {
              $query_a->where('tasks.status', '=', 'active');
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
            
    $completed_count = 0;            
    foreach($theirtasks as $theirtask) {
      
      // Completed tasks
      if($theirtask->status == 'complete') {
        $completed_count++;
      }
      
      // Format due date
      if($theirtask->due_at == null) {
        $theirtask->due_at = '';
      } elseif(Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'Y-m-d') > date('Y-m-d', strtotime($theirtask->due_at))) {
        $theirtask->due_at = '<span class="text-danger bold">Overdue</span>';
      } elseif(Timezone::convertFromUTC(Carbon::now(), Auth::user()->timezone, 'Y-m-d') == date('Y-m-d', strtotime($theirtask->due_at))) {
        $theirtask->due_at = '<span class="text-success bold">Today</span>';
      } elseif(Timezone::convertFromUTC(Carbon::now()->addDay(), Auth::user()->timezone, 'Y-m-d') == date('Y-m-d', strtotime($theirtask->due_at))) {
        $theirtask->due_at = 'Tomorrow';
      } else {
        $theirtask->due_at = date('D m/d', strtotime($theirtask->due_at));
      }
    }
      
    Session::put('listcode', 'following/'.$identifier);
    
    return view('tasks.list_following')
            ->with(array(
              'theirtasks' => $theirtasks,
              'lists' => $lists,
              'following' => $following,
              'listname' => $user->name,
              'completed_count' => $completed_count
            ));
  }
  
  public function refreshTasks($listcode=null) {
    $refreshdata = DB::table('tasklistrefreshdates')->where('user_id', '=', Auth::User()->id)->first();
    if(count($refreshdata) == 0) {
      //Insert new
      $data = array (
        'user_id' => Auth::User()->id,
        'refresh_date' => Carbon::now()->format('Y-m-d H:i:s')
      );
      $taskrefresh = new Taskrefresh;
      $taskrefresh->create($data);
    } else {
      //Update
      Taskrefresh::where(['user_id'=>Auth::User()->id])
            ->update(['refresh_date'=>Carbon::now()->format('Y-m-d H:i:s')]);
    }
    return Redirect::to('tasks/'.Session::get('listcode'));
  }
  
  public function createTaskList() {
    // validate the info, create rules for the inputs
    $rules = array(
      'list_name' => 'required'
    );

    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);

    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('tasks/'.Session::get('listcode'));
    } else {
      // Add task list
      $listcode = FunctionsController::RandomString('10');
      $listdata = array (
        'listcode' => $listcode,
        'user_id' => Auth::User()->id,
        'list' => Input::get('list_name')
      );
      $tasklistclass = new Tasklist;
      $tasklistclass->create($listdata);
      
      Session::put('listcode', $listcode);
      return Redirect::to('tasks/'.Session::get('listcode'));
    }
  }
  
  public function removeTaskList() {
    // Remove task list
    $listcode = Input::get('listcode');

    $tasklistclass = new Tasklist;
    $tasklistclass->where(['listcode'=>$listcode])->update(['status'=>'disabled']);

    Session::put('listcode', '');
  }
  
  public function createTask() {
    // validate the info, create rules for the inputs
    $rules = array(
      'task' => 'required'
    );

    // run the validation rules on the inputs from the form
    $validator = Validator::make(Input::all(), $rules);

    // if the validator fails, redirect back to the form
    if ($validator->fails()) {
      return Redirect::to('tasks')
        ->withErrors($validator); // send back all errors to the login form
    } else {
      
      // Get form data
      $task = array (
        'code' => FunctionsController::RandomString('10'),
        'user_id' => Auth::User()->id,
        'created_by' => Auth::User()->id,
        'task' => Input::get('task'),
        'priority' => '1'
      );
      $taskclass = new Task;
      $task_id_instert = $taskclass->create($task)->id;
      
      if(Input::get('to_list') != "") {
        $list = DB::table('tasklists')
              ->where('tasklists.listcode', '=', Input::get('to_list'))
              ->first(array('id'));
    
        // Add task follower
        $listdata = array (
          'task_id' => $task_id_instert,
          'tasklist_id' => $list->id
        );
  
        // Create new entry
        $tasklisttask = new Tasklisttask;
        $tasklisttask->create($listdata);
      }
      
      // Add task comment
      $comment_text = "created this task";
      $this->taskComment($task_id_instert, 'activity', $comment_text);
      
      return Redirect::to('tasks/'.Session::get('listcode'));
    }
  }
  
  public function updateTask() {
    // Get post data from javascript
    $code = Input::get('code');
    $type = Input::get('type');
    $data = Input::get('data');
    
    // Get task id
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $code)
              ->first(array('id'));
    
    // Data validators
    if($type == 'due_at') {
      $date = new DateTime($data);
      $data = $date->format('Y-m-d H:i:s');
      
      // Add task comment
      $comment_text = '<span class="glyphicon glyphicon-calendar"></span> changed date required to <strong>' . date('M d, Y', strtotime($data)) . '</strong>';
      $this->taskComment($task->id, 'activity', $comment_text);
      
    }
    
    // If user reassigns task, add as follower
    if($type == 'user_id') {
      if($data != Auth::User()->id) {
        // Get user's name
        $user = DB::table('users')
              ->where('users.id', '=', $data)
              ->first(array('name'));
        
        // Add current user as a follower
        $this->TaskFollower($code, Auth::User()->id, 'active');
        
        // Add task comment
        $comment_text = '<span class="glyphicon glyphicon-user"></span> assigned this task to ' . $user->name;
        $this->taskComment($task->id, 'activity', $comment_text);
      }
    }
    
    // Update task
    $taskdata = array (
      $type => $data
    );
    $taskclass = new Task;
    $taskclass->where(['code'=>$code])->update($taskdata);
  }
  
  public function taskPostComment() {
    // Get post data from javascript
    $code = Input::get('code');
    $data = Input::get('data');
    
    // Get task id
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $code)
              ->first(array('id'));
    
    // Add task comment
    $this->taskComment($task->id, 'comment', $data);
  }
  
  public function deleteTask($code) {             
    // Update task status
    Task::where(['code'=>$code])
            ->where('user_id', '=', Auth::User()->id)
            ->update(['status'=>'disabled']);
    return Redirect::to('tasks/'.Session::get('listcode'));
  }
  
  public function TaskFollower($code=null, $data=null, $status=null) {
    // Get post data from javascript
    if($code==null) {
      $code = Input::get('code');
    }
    if($data==null) {
      $data = Input::get('data');
    }
    if($status==null) {
      $status = Input::get('status');
    }
    
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $code)
              ->first(array('id'));
    
    // Add task follower
    $taskdata = array (
      'task_id' => $task->id,
      'user_id' => $data,
      'status' => $status
    );
    
    if($status == 'active') {
      // Check if user is already assigned and update status
      $taskfollower = DB::table('taskfollowers')
              ->leftjoin('tasks', 'taskfollowers.task_id', '=', 'tasks.id')
              ->where('tasks.code', '=', $code)
              ->where('taskfollowers.user_id', '=', $data)
              ->get();
      if(count($taskfollower) > 0) {
        // Update status
        Taskfollower::where(['task_id'=>$task->id])
            ->where(['user_id'=>$data])
            ->update(['status'=>$status]);
      } else {
        // Create new entry
        $taskfollower = new Taskfollower;
        $taskfollower->create($taskdata);
      }
    } else {
      Taskfollower::where(['task_id'=>$task->id])
            ->where(['user_id'=>$data])
            ->update(['status'=>$status]);
    }
  }
  
  public function TaskAttachment($action, $code) {
    // Get post data from javascript
    $file_id = Input::get('file_id');
    
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $code)
              ->first(array('id'));
    
    $attachment = new Taskattachment;
    if($action == 'add') {
      foreach($file_id as $key => $i) {        
        $attachment_data = array (
          'task_id' => $task->id,
          'file_id' => $file_id[$key],
          'user_id' => Auth::User()->id
        );
        $attachment->create($attachment_data);
      }
    } elseif($action == 'remove') {
      $attachment->where('task_id', '=', $task->id)
              ->where('file_id', '=', $file_id)
              ->update(['status' => 'disabled']);
    }
  }
   
  public function TaskList() {
    // Get post data from javascript
    $taskcode = Input::get('taskcode');
    $listcode = Input::get('listcode');
    $status = Input::get('status');
    
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $taskcode)
              ->first(array('id'));
    
    $list = DB::table('tasklists')
              ->where('tasklists.listcode', '=', $listcode)
              ->first(array('id'));
    
    // Add task follower
    $taskdata = array (
      'task_id' => $task->id,
      'tasklist_id' => $list->id,
      'status' => $status
    );
    
    if($status == 'active') {
      // Check if user is already assigned and update status
      $tasklist = DB::table('tasklist_tasks')
              ->where('tasklist_id', '=', $list->id)
              ->where('task_id', '=', $task->id)
              ->count();
      if($tasklist > 0) {
        // Update status
        Tasklisttask::where(['task_id'=>$task->id])
            ->where(['tasklist_id'=>$list->id])
            ->update(['status'=>$status]);
      } else {
        // Create new entry
        $tasklisttask = new Tasklisttask;
        $tasklisttask->create($taskdata);
      }
    } else {
      Tasklisttask::where(['task_id'=>$task->id])
            ->where(['tasklist_id'=>$list->id])
            ->update(['status'=>$status]);
    }
  }
  
  public function openTask() {
    // Update task status
    $taskdata = array (
      'status' => 'active'
    );
    $code = Input::get('code');
    
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $code)
              ->first(array('id'));
    
    // Add task comment
    $comment_text = '<span class="glyphicon glyphicon-unchecked"></span> reopened this task';
    $this->taskComment($task->id, 'activity', $comment_text);
    
    Task::where(['code'=>$code])
            ->update($taskdata);
  }
  
  public function completeTask() {
    // Update task status
    $taskdata = array (
      'status' => 'complete',
      'completed_at' => Carbon::now()->format('Y-m-d H:i:s')
    );
    $code = Input::get('code');
    
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $code)
              ->first(array('id'));
    
    // Add task comment
    $comment_text = '<span class="glyphicon glyphicon-check"></span> completed this task';
    $this->taskComment($task->id, 'activity', $comment_text);
    
    $taskclass = new Task;
    $taskclass->where(['code'=>$code])->update($taskdata);
  }
  
  public function priorityChange($set_to, $code) {
    
    // Check current priority
    $task = DB::table('tasks')
              ->where('tasks.code', '=', $code)
              ->first(array('id', 'priority'));
    
    if($set_to != $task->priority) {
      // Update task priority
      $taskdata = array (
        'priority' => $set_to
      );
      Task::where(['code'=>$code])
              ->update($taskdata);

      if($set_to == '3') { $set_to_text = '<span class="label label-danger">high</span>'; }
      if($set_to == '2') { $set_to_text = '<span class="label label-warning">medium</span>'; }
      if($set_to == '1') { $set_to_text = '<span class="label label-primary">low</span>'; }

      // Add task comment
      $comment_text = "changed the task priority to " . $set_to_text;
      $this->taskComment($task->id, 'activity', $comment_text);
    }
    return Redirect::to('tasks/'.Session::get('listcode'));
  }
  
  public function TaskDetails($code) {
    $taskdata = DB::table('tasks')
              ->leftjoin('users', 'tasks.user_id', '=', 'users.id')
              ->where('tasks.code', '=', $code)
              ->where('tasks.status', '!=', 'disabled')
              ->first(array('tasks.id', 'user_id', 'code', 'name', 'created_by', 'task', 'tasks.status', 'priority', 'due_at'));
    
    $taskfollowers = DB::table('taskfollowers')
              ->leftjoin('users', 'taskfollowers.user_id', '=', 'users.id')
              ->leftjoin('tasks', 'taskfollowers.task_id', '=', 'tasks.id')
              ->where('taskfollowers.task_id', '=', $taskdata->id)
              ->where('taskfollowers.status', '=', 'active')
              ->where('taskfollowers.user_id', '!=', $taskdata->user_id)
              ->get(array('taskfollowers.id', 'tasks.code', 'taskfollowers.user_id', 'users.name'));
    
    $tasklists = DB::table('tasklists')
              ->leftjoin('tasklist_tasks', 'tasklists.id', '=', 'tasklist_tasks.tasklist_id')
              ->where('tasklists.status', '=', 'active')
              ->where('tasklist_tasks.task_id', '=', $taskdata->id)
              ->where('tasklist_tasks.status', '=', 'active')
              ->where('tasklists.user_id', '=', $taskdata->user_id)
              ->get(array('tasklists.listcode', 'tasklists.list'));
    
    $attachments = DB::table('taskattachments')
              ->leftjoin('s3files', 'taskattachments.file_id', '=', 's3files.id')
              ->where('task_id', '=', $taskdata->id)
              ->where('status', '=', 'active')
              ->orderby('s3files.file_name', 'asc')
              ->get();
    
    $activitys = DB::table('taskcomments')
              ->leftjoin('users', 'taskcomments.user_id', '=', 'users.id')
              ->where('taskcomments.task_id', '=', $taskdata->id)
              ->where('taskcomments.status', '=', 'active')
              ->orderby('taskcomments.created_at', 'asc')
              ->get(array('users.name', 'taskcomments.user_id', 'taskcomments.comment', 'taskcomments.comment_type', 'taskcomments.created_at'));
    
    return view('tasks.info')
            ->with(array(
              'taskdata' => $taskdata,
              'taskfollowers' => $taskfollowers,
              'tasklists' => $tasklists,
              'attachments' => $attachments,
              'activitys' => $activitys,
              'functionscontroller' => new FunctionsController
            ));
  }
  
  public function taskComment($task_id, $type, $comment_text) {
    $comment_data = array (
      'task_id' => $task_id,
      'user_id' => Auth::User()->id,
      'comment_type' => $type,
      'comment' => $comment_text
    );
    // Create new entry
    $task_comment = new Taskcomment;
    $task_comment -> create($comment_data);
  }
  
}