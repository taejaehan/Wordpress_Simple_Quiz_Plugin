var mUrl = '/wp-admin/admin-ajax.php';
var mQuizNum = 5;
var mUserScore = 4;
var mCategoryId = 1;
jQuery(document).ready(function($){

	var mResultScore = 0;
	$('.youthqna-op-btn').on('click', function(e) {
		$(this).parent().siblings('li').removeClass('selected-op');
		$(this).parent().addClass('selected-op');
		$(this).parents('.youth-quiz').attr('userSelOp', $(this).val());
		// var qindex = $(this).parents('.youth_quiz_op').attr('qindex');
		// $('#youth_op_selected_'+qindex).val($(this).val());
	});
	$('#quiz_share_btn').on('click', function(e) {
		console.log('share');
	});
	$('#join_event_btn').on('click', function(e) {
		console.log('event');
		$('#youthqna_result_wrap').css('display','none');
		$('#youthqna_event_wrap').css('display','block');
		// $('#restart_btn').css('display','block');
	});
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
		var chk1 = $('#checkbox-01').prop('checked');
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
	            $('#restart_btn').css('display','block');
	   //          mUserScore = res.correctNum;
	   //          $('#youthqna_result').html('총 '+mUserScore+'개를 맞추셨습니다.');
				// $('#youthqna_result_wrap').css('display','block');
				// $('#restart_btn').css('display','block');

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
	$('#show_correct_answer_btn').on('click', function(e) {
		console.log('correct');
		$('#youthqna_result_wrap').css('display','none');
		$('#show_correct_answer_wrap').css('display','block');
		$('#restart_btn').css('display','block');

	});
	$('#restart_btn').on('click', function(e) {
		console.log('restart');
		location.reload();
	});
	$('.youthqna-next-btn').on('click', function(e) {

		var qindex = parseInt($(this).attr('currentQuizIndex'));
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
			$('#'+newxQuizWrap).css('display','block');
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
					$('#youthqna_result_wrap').css('display','block');
					$('#restart_btn').css('display','block');

					var ajaxResults = res.results;
					for(var i=0; i <= ajaxResults.length; i++){
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
					

		            // if(res){
		            //   console.log('성공적으로 업로드 했습니다');
		            //   // location.reload();
		            //   window.location = "/wp-admin/admin.php?page=youth_qna%2Fadmin.php&category="+$(this).val();
		            // }else{
		            //   console.log('업로드 실패');
		            // }
		        }
		    });
			
		};
	}); 


	/**
	 * [facebookShare]
	 */
	function facebookShare () {
	//공유 
	FB.ui( {
	    method: 'feed',
	    name: "2016 삼성 플레이 더 챌린지– 청춘문답",
	    link: "http://blog.samsung.com/0000/",
	    picture: "http://blog.samsung.com/0000/xxx.jpg",
	    caption: "http://blog.samsung.com/0000/",
	    description : '아는만큼 보인다!경제경영, 과학기술, 인문사회, 문화예술 다양한 분야의 퀴즈를실제 풀어보고 전문가들의 해설을 듣는 라이브 퀴즈 콘서트 <청춘문답>http://blog.samsung.com/0000/'
	  }, function( response ) {
	      // if ( response !== null && typeof response.post_id !== 'undefined' ) {
	      //   // console.log( response );
	      //   var data = {
	      //     'action': 'gguljam_sns',
	      //     'fb_id': FB.getUserID()
	      //   };
	      //   $.ajax({
	      //     type: "POST",
	      //     url: url,
	      //     data:data,
	      //     success: function(res){
	      //       // console.log(data);
	      //     }
	      //   });
	      // }
	  } );
	}

})