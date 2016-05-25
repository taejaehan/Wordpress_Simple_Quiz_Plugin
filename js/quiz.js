var mUrl = '/wp-admin/admin-ajax.php';
var mQuizNum = 5;
var mUserScore = 4;
var mCategoryId = 1;
var mPluginImgUrl = '';
jQuery(document).ready(function($){
	console.log('ready');
	// window.scrollTo(0, 0);
	// $(window).scrollTop(0);
	$(this).scrollTop(0);

	var mQuizFullWrap = $('#youthqna_quiz_section');
	var mCurrentQuizStep = 1;
	var mQuizCount = $('.youth-quiz').length - 1; //quiz중 #show_correct_answer 뺀 값(퀴즈 갯수)
	function mNextStep(){
		console.log('mQuizCount : ' + mQuizCount);
		console.log('mCurrentQuizStep : ' + mCurrentQuizStep);
		//마지막 문제일 경우 mCurrentQuizStep을 7로 변경
		if(mCurrentQuizStep - mQuizCount === 1){
			mCurrentQuizStep = 7;
		}else{
			mCurrentQuizStep++;
		}
		console.log('mCurrentQuizStep : ' + mCurrentQuizStep);
		if(mCurrentQuizStep < 11){
			//스탭 9 (이벤트완료시) background 검정으로 설정하기 위함
			if(mCurrentQuizStep !== 9){
				$('#youthqna_full_wrap').attr('class','');
			}else{
				$('#youthqna_full_wrap').attr('class','step-background');
			}
			mQuizFullWrap.attr('class','youthqna-step-'+mCurrentQuizStep);
		}
	};

	/*테스트 코드*/
	// var res = {"correctNum":1,"results":[{"correct_id":2,"correct_index":2,"correct_answer":"귤","user_id":1,"explanation":"내가 좋아하는 과일은 귤이다","q_id":"6"},{"correct_id":8,"correct_index":3,"correct_answer":"무한도전","user_id":7,"explanation":"무한도전 프로그램이다","q_id":"7"},{"correct_id":11,"correct_index":1,"correct_answer":"파랑","user_id":13,"explanation":"파랑색이다","q_id":"8"},{"correct_id":20,"correct_index":5,"correct_answer":"과천","user_id":20,"explanation":"디메이저는 과천에 위치해 있다","q_id":"9"},{"correct_id":21,"correct_index":1,"correct_answer":"휴식","user_id":24,"explanation":"쉬고싶다","q_id":"10"}]}
	// mPluginImgUrl = 'http://localhost.sblognew/wp-content/plugins/youth_qna/imgs/';

	// var ajaxResults = res.results;
	// for(var i=0; i < ajaxResults.length; i++){
	// 	var ajaxResult = ajaxResults[i];
	// 	var answerHtml = '';
	// 	var correctAnswerImgUrl = '';
		
	// 	switch(ajaxResult.correct_index){
	// 		case 1:
	// 			correctAnswerImgUrl = mPluginImgUrl+'answer_a_act.png';
	// 			break;
	// 		case 2:
	// 			correctAnswerImgUrl = mPluginImgUrl+'answer_b_act.png';
	// 			break;
	// 		case 3:
	// 			correctAnswerImgUrl = mPluginImgUrl+'answer_c_act.png';
	// 			break;
	// 		case 4:
	// 			correctAnswerImgUrl = mPluginImgUrl+'answer_d_act.png';
	// 			break;
	// 		case 5:
	// 			correctAnswerImgUrl = mPluginImgUrl+'answer_e_act.png';
	// 			break;
	// 	};
	// 	answerHtml = "<div style='padding:3% 0'>"
	// 	            +"<img style='margin-right:5px' src='"+correctAnswerImgUrl+"' /> "+ ajaxResult.correct_answer
	// 	            +"<p style='margin:3% 0'>"
	// 	            +"<img style='margin-right:5px' src='"+mPluginImgUrl+"correct_explanation.png' /> "+ ajaxResult.explanation
	// 	            +"</p>"
	// 	            +"</div>"
	// 	$('#youth_correct_quiz_answer_'+ajaxResult.q_id).html(answerHtml);
	// };
	/*테스트 코드*/
	
	//퀴즈 스타트
	$('#youth_quiz_start').on('click', function(e) {
		e.preventDefault();
		mNextStep();
	});

	//답변 선택
	$('.youthqna-op-btn').on('click', function(e) {
		e.preventDefault();
		$(this).parent().siblings('li').removeClass('selected-op');
		$(this).parent().addClass('selected-op');
		var currentQuizWrap = $(this).parents('.youth-quiz');
		currentQuizWrap.attr('userSelOp', $(this).val());
		currentQuizWrap.find('.youthqna-next-btn').attr('disabled',false);
	});

	//다음 문제 및 채점결과 보기
	$('.youthqna-next-btn').on('click', function(e) {
		console.log('NEXT!!!!!');
		e.preventDefault();
		// var qindex = parseInt($(this).attr('currentQuizIndex'));
		var currentQuizWrap = $(this).parents('.youth-quiz');
		var currentQuizIndex = parseInt(currentQuizWrap.attr('quizIndex'));
		var currentQuizId = currentQuizWrap.attr('quizId');
		var currentUserSelOp = currentQuizWrap.attr('userSelOp');
		if(currentUserSelOp === ''){
			alert('문제를 풀어주세요');
			return;
		};

		currentQuizIndex++;
		var newxQuizWrap = 'youth_quiz_'+currentQuizIndex;
		currentQuizWrap.css('display','none');
		console.log('newxQuizWrap : '  + newxQuizWrap);
		mNextStep();
		if($('#'+newxQuizWrap).length === 0){
			//마지막 문제에서 next하면 유저 선택 userSelOp을 가져와서 array에 저장
			var quizAnswer = [];
			mCategoryId = $('#youth_quiz_category').val();
			for(var i=1; i <= mQuizNum; i++){
				var answerId = $('#youth_quiz_'+i).attr('userSelOp');
				var quizId = $('#youth_quiz_'+i).attr('quizId');
				quizAnswer.push({
					category_id : mCategoryId,
					quiz_id : quizId,
					answer_id : answerId
				})
			};
			console.log(JSON.stringify(quizAnswer));
			var data = {
		      'action': 'youthqna_get_results',
		      'quiz_answer': quizAnswer
		    };
		    $.ajax({
		        url: mUrl,
		        data: data,
		        method: "POST",
		        success: function(res) {
		            console.log('success');
		            console.log('res : ' + JSON.stringify(res));
		            mUserScore = res.correctNum;
		            // $('#youthqna_result').html('총 '+mUserScore+'개를 맞추셨습니다.');
		            mPluginImgUrl = $('#youthqna_result_wrap').attr('pluginImgUrl');
		            var scoreUrl = mPluginImgUrl+'q_result_'+mQuizCount+'_'+mUserScore+'.png';
		            var textUrl;
		            if(mUserScore < mQuizCount){
			            textUrl = mPluginImgUrl+'q_results_bad.png';
		            }else{
			            textUrl = mPluginImgUrl+'q_results_good.png';
		            };
		            console.log('mPluginImgUrl : '  + mPluginImgUrl);
		            console.log('scoreUrl : '  + scoreUrl);
		            console.log('textUrl : '  + textUrl);
		            $('#youthqna_result_score').attr('src',scoreUrl);
		            $('#youthqna_result_text').attr('src',textUrl);
		            $('#join_event_btn').attr('disabled',false);
					var ajaxResults = res.results;
					for(var i=0; i < ajaxResults.length; i++){
						var ajaxResult = ajaxResults[i];
						var answerHtml = '';
						var correctAnswerImgUrl = '';
						
						switch(ajaxResult.correct_index){
							case 1:
								correctAnswerImgUrl = mPluginImgUrl+'answer_1_act.png';
								break;
							case 2:
								correctAnswerImgUrl = mPluginImgUrl+'answer_2_act.png';
								break;
							case 3:
								correctAnswerImgUrl = mPluginImgUrl+'answer_3_act.png';
								break;
							case 4:
								correctAnswerImgUrl = mPluginImgUrl+'answer_4_act.png';
								break;
							case 5:
								correctAnswerImgUrl = mPluginImgUrl+'answer_5_act.png';
								break;
						};
						answerHtml = "<div class='correct-answer-div' style='padding:6% 0'>"
						            +"<img style='margin-right:5px' src='"+correctAnswerImgUrl+"' /> "+ ajaxResult.correct_answer
						            +"<p style='margin:3% 0'>"
						            +"<img style='margin-right:5px' src='"+mPluginImgUrl+"correct_explanation.png' /> "+ ajaxResult.explanation
						            +"</p>"
						            +"</div>"
						$('#youth_correct_quiz_answer_'+ajaxResult.q_id).html(answerHtml);

					};
					
		        }
		    });
			
		};
	}); 

	$('.event-checkbox-input').change(function(){
		$('.event-checkbox-input').attr('checked',false);
		$(this).attr('checked',true);
	});
	//이벤트 참여하기
	$('#join_event_btn').on('click', function(e) {
		e.preventDefault();
		console.log('event');
		mNextStep();
	});

	//이벤트 참여완료
	$('#event-submit-btn').on('click', function(e) {
		e.preventDefault();
		console.log('event sumbit');
		var userName = $('#user_name').val();
		var userPhone = $('#user_phone').val();
		if ( userName === '') {
			event.preventDefault ? event.preventDefault() : (event.returnValue = false);
			alert('이름을 입력해주세요');
			return;
		};
		if ( userPhone === '') {
			event.preventDefault ? event.preventDefault() : (event.returnValue = false);
			alert('휴대폰 번호를 입력해주세요');
			return;
		}
		if ( userName.length > 30) {
		
			event.preventDefault ? event.preventDefault() : (event.returnValue = false);
			alert('이름은 30자까지 입력가능합니다 ');
			return;
		}
		if ( userPhone.length < 10 || userPhone.length > 12 
			|| userPhone.charAt(0) != '0' || userPhone.charAt(1) != '1') {
			alert('올바른 휴대폰 번호를 입력해주세요');
			return;
		}
		var chk1 = $('#event_faq_agree_1').prop('checked');
		if ( !chk1 ) {
			event.preventDefault ? event.preventDefault() : (event.returnValue = false);
			alert('개인정보 수집이용에 동의하셔야 합니다');
			return;
		};
		$('#event_processing').css('display','block');
		var data = {
	      'action': 'youthqna_join_event',
	      'user_name': userName,
	      'user_phone': userPhone,
	      'user_score': mUserScore,
	      'category_id' : mCategoryId
	    };
		$(this).unbind();
		$.ajax({
	        url: mUrl,
	        data: data,
	        method: "POST",
	        success: function(res) {
	        	$('#event_processing').css('display','none');
	            console.log('success');
	            console.log('res : ' + JSON.stringify(res));
	            mNextStep();
				// $('#youthqna_result_wrap').css('display','block');

	            // if(res){
	            //   console.log('성공적으로 업로드 했습니다');
	            //   // location.reload();
	            //   window.location = "/wp-admin/admin.php?page=youth_qna%2Fadmin.php&category="+$(this).val();
	            // }else{
	            //   console.log('업로드 실패');
	            // }
	        }
	    });

	});

	//참여 결과 공유하기
	$('#quiz_share_btn').on('click', function(e) {
		e.preventDefault();
		console.log('share');
	});
	//정답 및 해설 보기
	$('#show_correct_answer_btn').on('click', function(e) {
		e.preventDefault();
		console.log('correct');
		mNextStep();
	});
	//재도전
	// $('.restart-btn').on('click', function(e) {
	$('.restart-btn, #event-result-close-btn').on('click', function(e) {
		e.preventDefault();
		console.log('restart');
		location.reload();
	});



	/******************offline*******************/
	$('.offline-tab').on('click', function(e) {
		console.log('offline-tab click');

		$('#off_show_correct_answer_wrap').attr('class','youthqna-off-tab-'+$(this).attr('tabval'));
		window.scrollTo(0, 500);
	});

})