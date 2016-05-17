var mUrl = '/wp-admin/admin-ajax.php';
jQuery(document).ready(function($){


  /**
   * [카테고리 추가]
   */
  $('#category_add_btn').on('click', function(e) {
    var cName = $('#category_add_name').val();
    if(cName === null || cName === ''){
      alert('카테고리 이름을 입력하세요');
      return;
    }
    var data = {
      'action': 'youthqna_add_category',
      'c_name': cName
    };
    $.ajax({
        url: url,
        data: data,
        method: "POST",
        success: function(res) {
            console.log('success');
            console.log('res : ' + JSON.stringify(res));
            if(res){
              console.log('성공적으로 업로드 했습니다');
              // location.reload();
              window.location = "/wp-admin/admin.php?page=youth_qna%2Fadmin.php&category="+$(this).val();
            }else{
              console.log('업로드 실패');
            }
        }
    });
  });
  /**
   * [카테고리 변경]
   */
  $('#category_sel').change(function(){
    console.log('aa');
    // window.location.href = window.location.href + "&category="+$(this).val();
    window.location = "/wp-admin/admin.php?page=youth_qna%2Fadmin.php&category="+$(this).val();
  });

  
  
  $(".quiz-type-selctor").change(function(){
    var me = $(this),
        typeId = me.val(),
        typeLinkTd = $(this).parent().next(),
        typeLinkInput;
    if(typeId === '1'){
      typeLinkInput = '<input name="type_link" value="empty" readonly/>';
    }else if(typeId === '2'){
      typeLinkInput = '<input name="type_link_file" type="file" isDirty="FALSE" />';
    }else{
      typeLinkInput = '<input name="type_link" type="name" value="" />';
    }
    typeLinkTd.empty();
    typeLinkTd.html(typeLinkInput);
  });
  /**
   * [파일 설정 바꾸면 isDirty attr TRUE]
   */
  $("input[name=type_link_file], input[name=banner_link]").change(function(){
    console.log('file chagned');
    $(this).attr('isDirty','TRUE');
    $(this).prev().empty();
    // $(this).nextAll("input[name=type_link]").val();
  });

  /**
   * [배너 체크박스 value set]
   */
  $("input[name=banner_is_hidden]").change(function(){
    var value = 0;
    if($(this).is(":checked")){
      value = 1;
    }
    $(this).val(value);
  });
  
  /**
   * [정답 체크박스 한개만 체크 가능하도록 설정]
   */
  $('.answer-checkbox-input').change(function() {
    //true라면 다른 부분 false
    if($(this).is(":checked")){
      $(this).parents('td').find('.answer-checkbox-input').attr('checked',false);
    };
    $(this).attr('checked',true);
  });

  /**
   * [QUIZ BANNER submit (file이 변경되었으면 업로드)]
   */
  $('.banner-form').submit(function(){
    console.log('form SUBMIT');

    var me = $(this),
        form = me[0],
        formMethod = me.attr('method'),
        formData = new FormData(form),
        fileInput = $(this).parent().find("input[name=banner_link]");

    //file이 변경되었으면 file추가
    if(fileInput.length !== 0 && fileInput.attr('isDirty') === 'TRUE'){
      formData.append("banner_link",fileInput[0].files[0]);
      
    };

    $.ajax({
        url: mUrl,
        data: formData,
        method: formMethod,
        processData: false,
        contentType: false,
        success: function(res) {
            console.log('success');
            console.log('res : ' + JSON.stringify(res));
            location.reload();
        }
    });
    return false; //prevent form from submitting
  });

  /**
   * [QUIZ FORM submit (file이 변경되었으면 업로드)]
   */
  $('.quiz-form').submit(function(){
    console.log('form SUBMIT');

    var me = $(this),
        form = me[0],
        formMethod = me.attr('method'),
        formData = new FormData(form),
        fileInput = $(this).parent().find("input[name=type_link_file]");

    //file이 변경되었으면 file추가
    if(fileInput.length !== 0 && fileInput.attr('isDirty') === 'TRUE'){
      console.log('file not changed')
      formData.append("type_link_file",fileInput[0].files[0]);
    };
    $.ajax({
        url: mUrl,
        data: formData,
        method: formMethod,
        processData: false,
        contentType: false,
        success: function(res) {
            console.log('success');
            console.log('res : ' + JSON.stringify(res));
            location.reload();
        }
    });
    return false; //prevent form from submitting
  });

  $('.quiz-del-btn').on('click', function(e) {
    var qId = $(this).attr('qId');
    console.log(qId);
    var data = {
      'action': 'youthqna_del_quiz',
      'quiz_id': qId
    };
    $.ajax({
      type: "POST",
      url: mUrl,
      data:data,
      success: function(res){

        console.log('success');
        console.log('res : ' + JSON.stringify(res));
        if(res){
          location.reload();
          console.log('성공적으로 업로드 했습니다');
        }else{
          console.log('업로드 실패');
        }

      },
      complete: function(){
      },
      timeout: 60000
    });
  });

  // 엑셀 다운로드
  $("#youth_event_excel").click(function(e) {
    console.log('youth_event_excel');
    // e.preventDefault();
    location.href = youth_qna_excel_export.ㅌ;
    // var data = {
    //   'action': 'youthqna_excel_down',
    // };
    // $.ajax({
    //     url: url,
    //     data: data,
    //     method: "POST",
    //     // dataType: "json",
    //     success: function(res) {
    //         console.log('success');
    //         console.log('res : ' + JSON.stringify(res));
    //         window.location.href=res;
    //         // if(res){
    //         //   console.log('성공적으로 업로드 했습니다');
    //         //   // location.reload();
    //         //   window.location = "/wp-admin/admin.php?page=youth_qna%2Fadmin.php&category="+$(this).val();
    //         // }else{
    //         //   console.log('업로드 실패');
    //         // }
    //     }
    // });
  });

  // $('#new_quiz').on('click', function(e) {
    // console.log('new_quiz');
    // var quizRow =
    //       "<tr>"
    //       // +"<form id='quiz-form-new' method='post' action='/wp-admin/admin-ajax.php'>"
    //       +"<input name='action' type='hidden' value='youthqna_add_quiz' />"
    //       +"<input name='quiz_id' type='hidden' value='new' />"
    //       +"<td>NEW</td>"
    //       +"<td>"
    //         +"<select name='type'>"
    //           +"<option value='1'>Text</option>"
    //           +"<option value='2'>Picture</option>"
    //           +"<option value='3'>Vidoe</option>"
    //         +"</select>"
    //       +"</td>"
    //       +"<td>"
    //         +"<input name='type_link' type='name' value=''>"
    //       +"</td>"
    //       +"<td>"
    //         +"<input name='question' type='name' value=''>"
    //       +"</td>"
    //       +"<td>";
    //         for(var i=0; i < 4; i++){
    //           quizRow = quizRow +"<div>"
    //           +"<input name='answer-id-"+i+"' type='hidden' value='new'>"
    //           +"<span><input name='answer-text-"+i+"' type='text' value=''></span>"
    //           +"<input name='answer-checkbox-"+i+"' type='checkbox' value='new'>"
    //           +"</div>";
    //         };
    //         quizRow = quizRow
    //         +"<input name='answer-count' type='hidden' value='4'>"
    //       +"</td>"
    //       +"<td>"
    //         +"<input name='explanation' type='name' value=''>"
    //       +"</td>"
    //       +"<td>"
    //         +"<div><button id='add_new_quiz' type='submit' class='quiz-save-btn'>Save</button></div>"
    //       +"</td>"
    //       // +"</form>"
    //     +"</tr>";
    // $('#quiz-list').append(quizRow);
    // $('#add_new_quiz').on('click', function(e) {
    //   console.log('quiz');
    //   var parentTr = $(this).parents('tr');
    //   var action = parentTr.children("input[name*='action']").val();
    //   var quiz_id = parentTr.children("input[name*='quiz_id']").val();
    //   var type = parentTr.children("select[name*='type']").val();
    //   var type_link = parentTr.children("input[name*='type_link']").val();
    //   var question = parentTr.children("input[name*='question']").val();
    //   console.log(action);
      // var data = {
      //   'action': 'youthqna_add_quiz',
      //   'quiz_id': qId
      // };
      // $.ajax({
      //     url: url,
      //     data: data,
      //     method: formMethod,
      //     success: function(res) {
      //         // responseMsg.htmlx(res);
      //         console.log('success');
      //         console.log('res : ' + JSON.stringify(res));
      //         if(res){
      //           console.log('성공적으로 업로드 했습니다');
      //           location.reload();
      //         }else{
      //           console.log('업로드 실패');
      //         }
      //     }
      // });
    // });
  // });


  // $('#event_01 button').on('click', function(e) {
  //   e.preventDefault();
  //   console.log(this.id);

  //   $('#event_01 button').off('click');
  //   $('#event_01 button').attr('style',''); //cursor 제거

  //   //투표>결과보여주기>페이스북공유
  //   var vote = 0;
  //   if(this.id == 'eat_btn') vote = 0;
  //   if(this.id == 'some_btn') vote = 1;
  //   var data = {
  //     'action': 'gguljam_vote',
  //     'vote': vote
  //   };

  //   $.ajax({
  //     type: "POST",
  //     url: url,
  //     data:data,
  //     success: function(res){

  //       var eat_count = Number(res.eat_count);
  //       var some_count = Number(res.some_count);
  //       var count_sum = eat_count + some_count;
       
  //       //최소 1/4 넓이를 차지하게끔 설정
  //       var eat_ratio = eat_count / count_sum;
  //       var some_ratio = some_count / count_sum;
  //       if(eat_ratio < 0.25) eat_ratio = 0.25;
  //       else if(eat_ratio > 0.75) eat_ratio = 0.75;
  //       if(some_ratio < 0.25) some_ratio = 0.25;
  //       else if(some_ratio > 0.75) some_ratio = 0.75;

  //       var max_width_percent = 88; /*양쪽에 6% margin이 있으므로 width 100%는 88%*/
  //       var eat_width_percent = max_width_percent * eat_ratio;
  //       var some_width_percent = max_width_percent * some_ratio;

  //       var bg_percent = 50; /*배경은 40~90%*/
  //       var eat_bg_percent = (eat_ratio* -100) + 115;
  //       var some_bg_percent = (some_ratio* -100) + 115;

  //        // console.log('eat_count + some_count = ' + count_sum);
  //       // console.log('eat_ratio / some_ratio = ' + eat_ratio + ' / ' + some_ratio);
  //       // console.log('eat_width_percent / some_width_percent = ' + eat_width_percent + ' / ' + some_width_percent);
  //       // console.log('eat_bg_percent / some_bg_percent = ' + eat_bg_percent + ' / ' + some_bg_percent);

  //       //결과 보여주기
  //       $('#eat_count').html(eat_count + '명');
  //       $('#some_count').html(some_count+ '명');
        
  //       $('#eat_room').attr('style','width:'+eat_width_percent+'%; background-size:'+eat_bg_percent+'%');
  //       $('#some_room').attr('style','width:'+some_width_percent+'%; background-size:'+some_bg_percent+'%');

  //       //페이스북 공유 창 popup
  //       setTimeout(function () {
  //         FB.getLoginStatus(function(response) {
  //           if (response.authResponse) {
  //             // logged in and connected user, someone you know
  //             facebookShare(vote);
  //           } else {
  //             // no user session available, someone you dont know
  //             FB.login(function(response) {
  //                if (response.authResponse) {
  //                  console.log('Welcome!  Fetching your information.... ');
  //                  FB.api('/me', function(response) {
  //                    console.log('Good to see you, ' + response.name + '.');
  //                  });
  //                  facebookShare(vote);
  //                } else {
  //                  console.log('User cancelled login or did not fully authorize.');
  //                }
  //              });
  //           }
  //         });
  //       }, 1000);

  //     },
  //     complete: function(){
  //     },
  //     timeout: 60000
  //   });
  //   //$ajax 끝
  // });
  //$('#event_01 button') 클릭 끝 

  /*
  facebook 공유 
  @param : vote (0 - 먹방 / 1 - 썸방)
  */
  // function facebookShare (vote) {
  //   //공유 
  //   FB.ui( {
  //       method: 'feed',
  //       name: "꿀잼100%보장! '꿀잼펜션'",
  //       link: "http://blog.samsung.com/150secplay_2/",
  //       picture: "http://blog.samsung.com/wp-content/plugins/gguljam-pension/imgs/share"+vote+".jpg",
  //       caption: "http://blog.samsung.com/150secplay_2/",
  //       description : '먹방이냐, 썸방이냐? 당신의 선택은?'
  //     }, function( response ) {
  //         if ( response !== null && typeof response.post_id !== 'undefined' ) {
  //           // console.log( response );
  //           var data = {
  //             'action': 'gguljam_sns',
  //             'fb_id': FB.getUserID()
  //           };
  //           $.ajax({
  //             type: "POST",
  //             url: url,
  //             data:data,
  //             success: function(res){
  //               // console.log(data);
  //             }
  //           });
  //         }
  //     } );
  // }

});


