var mUrl = '/wp-admin/admin-ajax.php';
var mQuizNum = 5;
var mUserScore = 4;
var mCategoryId = 1;
jQuery(document).ready(function($){

	var mQuizSection = $('#youthqna_quiz_section');
	var mCurrentQuizStep = 0;
	function mNextStep(){
		mCurrentQuizStep++;
		mQuizSection.attr('class','youthqna-step-'+mCurrentQuizStep);
	};

	
	
	//퀴즈 스타트
	$('#youthqna_quiz_start_wrap').on('click', function(e) {
		mNextStep();
	});

	//답변 선택
	$('.youthqna-op-btn').on('click', function(e) {
		$(this).parent().siblings('li').removeClass('selected-op');
		$(this).parent().addClass('selected-op');
		$(this).parents('.youth-quiz').attr('userSelOp', $(this).val());
	});

	//다음 문제 및 채점결과 보기
	$('.youthqna-next-btn').on('click', function(e) {

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
		if($('#'+newxQuizWrap).length !== 0){
			mNextStep();
		}else{
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
		            $('#youthqna_result').html('총 '+mUserScore+'개를 맞추셨습니다.');
					var ajaxResults = res.results;
					for(var i=0; i < ajaxResults.length; i++){
						var ajaxResult = ajaxResults[i];
						var answerHtml = '';
						answerHtml = "<div>"
						            +"정답 : "+ ajaxResult.correct_index +" "+ ajaxResult.correct_answer
						            +"<p>"
						            +"해설 : "+ ajaxResult.explanation
						            +"</p>"
						            +"</div>"
						$('#youth_quiz_answer_'+ajaxResult.q_id).html(answerHtml);
					};
					mNextStep();
		        }
		    });
			
		};
	}); 

	$('.event-checkbox').change(function(){
		$(this).siblings('input').attr('checked',false);
	});
	//이벤트 참여하기
	$('#join_event_btn').on('click', function(e) {
		console.log('event');
		mNextStep();
	});

	//이벤트 참여완료
	$('#event-submit-btn').on('click', function(e) {
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
		var data = {
	      'action': 'youthqna_join_event',
	      'user_name': userName,
	      'user_phone': userPhone,
	      'user_score': mUserScore,
	      'category_id' : mCategoryId
	    };
		$.ajax({
	        url: mUrl,
	        data: data,
	        method: "POST",
	        success: function(res) {
	            console.log('success');
	            console.log('res : ' + JSON.stringify(res));
	            mNextStep();
	   //          mUserScore = res.correctNum;
	   //          $('#youthqna_result').html('총 '+mUserScore+'개를 맞추셨습니다.');
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
		console.log('share');
	});
	//정답 및 해설 보기
	$('#show_correct_answer_btn').on('click', function(e) {
		console.log('correct');
		mNextStep();
	});
	//재도전
	$('.restart-btn').on('click', function(e) {
		console.log('restart');
		location.reload();
	});

})