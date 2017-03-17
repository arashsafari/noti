<?php


include_once('Noti_LifeCycle.php');

class Noti_Plugin extends Noti_LifeCycle {

    
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
		$post_types =get_post_types();
		$no_noti_post_types;
		foreach($post_types as $key => $post_type){
			if($post_type != 'noti'){
				$no_noti_post_types[$key] = $post_type;
			}
		}
		
        return array(
            
            array(
				'key' => 'post_types',
				'title' => __('Show on selected post types','noti'),
				'type' => 'select',
				'multiple' => true,
				'values' => $no_noti_post_types
			),
			array(
				'key' => 'font_awesome',
				'title' => __('FontAwesome','noti'),
				'type' => 'select',
				'multiple' => false,
				'values' => array(
									'yes' => 'Load FontAwesome',
									'no' => 'Don\'t Load FontAwesome'
								)
			),
			array(
				'key' => 'namee',
				'title' => __('A text field','noti'),
				'type' => 'text',
				'values' => 'salam'
			)
        );
    }



    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Noti';
    }

    protected function getMainPluginFileName() {
        return 'noti.php';
    }

    
    protected function installDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        //            `id` INTEGER NOT NULL");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

	
	public function noti_admin_assets(){
			wp_register_style( 'notification', plugins_url('/css/noti.css', __FILE__), false, '1.0.0' );
			wp_enqueue_style( 'notification' );
			
			$fa  = $this->getOption('font_awesome', 'yes');
			if($fa  == 'yes'){
				wp_register_style( 'fontAwesome', plugins_url('/css/font-awesome.min.css', __FILE__), false, '1.0.0' );
				wp_enqueue_style( 'fontAwesome' );
			}
			wp_enqueue_script( 'notification', plugins_url('/js/noti.js', __FILE__));
	}
		
	public function noti_add_meta_box() {
			// print_r(get_alloptions());
			$screens  = $this->getOption('post_types', array( 'post', 'page' ));
			array_push($screens,'noti');
			foreach ( $screens as $screen ) {
				if($screen != 'noti'){
					add_meta_box(
						'noti_sectionid',
						__( 'My Post Section Title', 'noti_textdomain' ),
						array(&$this,'noti_meta_box_callback'),
						$screen
					);
				} else {
					add_meta_box(
						'noti_sectionid',
						__( 'My Post Section Title', 'noti_textdomain' ),
						array(&$this,'noti_noti_meta_box_callback'),
						$screen
					);
				}
			}
		}
		
	public function noti_save_meta_box_data( $post_id ) {

			/*
			 * We need to verify this came from our screen and with proper authorization,
			 * because the save_post action can be triggered at other times.
			 */

			// Check if our nonce is set.
			if ( ! isset( $_POST['noti_meta_box_nonce'] ) ) {
				return;
			}

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['noti_meta_box_nonce'], 'noti_meta_box' ) ) {
				return;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			global $post;
			
			 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post->ID;
			 
			} else {

				// Check the user's permissions.
				/* if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

					if ( ! current_user_can( 'edit_page', $post_id ) ) {
						return;
					}

				} else {

					if ( ! current_user_can( 'edit_post', $post_id ) ) {
						return;
					}
				} */

				/* OK, it's safe for us to save the data now. */
				
				// Make sure that it is set.
				/* if ( ! isset( $_POST['notifications'] ) ) {
					return;
				} */
				
				// Sanitize user input.
				$my_data = $_POST['notifications'];

				// Update the meta field in the database.
				update_post_meta( $post_id, '_noti_notifications', $my_data );
			}
		}
	public function add_this_script_footer(){
					
					global $post;
					$id = $post->ID;
					
					$isok = false;
					
					
					$post_notifications = get_post_meta( $post->ID, '_noti_notifications');
					
					// $isok_admin = false;
					$notifications_datas = array();
					// if(isset($post_notifications[0]) && !empty($post_notifications[0]) && is_single()){
						// $isok_admin = true;
					// }
					
					if(!empty($post_notifications[0])){
					
						foreach($post_notifications[0] as $index => $notification){
							
							$noti_kind = $notification['noti_kind'];

							if($noti_kind == 'past'){
								$noti_id = $notification['notinotis'];
								if(isset($noti_id) && $noti_id!=''){
									$noti_datas = get_post_meta( $noti_id, '_noti_notifications');
									if($notification['override']['ttl']!=''){
											$override_ttl = $notification['override']['ttl'];
											$noti_datas[0][0]['ttl'] = $override_ttl;
									}
									if($notification['override']['tts']!=''){
											$override_tts = $notification['override']['tts'];
											$noti_datas[0][0]['tts'] = $override_tts;
									}
									array_push($notifications_datas ,$noti_datas[0][0]);
									// break;
								}
							} elseif ($noti_kind == 'category'){
							$term_id = $notification['noticats'];
								if(isset($term_id) && $term_id!=''){
									$notis = get_posts(array(
										  'post_type' => 'noti',
										  'numberposts' => -1,
										  'tax_query' => array(
											array(
											  'taxonomy' => 'noticat',
											  'field' => 'id',
											  'terms' => $term_id
											)
										  )
									));
									$noti_index = rand(0,(count($notis) -1));
									$selected_noti = $notis[$noti_index];
									$noti_datas = get_post_meta( $selected_noti->ID, '_noti_notifications');
									if($notification['override']['ttl']!=''){
											$override_ttl = $notification['override']['ttl'];
											$noti_datas[0][0]['ttl'] = $override_ttl;
									}
									if($notification['override']['tts']!=''){
											$override_tts = $notification['override']['tts'];
											$noti_datas[0][0]['tts'] = $override_tts;
									}
									array_push($notifications_datas ,$noti_datas[0][0]);
									// break;
								}
							} elseif($noti_kind == 'tag'){
							$tag_term_id = $notification['notitags'];
								if(isset($tag_term_id) && $tag_term_id!=''){
									$notiz = get_posts(array(
										  'post_type' => 'noti',
										  'numberposts' => -1,
										  'tax_query' => array(
											array(
											  'taxonomy' => 'notitag',
											  'field' => 'id',
											  'terms' => $tag_term_id
											)
										  )
									));
									$noti_tag_index = rand(0,(count($notiz) -1));
									$selected_tag_noti = $notiz[$noti_tag_index];
									$noti_datas = get_post_meta( $selected_tag_noti->ID, '_noti_notifications');
									if($notification['override']['ttl']!=''){
											$override_ttl = $notification['override']['ttl'];
											$noti_datas[0][0]['ttl'] = $override_ttl;
									}
									if($notification['override']['tts']!=''){
											$override_tts = $notification['override']['tts'];
											$noti_datas[0][0]['tts'] = $override_tts;
									}
									array_push($notifications_datas ,$noti_datas[0][0]);
									// break;
								}
							} else if($noti_kind == 'new'){
								// $noti_datas = get_post_meta( $post->ID, '_noti_notifications');
								if($notification['override']['ttl']!=''){
											$override_ttl = $notification['override']['ttl'];
											$notification['ttl'] = $override_ttl;
									}
									if($notification['override']['tts']!=''){
											$override_tts = $notification['override']['tts'];
											$notification['tts'] = $override_tts;
									}
								array_push($notifications_datas ,$notification);
							}
						}
						
					} 
					$post_types  = $this->getOption('post_types', array( 'post', 'page' ));
					
					foreach($post_types as $post_type){
						if(get_post_type() == $post_type){
							$isok = true;
						}
					}
					if($isok){
						if(!is_admin){
							wp_enqueue_script('jquery');
						}
						wp_enqueue_style('notiStyle', plugins_url('/css/notistyles.css', __FILE__));
						$fa  = $this->getOption('font_awesome', 'yes');
						if($fa  == 'yes'){
							wp_register_style( 'fontAwesome', plugins_url('/css/font-awesome.min.css', __FILE__), false, '1.0.0' );
							wp_enqueue_style( 'fontAwesome' );
						}
						// wp_enqueue_script('notiScript', plugins_url('/js/notificationFx.js', __FILE__));
						
						$cornerexpand = '';
						$loadingcircle = '';
						$cornerexpandJS = '';
						$notificationFxs = '';
					
						$JSStart = '
						<script type="text/javascript" src="'.plugins_url('/js/notificationFx.js', __FILE__).'"></script>
						<script type="text/javascript">
							(function() {
						';
						$JSEnd = '
							})();
						</script>
						';
						//print_r($notifications_datas);
						foreach($notifications_datas as $index => $notifications_data){
							if($notifications_data['effect'] == 'cornerexpand'){
								$cornerexpand = '
								<div class="notification-shape shape-box" id="notification-shape" data-path-to="m 0,0 500,0 0,500 -500,0 z">
									<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 500 500" preserveAspectRatio="none">
										<path d="m 0,0 500,0 0,500 0,-500 z"/>
									</svg>
								</div>
								<script type="text/javascript" src="'.plugins_url('/js/snap.svg-min.js', __FILE__).'"></script>
								';
								
								$cornerexpandInsideJS = "path.animate( { 'path' : pathConfig.to }, 300, mina.easeinout );";
								
								$cornerexpandJS = "
									var svgshape = document.getElementById( 'notification-shape' ),
									s = Snap( svgshape.querySelector( 'svg' ) ),
									path = s.select( 'path' ),
									pathConfig = {
										from : path.attr( 'd' ),
										to : svgshape.getAttribute( 'data-path-to' )
									}";
								} else {
									$cornerexpand = '';
									$cornerexpandJS = '';
									$cornerexpandInsideJS = '';
								}
								
								if($notifications_data['effect'] == 'loadingcircle'){
								$loadingcircle = '
								<div class="notification-shape shape-progress" id="notification-shape">
									<svg width="70px" height="70px"><path d="m35,2.5c17.955803,0 32.5,14.544199 32.5,32.5c0,17.955803 -14.544197,32.5 -32.5,32.5c-17.955803,0 -32.5,-14.544197 -32.5,-32.5c0,-17.955801 14.544197,-32.5 32.5,-32.5z"/></svg>
								</div>
								<script type="text/javascript" src="'.plugins_url('/js/snap.svg-min.js', __FILE__).'"></script>
								';
								
								$loadingcircleInsideJS = "";
								
								$loadingcircleJS = "
										var svgshape = document.getElementById( 'notification-shape' );
									";
								} else {
									$loadingcircle = '';
									$loadingcircleJS = '';
									$loadingcircleInsideJS = '';
								}
								
								$nWrapper = $notifications_data['wrapper'];
								$nonOpen = $notifications_data['onOpen'];
								$nonClose = $notifications_data['onClose'];
								$nMessage = $notifications_data['message'];
								$nLayout = $notifications_data['layout'];
								$nEffect = $notifications_data['effect'];
								$nType = $notifications_data['type'];
								
								
								if($notifications_data['wrapper'] != '') {
									$dataWrapper = "document.querySelector('$nWrapper')";
								} else {
									$dataWrapper = 'document.body';
								}
								
								if($notifications_data['effect']=='cornerexpand' || $notifications_data['effect']=='loadingcircle'){
									$dataWrapper = 'svgshape';
								}
								
								
								if($notifications_data['ttl'] != '') { 
										$dataTTL = $notifications_data['ttl']; 
									} else {
										$dataTTL = '60000';
									}
								
								if($notifications_data['onOpen']!='') {
									$dataOnOpen = "onOpen : $nonOpen";
								} 
								if($notifications_data['onClose']!='') {
										$dataOnClose = "onClose : $nonClose";
									
								}
								
								if($notifications_data['effect'] == 'cornerexpand'){
									$dataOnClose =  "
										onClose : function() {
											setTimeout(function() {
												path.animate( { 'path' : pathConfig.from }, 300, mina.easeinout );
											}, 200 );
										}
										";
								}
								if($notifications_data['onClose']!='' && $notifications_data['onOpen']!=''){
									$comma = ',';
								} else {
									$comma = '';
								}
								
								
								
							if($notifications_data['tts'] != '') {
									$dataTTS = $notifications_data['tts'];
								} else {
									$dataTTS = '1000';
								}
								
								$notificationFxs .= "
								setTimeout( function() {	
									$cornerexpandInsideJS
								// create the notification
								var notification$index = new NotificationFx({
									wrapper : $dataWrapper,
									message : '$nMessage',
									layout : '$nLayout',
									effect : '$nEffect',
									ttl : $dataTTL,
									type : '$nType', // notice, warning, error or success
									$dataOnOpen
									$comma
									$dataOnClose
									
								});

								// show the notification
								notification$index.show();

							}, $dataTTS);
							";
						}
						echo $cornerexpand;
						echo $loadingcircle;
						echo $JSStart;
						echo $cornerexpandJS;
						echo $loadingcircleJS;
						echo $notificationFxs;
						echo $JSEnd;
					}
				}

	public function noti_noti_meta_box_callback( $post ) {
		wp_nonce_field( 'noti_meta_box', 'noti_meta_box_nonce' );
		$notifications = get_post_meta( $post->ID, '_noti_notifications');
		$notification = $notifications[0][0];
		// print_r($notification);
		?>
		<script type="text/javascript">
			jQuery(document).ready(function(){
				<?php 
					if(is_rtl()){
						?>
							var noti_samples = {
								'none' : '',
								'bouncyflip' : '<span class="icon fa fa-check"></span><p>یک متن نمونه <a href="#">لینک نمونه</a>.</p>',
								'flip' : '<p>متن نمونه <a href="#">لینک نمونه</a>.</p>',
								'exploader' : '<span class="icon icon-settings"></span><p>متن نمونه <a href="#">لینک نمونه</a>.</p>',
								'slidetop' : '<span class="icon fa fa-check"></span><p>متن نمونه  <a href="#">لینک نمونه</a> متن </p>',
								'genie' : '<p>متن نمونه <a href="#">لینک نمونه</a>.</p>',
								'jelly' : '<p>متن نمونه <a href="#">لینک نمونه</a>. </p>',
								'slide' : '<p>متن نمونه <a href="#">لینک نمونه</a>.</p>',
								'scale' : '<p>متن نمونه <a href="#">لینک نمونه </a>.</p>',
								'boxspinner' : '<p>متن نمونه  <a href="#">لینک نمونه</a></p>',
								'cornerexpand' : '<p><span class="icon fa fa-check"></span> متن نمونه <a href="#">لینک نمونه </a></p>',
								'loadingcircle' : '<p>متن نمونه</p>',
								'thumbslider' : '<div class="ns-thumb"><img src="img/user1.jpg"/></div><div class="ns-content"><p><a href="#">متن </a> نمونه .</p></div>'
							} 
						<?php
					} else {
					?>
					var noti_samples = {
						'none' : '',
						'bouncyflip' : '<span class="icon fa fa-check"></span><p>The event was added to your calendar. Check out all your events in your <a href="#">event overview</a>.</p>',
						'flip' : '<p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'exploader' : '<span class="icon icon-settings"></span><p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'slidetop' : '<span class="icon fa fa-check"></span><p>You have some interesting news in your inbox. Go <a href="#">check it out</a> now.</p>',
						'genie' : '<p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'jelly' : '<p>Hello there! I\'m a classic notification but I have some elastic jelliness thanks to <a href="http://bouncejs.com/">bounce.js</a>. </p>',
						'slide' : '<p>This notification has slight elasticity to it thanks to <a href="http://bouncejs.com/">bounce.js</a>.</p>',
						'scale' : '<p>This is just a simple notice. Everything is in order and this is a <a href="#">simple link</a>.</p>',
						'boxspinner' : '<p>I am using a beautiful spinner from <a href="http://tobiasahlin.com/spinkit/">SpinKit</a></p>',
						'cornerexpand' : '<p><span class="icon fa fa-check"></span> I\'m appaering in a morphed shape thanks to <a href="http://snapsvg.io/">Snap.svg</a></p>',
						'loadingcircle' : '<p>Whatever you did, it was successful!</p>',
						'thumbslider' : '<div class="ns-thumb"><img src="img/user1.jpg"/></div><div class="ns-content"><p><a href="#">Zoe Moulder</a> accepted your invitation.</p></div>'
					}
					<?php
					}
					?>
				jQuery('.noti_sampler').click(function (e) {
					e.preventDefault();
					var t = jQuery(this);
					var p = t.parent();
					var effect = jQuery('#noti_effect-0').val();
					var message = jQuery('[name="notifications[0][message]"]');
					// tinyMCE.activeEditor.setContent(noti_samples[effect])
					jQuery(message).html(noti_samples[effect]);
				});
			});
		</script>
		<div class="now noti-options-wrapper-{{row-count-placeholder}}">
							<div class="noti noti-new-wrapper">
									<div class="noti-one-half">
										<label for="noti_layout-0"><?php _e('Layout','noti');?></label>
										<select id="noti_layout-0" class="noti_layout" type="text" name="notifications[0][layout]">
											<option value="" <?php echo ($notification['layout'] == '') ? 'selected' : '';?>><?php _e('None','noti');?></option>
											<option value="attached" <?php echo ($notification['layout'] == 'attached') ? 'selected' : '';?>><?php _e('Attached','noti');?></option>
											<option value="bar" <?php echo ($notification['layout'] == 'bar') ? 'selected' : '';?>><?php _e('Bar','noti');?></option>
											<option value="growl" <?php echo ($notification['layout'] == 'growl') ? 'selected' : '';?>><?php _e('Growl','noti');?></option>
											<option value="other" <?php echo ($notification['layout'] == 'other') ? 'selected' : '';?>><?php _e('Other','noti');?></option>
										</select>
										<p><?php _e('Layout of the notification.','noti');?></p>
										
										<label for="noti_effect-0"><?php _e('Effect','noti');?></label>
										<select id="noti_effect-0" class="noti_effect" type="text" name="notifications[0][effect]">
											<option value="" data-layout="none" <?php echo ($notification['effect'] == '') ? 'selected' : '';?>><?php _e('Choose layou effect','noti');?></option>
											<option value="bouncyflip" data-layout="attached" <?php echo ($notification['effect'] == 'bouncyflip') ? 'selected' : '';?>><?php _e('Bouncy Flip','noti');?></option>
											<option value="flip" data-layout="attached" <?php echo ($notification['effect'] == 'flip') ? 'selected' : '';?>><?php _e('Flip','noti');?></option>
											<option value="exploader" data-layout="bar" <?php echo ($notification['effect'] == 'exploader') ? 'selected' : '';?>><?php _e('Exploader','noti');?></option>
											<option value="slidetop" data-layout="bar" <?php echo ($notification['effect'] == 'slidetop') ? 'selected' : '';?>><?php _e('Slide Top','noti');?></option>
											<option value="genie" data-layout="growl" <?php echo ($notification['effect'] == 'genie') ? 'selected' : '';?>><?php _e('Genie','noti');?></option>
											<option value="jelly" data-layout="growl" <?php echo ($notification['effect'] == 'jelly') ? 'selected' : '';?>><?php _e('Jelly','noti');?></option>
											<option value="slide" data-layout="growl" <?php echo ($notification['effect'] == 'slide') ? 'selected' : '';?>><?php _e('Slide','noti');?></option>
											<option value="scale" data-layout="growl" <?php echo ($notification['effect'] == 'scale') ? 'selected' : '';?>><?php _e('Scale','noti');?></option>
											<option value="boxspinner"data-layout="other" <?php echo ($notification['effect'] == 'boxspinner') ? 'selected' : '';?>><?php _e('Box Spinner','noti');?></option>
											<option value="cornerexpand" data-layout="other" <?php echo ($notification['effect'] == 'cornerexpand') ? 'selected' : '';?>><?php _e('Corner Expand','noti');?></option>
											<option value="loadingcircle" data-layout="other" <?php echo ($notification['effect'] == 'loadingcircle') ? 'selected' : '';?>><?php _e('Loading Circle','noti');?></option>
											<option value="thumbslider" data-layout="other" <?php echo ($notification['effect'] == 'thumbslider') ? 'selected' : '';?>><?php _e('Thumb Slider','noti');?></option>
										</select>
										<p><?php _e('Effect of the selected layout, it will be change according to layout.','noti');?></p>
									
									<label for="noti_type-0"><?php _e('Type','noti');?></label>
									<select id="noti_type-0" type="text" name="notifications[0][type]">
										<option value="notice" <?php echo ($notification['type'] == 'notice') ? 'selected' : '';?>><?php _e('Notice','noti');?></option>
										<option value="warning"<?php echo ($notification['type'] == 'warning') ? 'selected' : '';?>><?php _e('Warning','noti');?></option>
										<option value="error" <?php echo ($notification['type'] == 'error') ? 'selected' : '';?>><?php _e('Error','noti');?></option>
									</select>
									<p><?php _e('Type of the notification.','noti');?></p>
									</div>
									<div class="noti-one-half noti-last">
										<label for="noti_tts-0"><?php _e('Show after','noti');?></label>
										<input id="noti_tts-0" type="text" name="notifications[0][tts]" value="<?php echo $notification['tts'];?>"/>
										<p><?php _e('Time in milliseconds to wait for it to show.','noti');?></p>
										<label for="noti_ttl-0"><?php _e('Close after','noti');?></label>
										<input id="noti_ttl-0" type="text" name="notifications[0][ttl]" value="<?php echo $notification['ttl'];?>" />
										<p><?php _e('Time in milliseconds that notification will be closed! ','noti');?></p>
										
									</div>
									<div class="clearfix">
									<label for="noti_message-0"><?php _e('Message','noti');?></label>

										<?php
										$content = $notification['message'];
										$editor_id = 'notifications[0][message]';
										wp_editor( $content, $editor_id,array(' textarea_name'=>'notifications[0][message]' ));
										?>
										<br />
										<p><?php _e('Text to show. You can use HTML tags to style it.','noti');?></p>
										<a href="#" class="noti_sampler" data-index="0"><?php _e('Insert Related sample','noti');?></a>
									</div>
									<span class="noti-advance-trigger"><?php _e('Advance options','noti');?></span>
									
									<div class="noti-extra-panel noti-advance-panel">
										<label for="noti_wrapper-0"><?php _e('Wrapper selector','noti');?></label>
										<input id="noti_wrapper-0" type="text" name="notifications[0][wrapper]" value="<?php echo $notification['wrapper'];?>"/>
										<p><?php _e('Wrapper that will contains the notification, you can use css selectors.','noti');?></p>
										<label for="noti_onClose-0"><?php _e('onClose','noti');?></label>
										<textarea id="noti_onClose-0" name="notifications[0][onClose]"><?php echo $notification['onClose'];?></textarea>
										<p><?php _e('Callback for closing the notification, like function(){ alert("Thanks!");}','noti');?></p>
										<label for="noti_onOpen-0"><?php _e('onOpen','noti');?></label>
										<textarea id="noti_onOpen-0" name="notifications[0][onOpen]"><?php echo $notification['onOpen'];?></textarea>
										<p><?php _e('Callback for opening the notification, like function(){ alert("Wait!");}','noti');?></p>
									</div>
								</div>
						<div class="noti-gap"></div>
					</div>
		<?php
	}
	public function noti_meta_box_callback( $post ) {

			// Add a nonce field so we can check for it later.
			wp_nonce_field( 'noti_meta_box', 'noti_meta_box_nonce' );

			/*
			 * Use get_post_meta() to retrieve an existing value
			 * from the database and use the value for the form.
			 */
			$notifications = get_post_meta( $post->ID, '_noti_notifications');
			$noticats = get_terms( 'noticat', 'orderby=count&hide_empty=0' );
			$notitags = get_terms( 'notitag', 'orderby=count&hide_empty=0' );
			$notis = get_posts(array(
								  'post_type' => 'noti',
								  'numberposts' => -1
							));
			?>
			<script type="text/javascript">
				jQuery(document).ready(function(){
					var noti_samples = {
						'none' : '',
						'bouncyflip' : '<span class="icon icon-calendar"></span><p>The event was added to your calendar. Check out all your events in your <a href="#">event overview</a>.</p>',
						'flip' : '<p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'exploader' : '<span class="icon icon-settings"></span><p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'slidetop' : '<span class="icon icon-megaphone"></span><p>You have some interesting news in your inbox. Go <a href="#">check it out</a> now.</p>',
						'genie' : '<p>Your preferences have been saved successfully. See all your settings in your <a href="#">profile overview</a>.</p>',
						'jelly' : '<p>Hello there! I\'m a classic notification but I have some elastic jelliness thanks to <a href="http://bouncejs.com/">bounce.js</a>. </p>',
						'slide' : '<p>This notification has slight elasticity to it thanks to <a href="http://bouncejs.com/">bounce.js</a>.</p>',
						'scale' : '<p>This is just a simple notice. Everything is in order and this is a <a href="#">simple link</a>.</p>',
						'boxspinner' : '<div class="ns-thumb"><img src="img/user1.jpg"/></div><div class="ns-content"><p><a href="#">Zoe Moulder</a> accepted your invitation.</p></div>',
						'cornerexpand' : '<p><span class="icon icon-bulb"></span> I\'m appaering in a morphed shape thanks to <a href="http://snapsvg.io/">Snap.svg</a></p>',
						'loadingcircle' : '<p>Whatever you did, it was successful!</p>',
						'thumbslider' : '<div class="ns-thumb"><img src="img/user1.jpg"/></div><div class="ns-content"><p><a href="#">Zoe Moulder</a> accepted your invitation.</p></div>'
					}
				<?php if(isset($notifications[0]) && !empty($notifications[0])){ ?>
					var notifications = { <?php 
						foreach($notifications[0] as $mkey => $noti){
						echo $mkey.':{';
							foreach($noti as $key => $noti_option){
								if(is_array($noti_option)){
									echo $key . ": {";
									foreach($noti_option as $no_key => $no){
										echo $no_key . ":'" . $no . "'";
										if(end(array_keys($noti_option)) != $no_key){
											echo ',';
										}
									}
									echo ' } ';
								} else {
									echo $key . ":'" . $noti_option . "'";
								}
								if(end(array_keys($noti)) != $key){
									echo ',';
								}
							}
						echo '}';
						$last_key = end(array_keys($notifications[0]));
						if ($mkey != $last_key) {
									echo ',';
								}
						
					}?>};
					for(var i=0; i < <?php echo count($notifications[0]);?>; i++){
							jQuery('.noti .add').click();
							jQuery('.noti_sampler').click(function (e) {
								e.preventDefault();
								var t = jQuery(this);
								var p = t.parent().parent();
								var effect = p.find('.noti_effect').val();
								var message = p.find('.noti_message');
								jQuery(message).val(noti_samples[effect]);
							});
							// console.log(notifications[i]);
						// jQuery(notifications).each(function(index,val){
							// jQuery(notifications[index]).each(function(ind,val){
								if(notifications[i].noti_kind =='past'){
									jQuery('#noti-kind-' + i + ' .add-noti-past').addClass('noti-button-active');
									jQuery('.noti-options' + i).find('.noti-past-wrapper').addClass('active');
								} else if (notifications[i].noti_kind =='category'){
									jQuery('#noti-kind-' + i + ' .add-noti-cat').addClass('noti-button-active');
									jQuery('.noti-options' + i).find('.noti-category-wrapper').addClass('active');
								} else if(notifications[i].noti_kind =='tag'){
									jQuery('#noti-kind-' + i + ' .add-noti-tag').addClass('noti-button-active');
									jQuery('.noti-options' + i).find('.noti-tag-wrapper').addClass('active');
								} else if(notifications[i].noti_kind =='new'){
									jQuery('#noti-kind-' + i + ' .add-noti-new').addClass('noti-button-active');
									jQuery('.noti-options' + i).find('.noti-new-wrapper').addClass('active');
								}
								
							// });
						// });
						
						
					}
					
					jQuery.each(notifications, function(key,value) {
						jQuery.each(notifications[key], function(okey,ovalue) {							
							if(okey == 'override'){
								jQuery('[name="notifications['+key+']['+ okey +'][ttl]"]').val(ovalue.ttl);
								jQuery('[name="notifications['+key+']['+ okey +'][tts]"]').val(ovalue.tts);
							} else {
								jQuery('[name="notifications['+key+']['+ okey +']"]').val(ovalue);
							
								// tinymce.init();
							}
						});
					  
					});
					<?php } ?>
					jQuery('body').delegate('.noti-save-new', 'click', function (e) {
						e.preventDefault();
						var t = jQuery(this);
						var index = t.attr('data-index');
						var title = jQuery('#noti_save_name-' + index).val();
						var cat = jQuery('#noti_save_noticats-' + index).val();
						var tags = jQuery('#noti_save_notitags-' + index).val();
						var layout = jQuery('#noti_layout-' + index).val();
						var effect = jQuery('#noti_effect-' + index).val();
						var type = jQuery('#noti_type-' + index).val();
						var tts = jQuery('#noti_tts-' + index).val();
						var ttl = jQuery('#noti_ttl-' + index).val();
						var message = jQuery('#noti_message-' + index).val();
						var wrapper = jQuery('#noti_wrapper-' + index).val();
						var onClose = jQuery('#noti_onClose-' + index).val();
						var onOpen = jQuery('#noti_onOpen-' + index).val();
						
						var admin_url = '<?php echo admin_url('admin-ajax.php');?>';
						jQuery.ajax({
							type: 'POST',
							dataType: 'json',
							url: admin_url,
							data: { 
								'action': 'save_new_noti', //calls wp_ajax_nopriv_ajaxlogin
								'notification_title': title,
								'notification_cat': cat,
								'notification_tags': tags,
								'notification_layout': layout,
								'notification_effect': effect,
								'notification_type': type,
								'notification_tts': tts,
								'notification_ttl': ttl,
								'notification_message': message,
								'notification_wrapper': wrapper,
								'notification_onOpen': onOpen,
								'notification_onClose': onClose
							},
							beforeSend: function(data){
								jQuery('.noti-save-notification-box').html('<?php _e('Saving noti ...','noti');?>').fadeIn(500);
							},
							success: function(data){
								
								if(data.status == 'success'){
									jQuery('.noti-save-new').toggleClass('noti-hide');
									jQuery('#noti-save-notification-box-' + index).html(data.message)
										   .fadeIn(500)
										   .delay(4000)
										   .fadeOut(500,function(){
											   jQuery('#noti_save-' +index).click();
										   });
									
								} else {
									jQuery('#noti-save-notification-box-' + index).html(data.message)
										   .fadeIn(500);
								}
							}
						});
					});
					
				});
			</script>
			<div class="repeat noti">
			<table class="wrapper" width="100%">
				<thead>
					<tr>
						<td width="5%">
						</td>
						<td width="90%">
							<span class="add"><input class="button button-primary button-large" value="<?php _e('Add','noti');?>" type="button"></span>
						</td>
						<td width="5%"></td>
					</tr>
				</thead>
				<tbody class="container">
				<tr class="template row">
					
					<td width="5%"><span class="dashicons dashicons-sort move" title="<?php _e('Move','noti');?>"></span></td>
					<td width="90%" class="noti-options{{row-count-placeholder}}">
					<div class="not minimized noti-options-title-{{row-count-placeholder}}">
						<p><?php _e('Noti','noti');?> # <span class="noti-number"></span></p>
					</div>
					<div class="now noti-options-wrapper-{{row-count-placeholder}}">
							<div id="noti-kind-{{row-count-placeholder}}" class="noti-kind">
							<input class="button add-noti-past button-small" data-index="{{row-count-placeholder}}" value="<?php _e('Add From Past','noti');?>" type="button">
							<input class="button add-noti-cat button-small" data-index="{{row-count-placeholder}}" value="<?php _e('Add Random Noti From Categories','noti');?>" type="button">
							<input class="button add-noti-tag button-small" data-index="{{row-count-placeholder}}" value="<?php _e('Add Random Noti From Tags','noti');?>" type="button">
							<input class="button add-noti-new button-small" data-index="{{row-count-placeholder}}" value="<?php _e('Add Completely A New One','noti');?>" type="button">
							<input type="hidden" name="notifications[{{row-count-placeholder}}][noti_kind]" class="noti_kind_{{row-count-placeholder}}" />
						</div>
						<br />
							<div class="noti-panel noti-past-wrapper">
								<label for="noti_notis-{{row-count-placeholder}}"><?php _e('Show','noti');?></label>
								<select id="noti_notis-{{row-count-placeholder}}" class="noti_notis" type="text" name="notifications[{{row-count-placeholder}}][notinotis]">
									<option value="" <?php selected( $notifications['notinotis'],  ''); ?>><?php echo _e('None','noti');?></option>
									<?php foreach($notis as $notiSingle) { ?>
									<option value="<?php echo $notiSingle->ID;?>" <?php selected( $notifications['notinotis'],  $notiSingle->ID); ?>><?php echo $notiSingle->post_title;?></option>
									<?php } ?>
								</select>
								<?php _e('noti on this page.','noti');?>
								<p><?php _e('This will show one of the Notis randomly from this category every time on this page then will show it','noti');?></p>
							</div>
								
							<div class="noti-panel noti-category-wrapper">
								<label for="noti_noticats-{{row-count-placeholder}}"><?php _e('Show Random From','noti');?></label>
								<select id="noti_noticats-{{row-count-placeholder}}" class="noti_noticat" type="text" name="notifications[{{row-count-placeholder}}][noticats]">
									<option value="" <?php selected( $notifications['noticat'],  ''); ?>><?php echo _e('None','noti');?></option>
									<?php foreach($noticats as $noticat) { ?>
									<option value="<?php echo $noticat->term_id;?>" <?php selected( $notifications['noticat'],  $noticat->term_id); ?>><?php echo $noticat->name . ' ('. $noticat->count.' )';?></option>
									<?php } ?>
								</select>
								<?php _e('noti category.','noti');?>
								<p><?php _e('This will show one of the Notis randomly from this category every time on this page then will show it','noti');?></p>
							</div>
							
							<div class="noti-panel noti-tag-wrapper">
								<label for="noti_notitags-{{row-count-placeholder}}"><?php _e('Show Random From','noti');?></label>
								<select id="noti_notitags-{{row-count-placeholder}}" class="noti_notitag" type="text" name="notifications[{{row-count-placeholder}}][notitags]">
								<option value="" <?php selected( $notifications['notitag'],  ''); ?>><?php echo _e('None','noti');?></option>
									<?php 
									foreach($notitags as $notitag) {
									?>
									<option value="<?php echo $notitag->term_id;?>" <?php echo $selected; ?>><?php echo $notitag->name . ' ('. $notitag->count.' )';?></option>
									<?php } ?>
								</select>
								<?php _e('noti tag.','noti');?>
								<p><?php _e('This will show one of the Notis randomly from this tag every time on this page then will show it','noti');?></p>
								</div>
								
								<div class="noti-panel noti-new-wrapper">
									<div class="noti-one-half">
										<label for="noti_layout-{{row-count-placeholder}}"><?php _e('Layout','noti');?></label>
										<select id="noti_layout-{{row-count-placeholder}}" class="noti_layout" type="text" name="notifications[{{row-count-placeholder}}][layout]">
											<option value=""><?php _e('None','noti');?></option>
											<option value="attached"><?php _e('Attached','noti');?></option>
											<option value="bar"><?php _e('Bar','noti');?></option>
											<option value="growl"><?php _e('Growl','noti');?></option>
											<option value="other"><?php _e('Other','noti');?></option>
										</select>
										<p><?php _e('Layout of the notification.','noti');?></p>
										
										<label for="noti_effect-{{row-count-placeholder}}"><?php _e('Effect','noti');?></label>
										<select id="noti_effect-{{row-count-placeholder}}" class="noti_effect" type="text" name="notifications[{{row-count-placeholder}}][effect]">
											<option value=""data-layout="none"><?php _e('Choose layou effect','noti');?></option>
											<option value="bouncyflip"data-layout="attached"><?php _e('Bouncy Flip','noti');?></option>
											<option value="flip"data-layout="attached"><?php _e('Flip','noti');?></option>
											<option value="exploader" data-layout="bar"><?php _e('Exploader','noti');?></option>
											<option value="slidetop" data-layout="bar"><?php _e('Slide Top','noti');?></option>
											<option value="genie" data-layout="growl" ><?php _e('Genie','noti');?></option>
											<option value="jelly" data-layout="growl"><?php _e('Jelly','noti');?></option>
											<option value="slide" data-layout="growl"><?php _e('Slide','noti');?></option>
											<option value="scale" data-layout="growl"><?php _e('Scale','noti');?></option>
											<option value="boxspinner"data-layout="other"><?php _e('Box Spinner','noti');?></option>
											<option value="cornerexpand" data-layout="other"><?php _e('Corner Expand','noti');?></option>
											<option value="loadingcircle" data-layout="other"><?php _e('Loading Circle','noti');?></option>
											<option value="thumbslider" data-layout="other"><?php _e('Thumb Slider','noti');?></option>
										</select>
										<p><?php _e('Effect of the selected layout, it will be change according to layout.','noti');?></p>
									
									<label for="noti_type-{{row-count-placeholder}}"><?php _e('Type','noti');?></label>
									<select id="noti_type-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][type]">
										<option value="notice"><?php _e('Notice','noti');?></option>
										<option value="warning"><?php _e('Warning','noti');?></option>
										<option value="error"><?php _e('Error','noti');?></option>
									</select>
									<p><?php _e('Type of the notification.','noti');?></p>
									</div>
									<div class="noti-one-half noti-last">
										<label for="noti_tts-{{row-count-placeholder}}"><?php _e('Show after','noti');?></label>
										<input id="noti_tts-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][tts]" />
										<p><?php _e('Time in milliseconds to wait for it to show.','noti');?></p>
										<label for="noti_ttl-{{row-count-placeholder}}"><?php _e('Close after','noti');?></label>
										<input id="noti_ttl-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][ttl]" />
										<p><?php _e('Time in milliseconds that notification will be closed! ','noti');?></p>
										
									</div>
									<div class="clearfix"></div>
									<label for="noti_message-{{row-count-placeholder}}"><?php _e('Message','noti');?></label>
									<textarea class="noti_message_input" rows="5" id="noti_message_{{row-count-placeholder}}" name="notifications[{{row-count-placeholder}}][message]"></textarea>
									<p><?php _e('Text to show. You can use HTML tags to style it.','noti');?></p>
									<a href="#" class="noti_sampler" data-index="{{row-count-placeholder}}"><?php _e('Insert Related sample','noti');?></a>
									<?php
									$content = '';
									$editor_id = 'noti_message_{{row-count-placeholder}}';
									// wp_editor( $content, $editor_id, array(' textarea_name'=>'notifications[{{row-count-placeholder}}][message]' ));
									?>
									<br />
									<label for="noti_save-{{row-count-placeholder}}"><?php _e('Save this noti?','noti');?></label>
									<input id="noti_save-{{row-count-placeholder}}" type="checkbox" class="noti_save_check" name="notifications[{{row-count-placeholder}}][save][status]" />
									<input class="button button-primary noti-save-new button-small noti-hide" data-index="{{row-count-placeholder}}" value="<?php _e('Done?','noti');?>" type="button">
									<p><?php _e('By checking this, you can save it to database for later use!','noti');?></p>
									<div id="noti-save-panel-{{row-count-placeholder}}" class="noti-extra-panel noti-save-panel">
										<p id="noti-save-notification-box-{{row-count-placeholder}}" class="noti-save-notification-box"></p>
										<p><?php _e('Give it ','noti');?></p>
										<label for="noti_save_name-{{row-count-placeholder}}"><?php _e('A name','noti');?></label>
										<input id="noti_save_name-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][save][name]" />
										<p><?php _e('Like a post name, to be recognized later by only you!','noti');?></p>
										<label for="noti_save_noticats-{{row-count-placeholder}}"><?php _e('A Category','noti');?></label>
										<select id="noti_save_noticats-{{row-count-placeholder}}" class="noti_noticat" type="text" name="notifications[{{row-count-placeholder}}][save][noticats]">
											<option value="" <?php selected( $notifications['noticat'],  ''); ?>><?php echo _e('None','noti');?></option>
											<?php foreach($noticats as $noticat) { ?>
											<option value="<?php echo $noticat->term_id;?>" <?php selected( $notifications['noticat'],  $noticat->term_id); ?>><?php echo $noticat->name;?></option>
											<?php } ?>
										</select>
										<p><?php _e('Choose a category for it!','noti');?></p>
										<label for="noti_save_notitags-{{row-count-placeholder}}"><?php _e('Tags','noti');?></label>
											<input id="noti_save_notitags-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][save][notitags]" />
											<p><?php _e('Noti\'s tags, seperate with comma.','noti');?></p>
											<p><?php _e('NOTE: it will save by publishing the post!','noti');?></p>
									</div>
									<br />
									<br />
									<span class="noti-advance-trigger"><?php _e('Advance options','noti');?></span>
									
									<div class="noti-extra-panel noti-advance-panel">
										<label for="noti_wrapper-{{row-count-placeholder}}"><?php _e('Wrapper selector','noti');?></label>
										<input id="noti_wrapper-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][wrapper]" />
										<p><?php _e('Wrapper that will contains the notification, you can use css selectors.','noti');?></p>
										<label for="noti_onClose-{{row-count-placeholder}}"><?php _e('onClose','noti');?></label>
										<textarea id="noti_onClose-{{row-count-placeholder}}" name="notifications[{{row-count-placeholder}}][onClose]"></textarea>
										<p><?php _e('Callback for closing the notification, like function(){ alert("Thanks!");}','noti');?></p>
										<label for="noti_onOpen-{{row-count-placeholder}}"><?php _e('onOpen','noti');?></label>
										<textarea id="noti_onOpen-{{row-count-placeholder}}" name="notifications[{{row-count-placeholder}}][onOpen]"></textarea>
										<p><?php _e('Callback for opening the notification, like function(){ alert("Wait!");}','noti');?></p>
									</div>
								</div>
								<span class="noti-override-trigger"><?php _e('Override options','noti');?></span>
								<div class="noti-extra-panel noti-override-panel">
									
										<label for="noti_tts_override-{{row-count-placeholder}}"><?php _e('Show after','noti');?></label>
										<input id="noti_tts_override-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][override][tts]" />
										<p><?php _e('It will override the close after time of selected noti.','noti');?></p>
									
										<label for="noti_ttl_override-{{row-count-placeholder}}"><?php _e('Close after','noti');?></label>
										<input id="noti_ttl_override-{{row-count-placeholder}}" type="text" name="notifications[{{row-count-placeholder}}][override][ttl]" />
										<p><?php _e('It will override the show after time of selected noti.','noti');?></p>
																			
									</div>
						<div class="noti-gap"></div>
					</div>
					</td>
					<td width="5%"><span data-index="{{row-count-placeholder}}" class="dashicons dashicons-minus noti-minimize" title="<?php _e('Minimize','noti');?>"></span><span class="remove dashicons dashicons-no-alt" title="<?php _e('Remove','noti');?>"></span></td>
				</tr>
				</tbody>
			</table>
		</div>
	</div>
		<?php
			
		}
		
	public function add_noti_ost_type() {

		$labels = array(
			'name'                => _x( 'noti', 'Post Type General Name', 'noti' ),
			'singular_name'       => _x( 'noti', 'Post Type Singular Name', 'noti' ),
			'menu_name'           => __( 'Notification', 'noti' ),
			'parent_item_colon'   => __( 'Noti parent', 'noti' ),
			'all_items'           => __( 'All Notifications', 'noti' ),
			'view_item'           => __( 'View Notification', 'noti' ),
			'add_new_item'        => __( 'Add new notification', 'noti' ),
			'add_new'             => __( 'Add New Noti', 'noti' ),
			'edit_item'           => __( 'Edit notification', 'noti' ),
			'update_item'         => __( 'Update notification', 'noti' ),
			'search_items'        => __( 'Search notifications', 'noti' ),
			'not_found'           => __( 'Not found', 'noti' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'noti' ),
		);
		$args = array(
			'label'               => __( 'noti', 'noti' ),
			'description'         => __( 'Notifications', 'noti' ),
			'labels'              => $labels,
			'supports'            => array( 'title'),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 10,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);
		
		register_post_type( 'noti', $args );
		register_taxonomy("noticat", array("noti"), array("hierarchical" => true, "label" => __('Noti Category','noti'), "singular_label" => __('Noti Category','noti'), "rewrite" => true));
		register_taxonomy("notitag", array("noti"), array("hierarchical" => false, "label" => __('Noti Tag','noti'), "singular_label" => __('Noti Tag','noti'), "rewrite" => true));

	}
    public function noti_save_new_one() {
				
				$user_id = get_current_user_id();
				//
				
								
				$notification_title = $_POST['notification_title'];
				$notification_cat = $_POST['notification_cat'];
				$notification_tags = $_POST['notification_tags'];
				$notification_layout = $_POST['notification_layout'];
				$notification_effect = $_POST['notification_effect'];
				$notification_type = $_POST['notification_type'];
				$notification_tts = $_POST['notification_tts'];
				$notification_ttl = $_POST['notification_ttl'];
				$notification_message = $_POST['notification_message'];
				$notification_wrapper = $_POST['notification_wrapper'];
				$notification_onOpen = $_POST['notification_onOpen'];
				$notification_onClose = $_POST['notification_onClose'];

				if($notification_title == ''){
					header('Content-Type: application/json');
					echo json_encode(array(
						'status' 	 => 'failed',
						'message' 	 => __('<span class="error">Failed to save! No title!</span>','noti')
						)
					);
					die();
				}
				// update_user_meta( $user_id, 'themes', null );
				$notificatin_data[0] = array(
						'layout' 				=> $notification_layout,
						'effect'  => $notification_effect,
						'type'  => $notification_type,
						'tts' 			=> $notification_tts,
						'ttl' 			=> $notification_ttl,
						'message' 			=> $notification_message,
						'wrapper' 			=> $notification_wrapper,
						'onOpen' 			=> $notification_onOpen,
						'onClose' 			=> $notification_onClose
					);
				$user_id = get_current_user_id();

				// MAKE theme POST SET 'post_status' TO PENDING FOR REVIEW
				$noti_args = array(
												'post_title' 	=> $notification_title,
												'post_status'	=> 'publish',
												'post_type'		=> 'noti',
												'post_author' 	=>	$user_id,
												'tax_input' 	=> array(
																			'noticat' => $notification_cat,
																			'notitag' =>$notification_tags
																		)
											);
											
				// RETURN ID OF A POST
				$noti_id = wp_insert_post( $noti_args, $wp_error );
				
				// IF NO ERROR OCCURS THEN SET POST META DATA
				if(!is_wp_error( $wp_error )){
					
					update_post_meta($noti_id, '_noti_notifications', $notificatin_data);
					
					header('Content-Type: application/json');
					echo json_encode(array(
						'status' 	 => 'success',
						'message' 	 => $notification_title.', '.__('<span class="success">was successfully added!</span>','noti')
						)
					);
					
				} else {
					header('Content-Type: application/json');
					echo json_encode(array(
						'status' 	 => 'failed',
						'message' 	 => $notification_title.', '.__('<span class="error">Failed to save!</span>','noti')
						)
					);
				}
				
				
				die();
	}
    public function addActionsAndFilters() {

       
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));
		
		
		add_action( 'admin_enqueue_scripts', array(&$this, 'noti_admin_assets') );
	
		
		add_action( 'add_meta_boxes', array(&$this, 'noti_add_meta_box') );

		
		add_action( 'save_post', array(&$this, 'noti_save_meta_box_data') );
		

		add_action('wp_footer', array(&$this, 'add_this_script_footer'));


		add_action( 'init', array(&$this,'add_noti_ost_type'), 0 );
		
		// add_action('wp_ajax_save_new_noti', 'noti_save_new_one');
		add_action('wp_ajax_save_new_noti', array(&$this, 'noti_save_new_one'));

    }
	
}
