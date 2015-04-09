<script type="text/javascript" src="{!! URL::asset('js/countries.js') !!}"></script>
<script type="text/javascript">
  $(document).ready(function(){
    //Insert country & state
    print_country("country", "state", "United States", "");
  });
</script>


{!! Form::open(array('id'=>'add_company_form', 'url' => 'settings/account/company/create', 'method' => 'post', 'class' => 'form-horizontal')) !!}
  
  <div class="form-group">
    <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
      <p class="form-control-static text-muted">*Please provide accurate information to help Aecore users find and view your company's profile. Fake/test accounts will be removed.</p>
    </div>
  </div>

  <div class="form-group">
    {!! Form::label('name', 'Company', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::text('name', null, array('class' => 'form-control', 'placeholder' => 'Company', 'required'=>'true', 'autofocus'=>'true' )) !!}
      <span class="text-danger">{!! $errors->first('name') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('type', 'Type', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::select('type', array(
          '' => 'Select Company Type',
          'Architect' => 'Architect',
          'Broker' => 'Broker',
          'Construction Manager' => 'Construction Manager',
          'Consultant' => 'Consultant',
          'Engineer' => 'Engineer',
          'General Contractor' => 'General Contractor',
          'Lender' => 'Lender',
          'Municipality' => 'Municipality',
          'Owner' => 'Owner',
          'Subcontractor' => 'Subcontractor',
          'Vendor' => 'Vendor'
        ), null, array('class'=>'form-control', 'required'=>'true'))
      !!}
      <span class="text-danger">{!! $errors->first('type') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('labor', 'Labor', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::select('labor', array(
          'Not Applicable' => 'Not Applicable',
          'Non-union' => 'Non-union',
          'Union' => 'Union'
        ), null, array('class' => 'form-control'))
      !!}
      <span class="text-danger">{!! $errors->first('labor') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('street', 'Street', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::text('street', null, array('class' => 'form-control', 'placeholder' => 'Street', 'required'=>'true' )) !!}
      <span class="text-danger">{!! $errors->first('street') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('city', 'City', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::text('city', null, array('class' => 'form-control', 'placeholder' => 'City', 'required'=>'true' )) !!}
      <span class="text-danger">{!! $errors->first('city') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('country', 'Country', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::select('country', array(
          '' => 'Select Country'
        ), null, array('class' => 'form-control', 'onChange' => 'print_state(\'state\', this.selectedIndex)'))
      !!}
      <span class="text-danger">{!! $errors->first('country') !!}</span>
    </div>
  </div>

  <div class="form-group">
    {!! Form::label('state', 'State', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::select('state', array(
          '' => 'Select State'
        ), null, array('class' => 'form-control'))
      !!}
      <span class="text-danger">{!! $errors->first('state') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('zipcode', 'Zip Code', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::text('zipcode', null, array('class' => 'form-control', 'placeholder' => 'Zip Code', 'required'=>'true' )) !!}
      <span class="text-danger">{!! $errors->first('zipcode') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('main', 'Phone', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::text('main', null, array('class' => 'form-control', 'placeholder' => 'Phone Number' )) !!}
      <span class="text-danger">{!! $errors->first('main') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('fax', 'Fax', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::text('fax', null, array('class' => 'form-control', 'placeholder' => 'Fax Number' )) !!}
      <span class="text-danger">{!! $errors->first('fax') !!}</span>
    </div>
  </div>
  
  <div class="form-group">
    {!! Form::label('website', 'Website', array('class' => 'col-md-3 col-lg-2 control-label')) !!}
    <div class="col-md-9 col-lg-8">
      {!! Form::text('website', null, array('class' => 'form-control', 'placeholder' => 'http://www.aecore.com' )) !!}
      <span class="text-danger">{!! $errors->first('website') !!}</span>
    </div>
  </div>

  <div class="form-group no-margin">
    <div class="col-md-offset-3 col-md-9 col-lg-offset-2 col-lg-8">
      {!! Form::submit('Create Company', array('class' => 'btn btn-success')) !!}
    </div>
  </div>

{!! Form::close() !!}