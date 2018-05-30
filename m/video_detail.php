<?
    include_once "../include/autoload.php";

    $mnv_f = new mnv_function();
    $my_db         = $mnv_f->Connect_MySQL();

	$video_idx	= $_REQUEST["idx"];

	// 영상 정보
	$detail_query	= "SELECT * FROM video_info2 WHERE 1 AND video_idx='".$video_idx."'";
	$detail_result 	= mysqli_query($my_db, $detail_query);
	$detail_data	= mysqli_fetch_array($detail_result);

	// Like 여부 체크
	$like_query		= "SELECT * FROM like_info WHERE mb_email='".$_SESSION['ss_vvv_email']."' AND v_idx='".$video_idx."' AND like_flag='Y'";
	$like_result	= mysqli_query($my_db, $like_query);

	$like_flag = "";
	if (mysqli_num_rows($like_result) > 0)
		$like_flag = "is-active";

	// 유튜브 영상 코드 자르기
	$yt_code_arr1   = explode("v=", $detail_data["video_link"]);
	$yt_code_arr2   = explode("&",$yt_code_arr1[1]);
	$yt_thumb       = "https://img.youtube.com/vi/".$yt_code_arr2[0]."/hqdefault.jpg";
	

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
					<div class="content detail">
						<div class="inner">
							<div class="wrapper">
								<div class="info-wrap">
									<div class="brand">
										<?=$detail_data["video_brand"]?>
									</div>
									<div class="title">
										<?=$detail_data["video_title"]?>
									</div>
									<div class="date">
										<?=$detail_data["video_date"]?>
										<!-- 2018 - 01 - 01 -->
									</div>
									<div class="icon-wrap">
										<span class="play">
											<i class="icon"></i>
											<span class="cnt" id="play_count"><?=number_format($detail_data["play_count"])?></span>
										</span>
										<span class="comment">
											<i class="icon"></i>
											<span class="cnt"><?=number_format($detail_data["comment_count"])?></span>
										</span>
										<span class="like">
											<i class="icon"></i>
											<span class="cnt" id="like_count"><?=number_format($detail_data["like_count"])?></span>
										</span>
										<span class="collect">
											<i class="icon"></i>
											<span class="cnt"><?=number_format($detail_data["collect_count"])?></span>
										</span>
									</div>
								</div>
								<div class="player" id="video_area">
									<img src="./images/loading.jpg" alt="로딩 이미지">
									<!-- <img src="./images/detail_video_sample.jpg" alt=""> -->
								</div>
								<div class="actions">
									<a href="javascript:like_video('<?=$video_idx?>')" class="action like <?=$like_flag?>"></a>
<?
	if ($_SESSION['ss_vvv_idx'] != "")
	{
?>									
									<a href="javascript:void(0)" class="action collect" data-layer="#collection-save"></a>

<?
	}else{
?>		
									<a href="javascript:alert('로그인 후 이용해 주세요.');location.href='./login.php?refurl=video_detail.php?idx=<?=$video_idx?>'" class="action collect"></a>
<?
	}
?>							
									<!-- <a href="javascript:request_translate('<?=$video_idx?>')" class="action translate"><span>번역</span></a> -->
									<a href="javascript:void(0)" class="action share"></a>
									<ul class="share-spread">
										<li class="fb"><a href="#"><img src="./images/detail_share_fb.png" alt="페이스북 공유"></a></li>
										<li class="kt"><a href="#"><img src="./images/detail_share_kt.png" alt="카카오톡 공유"></a></li>
										<li class="url"><a href="#"><img src="./images/detail_share_url.png" alt="링크 공유"></a></li>
									</ul>
								</div>
								<div class="block-comment">
									<h5>comment</h5>
									<div class="input-group">
										<div class="text-box">
											<input type="text" id="comment_text">
										</div>
										<div class="button">
											<button type="button" class="button-submit" onclick="ins_comment('<?=$video_idx?>')">
												확인
											</button>
										</div>
									</div>
									<div class="comment-list">
