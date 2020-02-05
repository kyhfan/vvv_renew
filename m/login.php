<?
    include_once "../include/autoload.php";

    $mnv_f = new mnv_function();
    $my_db         = $mnv_f->Connect_MySQL();
    $mobileYN      = $mnv_f->MobileCheck();

	if ($_REQUEST["refurl"] == "")
		$ref_url = "index.php";
	else
		$ref_url = $_REQUEST["refurl"];
	
	if ($_SESSION['ss_vvv_email'])
		echo "<script>location.href='index.php';</script>";

    include_once "./head.php";
?>
	<body>
		<div id="app">
			<div class="app-container sub">
				<!--햄버거 클릭 메뉴-->
<?
    include_once "./menu_layer.php";
?>							
				<!--햄버거 클릭 메뉴-->
				<!--검색 메뉴-->
<?
    include_once "./search_layer.php";
?>			
				<!--검색 메뉴-->
<?
    include_once "./header_layer.php";
?>			
				<div class="main-container">
					<div class="content login">
						<div class="inner">
							<div class="title">
								<h3>
									LOGIN
								</h3>
								<p>WELCOME TO VVV</p>
							</div>
							<div class="button-wrap">
								<button type="button" class="btn-login fb" id="fblogin">페이스북 로그인</button>
								<button type="button" class="btn-login kt">카카오톡 로그인</button>
							</div>
						</div>
					</div>
				</div>
<? include_once "footer_layer.php"; ?>
			</div>
<? 	include_once "cursor.php"; ?>
		</div>
		<script src="//developers.kakao.com/sdk/js/kakao.min.js"></script>		
		<script>
			// Kakao.init('ff013671b5f7b01d59770657a8787952');
			Kakao.init('8c2d788e7cfd26aa9e495f5bf8b2be2c');

			//	기본 기능 테스트 코드
			$doc = $(document);
			
			function getUserData() {
				/* FB.api('/me', function(response) {
					document.getElementById('response').innerHTML = 'Hello ' + response.name;
					console.log(response);
				}); */
				FB.api('/me', {fields: 'name,email,gender,birthday'}, function(response) {
					console.log(JSON.stringify(response));
					// $("#name").text("이름 : "+response.name);
					// $("#email").text("이메일 : "+response.email);
					// $("#gender").text("성별 : "+response.gender);
					// $("#birthday").text("생년월일 : "+response.birthday);
					// $("#id").text("아이디 : "+response.id);

					$.ajax({
						type   : "POST",
						async  : false,
						url    : "../main_exec.php",
						data:{
							"exec"				: "member_facebook_login",
							"login_way"			: "facebook",
							"mb_name"			: response.name,
							"mb_email"			: response.email,
							"gender"			: response.gender,
							"birthday"			: response.birthday,
							"id"				: response.id
						},
						success: function(response){
							console.log(response);
							if (response.match("Y") == "Y")
							{
								location.href	= "<?=$ref_url?>";
							}else if (response.match("J") == "J"){
								location.href 	= "join.php";
							}else{
								alert("다시 시도해 주세요!");
								location.reload();
							}
						}
					});

				});
			}

			window.fbAsyncInit = function() {
				//SDK loaded, initialize it
				FB.init({
					appId      : '1893328200738001',
					cookie     : true,  // enable cookies to allow the server to access
					xfbml      : true,  // parse social plugins on this page
					version    : 'v2.8' 
				});

				//check user session and refresh it
				FB.getLoginStatus(function(response) {
					// statusChangeCallback(response);
					if (response.status === 'connected') {
						//user is authorized
						//document.getElementById('loginBtn').style.display = 'none';
						//getUserData();
						FB.logout();
					} else {
						//user is not authorized
					}
				});
			};

			//load the JavaScript SDK
			(function(d, s, id){
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.com/ko_KR/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));

			//add event listener to login button
			document.getElementById('fblogin').addEventListener('click', function() {
			// $doc.on('click', '.btn-login.fb', function() {
				//do the login
				FB.login(function(response) {
					console.log(response);
					if (response.authResponse) {
						access_token = response.authResponse.accessToken; //get access token
						user_id = response.authResponse.userID; //get FB UID
						console.log('access_token = '+access_token);
						console.log('user_id = '+user_id);
						$("#access_token").text("접근 토큰 : "+access_token);
						$("#user_id").text("FB UID : "+user_id);
						//user just authorized your app
						//document.getElementById('loginBtn').style.display = 'none';
						getUserData();
					}
				}, {scope: 'email,public_profile,user_birthday',
					return_scopes: true});
			}, false);

			// 카카오 로그인
			$doc.on('click', '.btn-login.kt', function() {
				var refurl = "<?=$ref_url?>";
				// 로그인 창을 띄웁니다.
				Kakao.Auth.login({
				success: function(authObj) {
					// 로그인 성공시, API를 호출합니다.
					Kakao.API.request({
						url: '/v1/user/me',
						success: function(res) {
							// console.log(JSON.stringify(res));
							$.ajax({
								type   : "POST",
								async  : false,
								url    : "../main_exec.php",
								data:{
									"exec"				: "member_kakao_login",
									"login_way"			: "kakao",
									"mb_email"			: res.kaccount_email,
									"mb_email_verified"	: res.kaccount_email_verified,
									"mb_way_id"			: res.id,
									"mb_profile_img"	: res.properties.profile_image,
									"mb_name"			: res.properties.nickname,
									"mb_thumbnail_img"	: res.properties.thumbnail_image
								},
								success: function(response){
									// console.log(response);
									if (response.match("Y") == "Y")
									{
										location.href	= "<?=$ref_url?>";
									}else if (response.match("J") == "J"){
										location.href 	= "join.php";
									}else{
										alert("다시 시도해 주세요!");
										location.reload();
									}
								}
							});
						},
						fail: function(error) {
						alert(JSON.stringify(error));
						}
					});
				},
				fail: function(err) {
					alert(JSON.stringify(err));
				}
				});
			});

		</script>
	</body>

</html>