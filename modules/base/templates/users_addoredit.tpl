<div class="panel_headline"><?php echo $headline;?></div>
<div id="panel">
<fieldset class="options">

	<legend>User Profile</legend>
	
	<TABLE class="form">
	
		<form method="POST">
		<TR>
			<TH>User Name</TH>
			<TD>
			<?php if ($edit === true):?>
			<input type="hidden" size="30" name="<?php echo $this->getNs();?>user_id" value="<?php echo $user['user_id']?>"><?php echo $user['user_id']?>
			<?php else:?>
			<input type="text" size="30" name="<?php echo $this->getNs();?>user_id" value="<?php echo $user['user_id']?>">
			<?php endif;?>
			</TD>
		</TR>
		
		<?php if ($edit === true):?>
		<TR>
			<TH>API Key</TH>
			<TD><?php echo $user['api_key'];?></TD>
		</TR>
		<?php endif;?>
		
		<TR>
			<TH>Real Name</TH>
			<TD><input type="text" size="30" name="<?php echo $this->getNs();?>real_name" value="<?php $this->out( $this->getValue( 'real_name', $user ) );?>"></TD>
		</TR>
		<?php if ($user['id'] != 1):?>
		<TR>	
			<TH>Role</TH>
			<TD>
			<select name="<?php echo $this->getNs();?>role">
				<?php foreach ($roles as $role):?>
				<option <?php if($user['role'] === $role): echo "SELECTED"; endif;?> value="<?php echo $role;?>"><?php echo $role;?></option>
				<?php endforeach;?>
			</select>
			</TD>
		</TR>
		<?php endif;?>
		<TR>
			<TH>E-mail Address</TH>
			<TD><input type="text"size="30" name="<?php echo $this->getNs();?>email_address" value="<?php echo $user['email_address'];?>"></TD>
		</TR>
		
		<TR>
			<TD>
				<input type="hidden" name="<?php echo $this->getNs();?>id" value="<?php echo $user['id'];?>">
				<?php echo $this->createNonceFormField($action);?>
				<input type="hidden" name="<?php echo $this->getNs();?>action" value="<?php echo $action;?>">
				<input type="submit" value="Save" name="<?php echo $this->getNs();?>save_button">
			</TD>
		</TR>
		</form>
	
	</TABLE>

</fieldset>
<?php if ($edit === true):?>
<P>
<fieldset class="options">

	<legend>Change Password</legend>
	<div style="padding:10px">
	<a href="<?php echo $this->makeLink(array('do' => 'base.passwordResetForm'))?>">Change password for this user</a>
	</div>
</fieldset>
<?php endif;?>
</div>