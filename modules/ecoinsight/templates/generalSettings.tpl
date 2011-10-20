<div class="panel_headline"><?php echo $headline?></div>

<div class="subview_content">
	
	<form method="post" name="owa_options">
	
		<fieldset name="owa-options" class="options">
			<legend>Notification</legend>
					
			<div class="setting" id="enable-offline-advertising">
				<div class="title">Enable Notifications</div> 
				<div class="description">Indicates whether offline notifications should be enabled.</div>
				<div class="field">
					<select name="<?php echo $this->getNs();?>config[ecoinsight.enable_offline_ad_notification]">
						<option value="0" <?php if ($config['enable_offline_ad_notification'] == false):?>SELECTED<?php endif;?>>Off</option>
						<option value="1" <?php if ($config['enable_offline_ad_notification'] == true):?>SELECTED<?php endif;?>>On</option>		
					</select>
				</div>
			</div> 
			
			<div class="setting" id="offline-advertising">	
				<div class="title">Endpoint</div> 
				<div class="description">Specifies the URL which should receive offline notifications.</div>
				<div class="field">
					<input type="text" size="50" name="<?php echo $this->getNs();?>config[ecoinsight.offline_ad_notification_url]" value="<?php echo $config['offline_ad_notification_url'];?>">
				</div>
			</div>
		</fieldset>
		
		<fieldset name="owa-options" class="options">
			<legend>Advertising Server Database</legend>				
			
			<div class="setting" id="advertising-server-name">	
				<div class="title">Host Name</div> 
				<div class="description">The name of the advertising server.</div>
				<div class="field">
					<input type="text" size="50" name="<?php echo $this->getNs();?>config[ecoinsight.ad_db_host]" value="<?php echo $config['ad_db_host'];?>">
				</div>
			</div>
			
			<div class="setting" id="advertising-server-db">	
				<div class="title">Database Name</div> 
				<div class="description">The name of the advertising server database.</div>
				<div class="field">
					<input type="text" size="50" name="<?php echo $this->getNs();?>config[ecoinsight.ad_db_name]" value="<?php echo $config['ad_db_name'];?>">
				</div>
			</div>

			<div class="setting" id="advertising-server-user">	
				<div class="title">User Name</div> 
				<div class="description">The name of the advertising server database user.</div>
				<div class="field">
					<input type="text" size="50" name="<?php echo $this->getNs();?>config[ecoinsight.ad_db_user]" value="<?php echo $config['ad_db_user'];?>">
				</div>
			</div>

			<div class="setting" id="advertising-server-pwd">	
				<div class="title">Password</div> 
				<div class="description">The password for the advertising database.</div>
				<div class="field">
					<input type="password" size="16" name="<?php echo $this->getNs();?>config[ecoinsight.ad_db_password]" value="<?php echo $config['ad_db_password'];?>">
				</div>
			</div>

			
		</fieldset>

		<br/>
		
		<?php echo $this->createNonceFormField('ecoinsight.generalSettingsUpdate');?>
		
		<button type="submit" name="<?php echo $this->getNs();?>action" value="ecoinsight.generalSettingsUpdate">Update Configuration</button>
		<input type="hidden" name="<?php echo $this->getNs();?>module" value="ecoinsight">
		<button type="submit" name="<?php echo $this->getNs();?>action" value="ecoinsight.generalSettingsReset">Reset to Default Values</button>
		
	</form>
</div>