<?
	$comment_query		= "SELECT * FROM comment_info WHERE v_idx='".$video_idx."' AND showYN='Y' ORDER BY idx DESC";
	$comment_result		= mysqli_query($my_db, $comment_query);

	while ($comment_data = mysqli_fetch_array($comment_result))
	{
?>
										<div class="row">
											<div class="u-id">
												<a href="my_vvv.php?idx=<?=$comment_data["mb_idx"]?>"><?=$comment_data["mb_name"]?></a>
											</div>
											<div class="u-comment">
												<?=$comment_data["comment_text"]?>
											</div>
											<div class="date">
												<?=substr($comment_data["comment_regdate"],0,10)?>
											</div>
											<!-- <div class="actions">
												<button type="button" class="remove-comment"></button>
											</div> -->
										</div>
<?
	}
?>										
									</div>
								</div>
<?
	$related_query		= "SELECT * FROM video_info2 WHERE video_brand='".$detail_data["video_brand"]."' AND video_idx NOT IN ('".$detail_data["video_idx"]."') ORDER BY like_count DESC LIMIT 4";
	$related_result		= mysqli_query($my_db, $related_query);
	$related_count 		= mysqli_num_rows($related_result);
	if ($related_count > 0)
	{
?>
								<div class="block-related">
									<h5>관련 영상</h5>
									<div class="list-container">
										<div class="video-list">
<?
		while ($related_data = mysqli_fetch_array($related_result))
		{
			// 유튜브 영상 코드 자르기
			$rel_yt_code_arr1   = explode("v=", $related_data["video_link"]);
			$rel_yt_code_arr2   = explode("&",$rel_yt_code_arr1[1]);
			$rel_yt_thumb       = "https://img.youtube.com/vi/".$rel_yt_code_arr2[0]."/hqdefault.jpg";
?>											
											<div class="video">
												<a href="video_detail.php?idx=<?=$related_data["video_idx"]?>">
													<figure>
														<div class="thumbnail box-bg" style="background: url(<?=$rel_yt_thumb?>) center no-repeat; background-size: cover; padding-bottom: 52.92%;"></div>
														<figcaption>
															<span class="brand">[<?=$related_data["video_brand"]?>]</span>
															<span class="title"><?=$related_data["video_title"]?></span>
															<span class="icon-wrap">
																<span class="play">
																	<i class="icon"></i>
																	<span class="cnt"><?=number_format($related_data["play_count"])?></span>
																</span>
																<span class="comment">
																	<i class="icon"></i>
																	<span class="cnt"><?=number_format($related_data["comment_count"])?></span>
																</span>
																<span class="like">
																	<i class="icon"></i>
																	<span class="cnt"><?=number_format($related_data["like_count"])?></span>
																</span>
																<span class="collect">
																	<i class="icon"></i>
																	<span class="cnt"><?=number_format($related_data["collect_count"])?></span>
																</span>
															</span>
														</figcaption>
													</figure>
												</a>
											</div>
<?
		}
?>											
										</div>
									</div>
								</div>
<?
	}
?>								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div id="cursor" class="defualt"></div>
			<div class="layer collection-pick" id="collection-save">
				<button type="button" class="layer-close" data-layer="@close"></button>
				<div class="inner">
					<div class="title">
						<h5>Collection</h5>
					</div>
					<div class="content">
						<div class="block video-info">
							<figure>
								<div class="thumb-wrap">
									<div class="thumbnail" style="background: url(<?=$yt_thumb?>) 0 0 / 100% auto no-repeat; padding-bottom: 62.2%;"></div>
								</div>
								<figcaption>
									<div class="vid-brand"><?=$detail_data["video_brand"]?></div>
									<div class="vid-title"><?=$detail_data["video_title"]?></div>
									<div class="vid-date"><?=$detail_data["video_date"]?></div>
								</figcaption>
							</figure>

						</div>
						<div class="block collection-info" id="choice_collection">
							<h6>컬렉션 선택</h6>
							<div class="collection-list">
								<div class="scroll-box">
									<ul id="my_collection_list">
