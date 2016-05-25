<?php
    require_once( 'includes.php' );
?><!DOCTYPE html>
<html>
	<head>
		<title>Facebook Album Challenge</title>

		<link rel="shortcut icon" type="image/jpg" href="libs/resources/img/favicon.jpg"/>
		<link rel="stylesheet" type="text/css" href="libs/resources/css/jquery.fancybox.css" />
		<link rel="stylesheet" type="text/css" href="libs/resources/css/bootstrap.css" />
		<link rel="stylesheet" type="text/css" href="libs/resources/css/style.css" />

		<script src="libs/resources/js/jquery-2.1.1.min.js"></script>
		<script src="libs/resources/js/spin.min.js"></script>
		<script src="libs/resources/js/jquery.fancybox.js" type="text/javascript" charset="utf-8"></script>
		<script src="libs/resources/js/bootstrap.min.js"></script>
	</head>
	<body>
		<?php
		use Facebook\GraphObject;
		use Facebook\GraphSessionInfo;
		use Facebook\Entities\AccessToken;
		use Facebook\HttpClients\FacebookHttpable;
		use Facebook\HttpClients\FacebookCurl;
		use Facebook\HttpClients\FacebookCurlHttpClient;
		use Facebook\FacebookSession;
		use Facebook\FacebookRedirectLoginHelper;
		use Facebook\FacebookRequest;
		use Facebook\FacebookResponse;
		use Facebook\FacebookSDKException;
		use Facebook\FacebookRequestException;
		use Facebook\FacebookAuthorizationException;

		FacebookSession::setDefaultApplication( $fb_app_id, $fb_secret_id );

		// login helper with redirect_uri
		$helper = new FacebookRedirectLoginHelper( $fb_login_url );
		
		// see if a existing session exists
		if ( isset( $_SESSION ) && isset( $_SESSION['fb_token'] ) ) {
			// create new session from saved access_token
			$session = new FacebookSession( $_SESSION['fb_token'] );

			try {
				if ( !$session->validate() ) {
				  $session = null;
				}
			} catch ( Exception $e ) {
				// catch any exceptions
				$session = null;
			}
		}  
		 
		if ( !isset( $session ) || $session === null ) {
			try {
				$session = $helper->getSessionFromRedirect();
			} catch( FacebookRequestException $ex ) {
				print_r( $ex );
			} catch( Exception $ex ) {
				print_r( $ex );
			}
		}

		$google_session_token = "";

		// see if we have a session
		if ( isset( $session ) ) {

			// require_once( 'libs/resize_image.php' );

			$_SESSION['fb_login_session'] = $session;
			$_SESSION['fb_token'] = $session->getToken();


			// create a session using saved token or the new one we generated at login
			$session = new FacebookSession( $session->getToken() );
			
			$request_user_details = new FacebookRequest( $session, 'GET', '/me?fields=id,name' );
			
			$response_user_details = $request_user_details->execute();
			$user_details = $response_user_details->getGraphObject()->asArray();
			
			$user_id = $user_details['id'];
			$user_name = $user_details['name'];
			
			
			if ( isset( $_SESSION['google_session_token'] ) ) {
				$google_session_token = $_SESSION['google_session_token'];
			}
			?>
			<nav class="navbar navbar-default " role="navigation">
				<div class="container">
					<div class="row">
						<div class="col-sm-12">
							<div class="col-xs-12 col-sm-2 text-center pull-right">
								<button class="dropdown-toggle btn-default btn" id="menu1" type="button" data-toggle="dropdown">
									<img src="<?php echo 'https://graph.facebook.com/'.$user_id.'/picture';?>" id="user_photo" class="img-circle" />
									<span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
								  <li role="presentation"><a href="http://facebook.com/" target="_blanck">Timeline</a></li>
								  <li role="presentation"><a href="http://facebook.com/<?=$user_id; ?>" target="_blanck">Profile</a></li>
								  <li role="presentation"><a href="logout.php" >Logout</a></li>
								</ul>	
							</div>
							
							<div class="col-xs-12 col-sm-3 text-center">
								<a class="" href="http://facebook.com/" id="username">
									<span style="margin-left: 5px;"><?php echo $user_name;?></span>
								</a>
							</div>
						</div>
					</div>
				</div>
			</nav>
			<ul class="nav navbar-nav pull-right">
								<li>
									<a href="#" id="download-all-albums" class="center">
										<span class="btn btn-primary col-md-12">
											Download All
										</span>
									</a>
								</li>
								<li>
									<a href="#" id="download-selected-albums" class="center">
										<span class="btn btn-warning col-md-12">
											Download Selected
										</span>
									</a>
								</li>
								
							</ul>
			<div class="container" id="main-div">
				<div class="row">
					<span id="loader" class="navbar-fixed-top"></span>
					<div class="modal fade" id="download-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">
											<span aria-hidden="true">&times;</span><span class="sr-only">Close</span>
										</button>
										<h4 class="modal-title" id="myModalLabel">Albums Report</h4>
									</div>
									<div class="modal-body" id="display-response">
										<!-- Response is displayed over here -->
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									</div>
								</div>
							</div>
						</div>
				</div>
				
				<div class="row">
					<?php
						// graph api request for user data
						$request_albums = new FacebookRequest( $session, 'GET', '/me/albums?fields=id,cover_photo,from,name' );
						$response_albums = $request_albums->execute();
						// get response
						$albums = $response_albums->getGraphObject()->asArray();
						if ( !empty( $albums ) ) {
							foreach ( $albums['data'] as $album ) {
								$album = (array) $album;
								$request_album_photos = new FacebookRequest( $session,'GET', '/'.$album['id'].'/photos?fields=source' );
								$response_album_photos = $request_album_photos->execute();			
								$album_photos = $response_album_photos->getGraphObject()->asArray();
								if ( !empty( $album_photos ) ) {
									foreach ( $album_photos['data'] as $album_photo ) {
										$album_photo = (array) $album_photo;
										$cover_photo = $album['cover_photo']->id;
										if ( $cover_photo == $album_photo['id'] ) {
											$album_cover_photo = $album_photo['source'];
											$album_resized_cover_photo = $album_cover_photo;
										?>
										<div class="col-md-3">
											<div class="thumbnail no-border center">
												
												<a href="<?php echo $album_photo['source'];?>" class="fancybox" rel="<?php echo $album['id'];?>">
												  <img src="<?php echo $album_resized_cover_photo;?>" class="thm image-responsive img-rounded" alt="<?php echo $album['name'];?>" />
												</a>
												<div class="caption">
												<h4><?php echo $album['name'].' ('.count($album_photos['data']).')';?></h4>
													<button rel="<?php echo $album['id'].','.$album['name'];?>" class="single-download btn btn-primary pull-left" title="Download Album">
														<span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span>
													</button>
													<input type="checkbox" class="select-album" title="select album" value="<?php echo $album['id'].','.$album['name'];?>" />
												</div>
											</div>
										</div>
										<?php
										} else {
										?>
											<a href="<?php echo $album_photo['source'];?>" class="fancybox" rel="<?php echo $album['id'];?>" style="display:none;"></a>
										<?php
										}
									}
								}
							}
						}
						?>
				</div>
			</div>
		<?php
		} else {
			$perm = array( "scope" => "email, user_photos" );
		?>
			<div id="login-div" class="row">
				<a id="login-link" class="btn btn-primary btn-lg" href="<?php echo $helper->getLoginUrl( $perm );?>">Facebook</a>
			</div>
		<?php
		}
		?>
				<script type="text/javascript" charset="utf-8">
			$( document ).ready(function() {
				var opts = {
				  lines: 15 // The number of lines to draw
				, length: 0 // The length of each line
				, width: 6 // The line thickness
				, radius: 56 // The radius of the inner circle
				, scale: 1.25 // Scales overall size of the spinner
				, corners: 0.9 // Corner roundness (0..1)
				, color: '#000' // #rgb or #rrggbb or array of colors
				, opacity: 0 // Opacity of the lines
				, rotate: 35 // The rotation offset
				, direction: 1 // 1: clockwise, -1: counterclockwise
				, speed: 0.8 // Rounds per second
				, trail: 60 // Afterglow percentage
				, fps: 20 // Frames per second when using setTimeout() as a fallback for CSS
				, zIndex: 2e9 // The z-index (defaults to 2000000000)
				, className: 'spinner' // The CSS class to assign to the spinner
				, top: '50%' // Top position relative to parent
				, left: '50%' // Left position relative to parent
				, shadow: false // Whether to render a shadow
				, hwaccel: false // Whether to use hardware acceleration
				, position: 'absolute' // Element positioning
				};
				var target = document.getElementById('loader');

				$('.fancybox').fancybox({
					autoPlay: true
				});

				function append_download_link(url) {
					var spinner = new Spinner(opts).spin(target);

					$.ajax({
						url:url,
						success:function(result){
							$("#display-response").html(result);
							spinner.stop();
							$("#download-modal").modal({
								show: true
							});
						}
					});
				}

				function get_all_selected_albums() {
					var selected_albums;
					var i = 0;
					$(".select-album").each(function () {
						if ($(this).is(":checked")) {
							if (!selected_albums) {
								selected_albums = $(this).val();
							} else {
								selected_albums = selected_albums + "/" + $(this).val();
							}
						}
					});

					return selected_albums;
				}

				$(".single-download").on("click", function() {
					var rel = $(this).attr("rel");
					var album = rel.split(",");

					append_download_link("download_album.php?zip=1&single_album="+album[0]+","+album[1]);
				});

				$("#download-selected-albums").on("click", function() {
					var selected_albums = get_all_selected_albums();
					append_download_link("download_album.php?zip=1&selected_albums="+selected_albums);
				});

				$("#download-all-albums").on("click", function() {
					append_download_link("download_album.php?zip=1&all_albums=all_albums");
				});
			});
		</script>
<script>
		$(document).ready(function(){
			$(".thumbnail").each(function() {  
			 var imgsrc = ($(this).find('img').attr('src'));
			 
			 $(this).css('background-image', 'url("' + imgsrc + '")');

			 });    
		});
		</script>
	</body>
</html>
