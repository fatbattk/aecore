<?php

// landing page
Route::get('/', function() {
  return 'Welcome to Aecore Alpha! <a href="/login">Login</a>';
});
Route::get('/home', function() {
  return 'Welcome to Aecore Alpha! <a href="/login">Login</a>';
});

// Sign up
Route::get('signup', array('uses' => 'UsersController@showSignup'));
Route::post('signup', array('uses' => 'UsersController@doSignup'));

// Log in
Route::get('login', 'UsersController@showLogin');
Route::post('login', 'UsersController@doLogin');

// Log out
Route::get('logout', array('uses' => 'UsersController@doLogout'));

// Reset password
Route::get('reset', array('uses' => 'UsersController@showReset'));

//
// Authorized user application routes
//
Route::group(array('before' => 'auth'), function(){
    
  // Autocomplete
  Route::post('autocomplete/companies', array('uses' => 'AutocompleteController@Companies'));
  Route::post('autocomplete/users', array('uses' => 'AutocompleteController@Users'));
  Route::post('autocomplete/tasklists', array('uses' => 'AutocompleteController@Tasklists'));
  Route::post('autocomplete/plansheets', array('uses' => 'AutocompleteController@Plansheets'));
  
  // Welcome
  Route::get('welcome/company', function() {
    return View::make('welcome.company');
  });
  
  // Projects
  Route::get('projects', array('uses' => 'ProjectsController@listProjects'));
  Route::get('projects/new', array('uses' => 'ProjectsController@newProject'));
  Route::post('projects/new/save', array('uses' => 'ProjectsController@newProjectSave'));
  Route::get('projects/edit/{code}', array('uses' => 'ProjectsController@editProject'));
  Route::post('projects/edit/{code}/save', array('uses' => 'ProjectsController@editProjectSave'));
  Route::get('projects/launch/{code}', array('uses' => 'ProjectsController@launchProject'));
  
  // Tasks
  Route::get('tasks/{listcode?}', array('uses' => 'TasksController@listTasks'));
  Route::get('tasks/following/{identifier}', array('uses' => 'TasksController@listFollowingTasks'));
  Route::get('tasks/list/refresh', array('uses' => 'TasksController@refreshTasks'));
  Route::post('tasks/list/create', array('uses' => 'TasksController@createTaskList'));
  Route::post('tasks/list/remove', array('uses' => 'TasksController@removeTaskList'));
  Route::post('tasks/create', array('uses' => 'TasksController@createTask'));
  Route::post('tasks/open', array('uses' => 'TasksController@openTask'));
  Route::post('tasks/complete', array('uses' => 'TasksController@completeTask'));
  Route::get('tasks/delete/{code}', array('uses' => 'TasksController@deleteTask'));
  Route::get('tasks/priority/{set_to}/{code}', array('uses' => 'TasksController@priorityChange'));
    // Task details
    Route::get('tasks/details/{code}', array('uses' => 'TasksController@TaskDetails'));
    Route::post('tasks/update', array('uses' => 'TasksController@updateTask'));
    Route::post('tasks/comment', array('uses' => 'TasksController@taskPostComment'));
    Route::post('tasks/follower', array('uses' => 'TasksController@TaskFollower'));
    Route::post('tasks/list', array('uses' => 'TasksController@TaskList'));
    
    Route::post('attachment/upload', array('uses' => 'UploadsController@uploadFile'));
    Route::post('tasks/attachment/{action}/{code}', array('uses' => 'TasksController@TaskAttachment'));
  
  // Dashboard
  Route::get('dashboard', array('uses' => 'DashboardController@showDashboard'));
  
  // Teams
  Route::get('team', array('uses' => 'TeamController@listTeam'));
  Route::get('team/modal/add', array('uses' => 'TeamController@addModal'));
  Route::get('team/modal/help', array('uses' => 'TeamController@helpModal'));
  Route::post('team/add', array('uses' => 'TeamController@addTeam'));
  Route::post('team/remove', array('uses' => 'TeamController@removeMember'));
  Route::post('team/invite', array('uses' => 'TeamController@invite'));
  Route::post('team/admin/add', array('uses' => 'TeamController@makeAdmin'));
    // Distribution modal
    Route::get('team/modal/distribution', array('uses' => 'TeamController@distributionModal'));
    Route::post('team/list/add', array('uses' => 'TeamController@addDistributionList'));
    Route::post('team/list/remove', array('uses' => 'TeamController@removedistributionList'));
    Route::get('team/list/show/{listcode}', array('uses' => 'TeamController@showDistributionList'));
    Route::post('team/userlist/toggle', array('uses' => 'TeamController@toggleDistribution'));
    
  // Plan Room
  Route::get('planroom', array('uses' => 'PlanroomController@showPlanroom'));
  Route::get('planroom/modal/upload', array('uses' => 'PlanroomController@uploadModal'));
  Route::post('planroom/upload', array('uses' => 'PlanroomController@uploadSet'));
  Route::get('planroom/modal/process', array('uses' => 'PlanroomController@processModal'));
  Route::post('planroom/process', array('uses' => 'PlanroomController@processSets'));
  
  Route::get('planroom/sheets/review', array('uses' => 'PlanroomController@reviewSheet'));
  Route::post('planroom/sheets/publish', array('uses' => 'PlanroomController@publishSheet'));
  Route::post('planroom/sheets/checkrevision', array('uses' => 'PlanroomController@checkRevision'));
  Route::get('planroom/sheet/{code}', array('uses' => 'PlanroomController@showSheet'));
  
  // Engineering -> RFI's
  Route::get('rfis', array('uses' => 'RfiController@showRfiLog'));
  
  // Settings / Profile
  Route::get('settings/profile', array('uses' => 'SettingsController@showProfile'));
  Route::post('settings/profile', array('uses' => 'SettingsController@updateProfile'));
  
  Route::post('settings/avatar/upload/{type}', array('uses' => 'UploadsController@uploadAvatar'));
  Route::post('settings/logo/upload', array('uses' => 'UploadsController@uploadLogo'));
  Route::post('settings/logo/upload/complete', function() {
      return Redirect::to('settings/company')
              ->with('UpdateSuccess', '<strong>Success!</strong> Your company logo has been updated!');
    });
  Route::get('settings/avatar/crop/{type}', array('uses' => 'SettingsController@showAvatarCropModal'));
  Route::post('settings/avatar/crop/{type}', array('uses' => 'UploadsController@cropAvatar'));
   
  // Settings / Account / Password
  Route::get('settings/account', array('uses' => 'SettingsController@showAccount'));
  Route::group(array('before' => 'csrf'), function() {
    Route::post('settings/account/password', array('uses' => 'SettingsController@changePassword'));
  });
  
  // Settings / Account / Company
  Route::get('settings/account/company/createform', array('uses' => 'SettingsController@createCompanyForm'));
  Route::post('settings/account/company/create', array('uses' => 'SettingsController@createCompany'));
  Route::post('settings/account/company/join', array('uses' => 'SettingsController@joinCompany'));
  Route::post('settings/account/company/leave', array('uses' => 'SettingsController@leaveCompany'));
  
  // Settings / Account / Delete
  Route::post('settings/account/delete', array('uses' => 'SettingsController@deleteAccount'));
  
  // Settings / Notifications
  Route::get('settings/notifications', array('uses' => 'SettingsController@showNotifications'));
  
  // Settings / Company / About
  Route::get('settings/company', array('uses' => 'SettingsController@showCompanyAbout'));
  Route::post('settings/company/update', array('uses' => 'SettingsController@updateCompany'));
  
  // Settings / Company / Options
  Route::get('settings/company/preferences', array('uses' => 'SettingsController@showCompanyPreferences'));
  
  // Settings / Company / Users
  Route::get('settings/company/users', array('uses' => 'SettingsController@showCompanyUsers'));
  Route::get('settings/company/users/remove/{identifier}', array('uses' => 'SettingsController@showCompanyUserRemoveModal'));
  Route::post('settings/company/users/remove/{identifier}', array('uses' => 'SettingsController@doCompanyUserRemoveModal'));
  Route::get('settings/company/users/admin/{identifier}', array('uses' => 'SettingsController@doCompanyUserMakeAdmin'));
  
  // User Profiles
  Route::get('/profile/{username}', 
      array('as' => 'profile-user', 
            'uses' => 'ProfileController@user'
  ));
  
  // PDFs
  Route::get('pdf/tasks', array('uses' => 'PdfController@pdfTaskList'));
  Route::get('pdf/team', array('uses' => 'PdfController@pdfTeam'));
  Route::get('pdf/drawinglog', array('uses' => 'PdfController@pdfDrawingLog'));
  
});