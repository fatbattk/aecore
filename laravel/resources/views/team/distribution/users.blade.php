<div class="distributeTile" style="border-top:none;">
  <span class="btn btn-xs btn-link pull-right bold" onClick="removeDistributionList('<?php echo $listinfo->code; ?>');" title="Delete this distribution list.">Delete List</span>
  <span class="small light bold" style="line-height:22px;">{!! $listinfo->list_name !!}</span>
</div>
@if(count($members) > 0)
  @foreach($members AS $member)
    <div class="distributeTile">
      <div class="btn-group pull-right" style="margin-top:3px;">
        <button id="onbutton_{!! $member->identifier !!}" class="btn btn-xs {!! $member->onswitch !!}" onClick="toggleDistribution('active','<?php echo $listinfo->code; ?>','<?php echo $member->identifier; ?>');">ON</button>
        <button id="offbutton_{!! $member->identifier !!}" class="btn btn-xs {!! $member->offswitch !!}" onClick="toggleDistribution('disabled','<?php echo $listinfo->code; ?>','<?php echo $member->identifier; ?>');">OFF</button>  
      </div>
      <?php $useravatar = new App\Models\Useravatar; ?>
      <img src="{!! $useravatar->getUserAvatar($member->id, 'sm') !!}" class="avatar_sm" />
      <p class="bold">{!! $member->name !!}</p>
      <p class="small light">{!! $member->company_name !!}</p>
    </div>
  @endforeach
@endif