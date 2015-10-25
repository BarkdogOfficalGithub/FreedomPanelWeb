<?php
$panel->setPageTitle('Account');
$panel->setPageHeader('Account');
$panel->setPageDescription($_SESSION['username']);

if (!$panel->userHasPermission('can_login')) {
  $panel->setPageContent('You do not have permission to access this content');
  return;
}


$content = <<<EOF

<div class="well col-sm-7">
<h3>Change Password</h3>
<form class="form-horizontal">
<div class="form-group">
  <label for="inputPassword3" class="col-sm-2 control-label">Current Password</label>
  <div class="col-sm-10">
    <input type="password" class="form-control" id="currentPassword" placeholder="Current Password">
  </div>
</div>
<div class="form-group">
  <label for="inputPassword3" class="col-sm-2 control-label">New Password</label>
  <div class="col-sm-10">
    <input type="password" class="form-control" id="newPassword" placeholder="New Password">
  </div>
</div>
<div class="form-group">
  <label for="inputPassword3" class="col-sm-2 control-label">Confirm New Password</label>
  <div class="col-sm-10">
    <input type="password" class="form-control" id="newPasswordConfirm" placeholder="Confirm New Password">
  </div>
</div>
<div class="form-group">
  <div class="col-sm-offset-2 col-sm-10">
    <button type="submit" class="btn btn-primary" onclick='event.preventDefault(); changePassword();'>Change Password</button>
  </div>
</div>
<div id='passwordError' style='display: none' class="alert alert-danger alert-dismissible" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>Error!</strong> An error occured while changing password: <span id='passwordErrorText'></span>
</div>
</form>
</div>



EOF;









$panel->setPageContent($content);

?>