<?
	// 컬렉션 리스트 정보
	$collection_query	= "SELECT * FROM collection_info WHERE 1 AND collection_mb_idx='".$_SESSION["ss_vvv_idx"]."'";
	$collection_result 	= mysqli_query($my_db, $collection_query);

	while($collection_data = mysqli_fetch_array($collection_result))
	{
		$secret_flag	= "";
		if ($collection_data["collection_secret"] == "N")
			$secret_flag	= "is-secret";
?>										
										<li class="c-info <?=$secret_flag?>">
											<span onclick="collect_video('<?=$video_idx?>','<?=$collection_data["idx"]?>');"><?=$collection_data["collection_name"]?></span><i class="secret"></i>
										</li>
<?
	}
?>										
									</ul>
								</div>
							</div>
							<!-- <button type="button" class="btn-add" data-layer="#collection-add"> -->
							<button type="button" class="btn-add" onclick="open_add_collection()">
								<span class="icon"></span>
								<span>컬렉션 추가하기</span>
							</button>
						</div>
						<div class="block collection-info" id="add_collection" style="display:none;">
							<div class="collection-setting">
								<div class="input-wrap">
									<span>컬렉션 추가하기</span>
									<div class="input-group">
										<div class="guide">이름</div>
										<div class="input">
											<input type="text" id="collection_name">
										</div>
									</div>
									<div class="input-group">
										<div class="guide">설명</div>
										<div class="input">
											<input type="text" id="collection_desc">
										</div>
									</div>
								</div>
								<div class="setting">
									<span class="secret-guide">비밀 설정</span>
									<div class="toggle secret is-active">
										<input type="checkbox" type="checkbox" class="secret-toggle toggle-trigger" id="secret" name="secret">
										<div class="toggle-circle"></div>
									</div>
								</div>
							</div>	
							<div class="button-wrap">
								<button type="button" data-layer="@close">
									취소
								</button>
								<button type="button" onclick="create_collection()">
									만들기
								</button>
							</div>	
						</div>
					</div>
				</div>
			</div>
			<div class="layer collection-pick" id="collection-add">
				<button type="button" class="layer-close" data-layer="@close"></button>
				<div class="inner">
					<div class="title">
						<h5>Collection</h5>
					</div>
					<div class="content">
						<div class="block video-info">
							<figure>
								<div class="thumb-wrap">
									<div class="thumbnail" style="background: url(<?=$yt_thumb?>) 0 0 / 100% auto no-repeat; padding-bottom: 62.59%;"></div>
								</div>
								<figcaption>
									<div class="vid-brand"><?=$detail_data["video_brand"]?></div>
									<div class="vid-title"><?=$detail_data["video_title"]?></div>
									<div class="vid-date"><?=$detail_data["video_date"]?></div>
								</figcaption>
							</figure>

						</div>
						<div class="block collection-info">
							<div class="collection-setting">
								<div class="input-wrap">
									<span>컬렉션 추가하기</span>
									<div class="input-group">
										<div class="guide">이름</div>
										<div class="input">
											<input type="text" id="collection_name">
										</div>
									</div>
									<div class="input-group">
										<div class="guide">설명</div>
										<div class="input">
											<input type="text" id="collection_desc">
										</div>
									</div>
								</div>
								<div class="setting">
									<span class="secret-guide">비밀 설정</span>
									<div class="toggle secret is-active">
										<input type="checkbox" type="checkbox" class="secret-toggle toggle-trigger" id="secret" name="secret">
										<div class="toggle-circle"></div>
									</div>
								</div>
							</div>	
							<div class="button-wrap">
								<button type="button">
									취소
								</button>
								<button type="button" onclick="create_collection()">
									만들기
								</button>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			//	기본 기능 테스트 코드
			$doc = $(document),
				$win = $(window),
				$html = $('html');
			$doc.on('click', '.button-search', function() {
				$html.addClass('search-layer-opened');
			});
			$doc.on('click', '.search-layer-close', function() {
				$html.removeClass('search-layer-opened');
			});
			$doc.on('click', '.button-menu', function() {
				$html.toggleClass('menu-opened');
			});
			$win.on('scroll', function() {
				if(150 < $(this).scrollTop()) {
					$('.side-nav .search-wrap').css({
						opacity: 1
					});
				} else {
					$('.side-nav .search-wrap').css({
						opacity: 0
					});
				}
			});
			
			//			공유 버튼 토글
			$doc.on('click', '.actions .share', function() {
				$(this).toggleClass('is-active');
			});

			// 유튜브 api 재생 클릭시 이벤트 설정
			var tag = document.createElement('script');

			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName('script')[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

			var player;

			if ($(window).width() < 1040)
			{
				var yt_width = $(window).width() -48;
				var yt_height = Math.round((yt_width / 16) * 9);
			}else{
				var yt_width = '1200';
				var yt_height = '630';
			}

			function onYouTubeIframeAPIReady() {
				player = new YT.Player('video_area', {
					height: yt_height,
					width: yt_width,
					videoId: '<?=$yt_code_arr2[0]?>',
					events: {
						// 'onReady': onPlayerReady,
						'onStateChange': onPlayerStateChange
					}
				});
			}

			var play_flag = 0;
			function onPlayerStateChange(event) {
				if (event.data == 1)
				{
					if (play_flag == 0)
					{
						$.ajax({
							type   : "POST",
							async  : false,
							url    : "../main_exec.php",
							data:{
								"exec"				    : "view_video",
								"v_idx"		            : "<?=$video_idx?>"
							},
							success: function(response){
								console.log(response);
								if (response.match("Y") == "Y")
								{
									$("#play_count").html(Number($("#play_count").html()) + 1);
								}
							}
						});
					}
				}else if (event.data == 2){
					play_flag = 1;
				}
			}

			// 검색 APPLY 클릭
			$doc.on('click', '#search-layer-submit', function() {
				console.log("search");
				var search_keyword      = nullToBlank($("#search_keyword").val());
				var search_year         = nullToBlank($("#order-date").val());
				var search_nation       = nullToBlank($("#order-nation").val());
				var search_category1    = nullToBlank($("#order-industry").val());
				var search_genre        = nullToBlank($("#order-genre").val());
				var search_prize        = nullToBlank($("#order-awards").val());
				var search_sort         = nullToBlank($("#order-sortby").val());

				location.href = "video_list.php?keyword=" + search_keyword + "&year=" + search_year + "&nation=" + search_nation + "&category=" + search_category1 + "&genre=" + search_genre + "&prize=" + search_prize + "&sort=" + search_sort;
			});

			function nullToBlank(str)
			{
				if (str == null)
					str = "";
					
				return str;
			}

			$doc.on('click', '#search-layer-refresh', function() {
				$("#search_keyword").val("");
				$("#order-date").val("");
				$("#order-nation").val("");
				$("#order-industry").val("");
				$("#order-genre").val("");
				$("#order-awards").val("");
				$("#order-sortby").val("new");        
			});

			function like_video(v_idx)
			{
				$.ajax({
					type   : "POST",
					async  : false,
					url    : "../main_exec.php",
					data:{
						"exec"				    : "like_video",
						"v_idx"		            : v_idx
					},
					success: function(response){
						console.log(response);
						if (response.match("Y") == "Y")
						{
							// alert("Like 되었습니다!");
							$(".action.like").addClass("is-active");
							$("#like_count").html(Number($("#like_count").html()) + 1);
						}else if (response.match("L") == "L"){
							alert("로그인 후 이용해 주세요!");
							location.href = "login.php?refurl=video_detail.php?idx=<?=$video_idx?>";
						}else{
							// alert("Like 에서 제외 되었습니다!");
							$(".action.like").removeClass("is-active");
							$("#like_count").html($("#like_count").html() - 1);
						}
					}
				});
			}

			function collect_video(v_idx, c_idx)
			{
				$.ajax({
					type   : "POST",
					async  : false,
					url    : "../main_exec.php",
					data:{
						"exec"				    : "collect_video",
						"v_idx"		            : v_idx,
						"c_idx"		            : c_idx
					},
					success: function(response){
						console.log(response);
						if (response.match("Y") == "Y")
						{
							alert("선택하신 컬렉션에 영상이 담겼습니다");
							vvv.layer.close($("#collection-save"));
						}else if (response.match("D") == "D"){
							alert("선택하신 컬렉션에 이미 해당 영상이 담겨 있습니다");
						}else{
							alert("다시 시도해 주세요");
							location.reload();
						}
					}
				});
			}

			function create_collection()
			{
				var collection_name		= $("#collection_name").val();
				var collection_desc		= $("#collection_desc").val();
				var collection_secret	= $("input:checkbox[id='secret']").is(":checked");
				var secretFlag			= "";
				var appendTxt			= "";

				if (collection_secret === true)
				{
					secretFlag = "is-secret";
				}

				if (collection_name == "")
				{
					alert("컬렉션 이름을 입력해 주세요.");
					return false;
				}

				if (collection_desc == "")
				{
					alert("컬렉션 설명을 입력해 주세요.");
					return false;
				}

				$.ajax({
					type   : "POST",
					async  : false,
					url    : "../main_exec.php",
					data:{
						"exec"				    : "create_detail_collection",
						"collection_name"       : collection_name,
						"collection_desc"		: collection_desc,
						"collection_secret"		: collection_secret
					},
					success: function(response){
						console.log(response);
						res_arr 	= response.split("||");
						if (res_arr[0].match("Y") == "Y")
						{
							appendTxt += "<li class='c-info "+ secretFlag +"'>";
							appendTxt += "<span onclick=collect_video('<?=$video_idx?>','" + res_arr[1] + "');>" + collection_name + "</span><i class='secret'ß></i>";
							appendTxt += "</li>";

							$("#my_collection_list").append(appendTxt);
							$("#choice_collection").show();
							$("#add_collection").hide();
						}else if (res_arr[0].match("D") == "D"){
							alert("이미 생성된 컬렉션 이름입니다. 다른 이름으로 생성해 주세요.")
						}else{
							alert("다시 입력해 주세요.");
							// location.reload();
						}
					}
				});			

			}

			function open_add_collection()
			{
				$("#choice_collection").hide();
				$("#add_collection").show();
			}

			function request_translate(v_idx)
			{
				$.ajax({
					type   : "POST",
					async  : false,
					url    : "../main_exec.php",
					data:{
						"exec"				    : "request_translate",
						"v_idx"		            : v_idx
					},
					success: function(response){
						console.log(response);
						if (response.match("Y") == "Y")
						{
							alert("번역 요청이 접수 되었습니다. 번역이 완료되면 이메일로 알려드리겠습니다.");
						}else if (response.match("L") == "L"){
							alert("로그인 후 이용해 주세요!");
							location.href = "login.php?refurl=video_detail.php?idx=<?=$video_idx?>";
						}else{
							alert("다시 시도해 주세요.");
						}
					}
				});
			}

			function ins_comment(idx)
			{
				var comment_text 	= $("#comment_text").val();

				if (comment_text == "")
				{
					alert("댓글을 입력해 주세요.");
					return false;
				}

				$.ajax({
					type   : "POST",
					async  : false,
					url    : "../main_exec.php",
					data:{
						"exec"				    : "insert_comment",
						"idx"		            : idx,
						"comment_text"          : comment_text
					},
					success: function(response){
						console.log(response);
						if (response.match("Y") == "Y")
						{
							// alert("덧글이 입력되었습니다.");
							location.reload();
						}else if (response.match("L") == "L"){
							alert("로그인 후 이용해 주세요!");
							location.href = "login.php?refurl=video_detail.php?idx=<?=$video_idx?>";
						}else{
							alert("다시 입력해 주세요.");
							location.reload();
						}
					}
				});			
			}
		</script>
	</body>

</html>