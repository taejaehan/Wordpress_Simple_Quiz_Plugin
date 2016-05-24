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
      typeLinkInput = '<input name="type_link" value="" />';
    }else if(typeId === '2'){
      typeLinkInput = '<input name="type_link_file" type="file" isDirty="FALSE" />'
                      +'ALT : <input name="img_alt" type="name" value=""/>';
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
  $("input[name=is_hidden], input[name=show_online], input[name=show_offline]").change(function(){
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

        // TODO : text에서 picture나 video로 변경시 input값 다시 넣어주기
        // videoInput = $(this).parent().find("input[name=type_link]");
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
    e.preventDefault();
    var data = {
      'action': 'youthqna_excel_down',
    };
    $.ajax({
        url: mUrl,
        data: data,
        method: "POST",
        // dataType: "json",
        success: function(res) {
            console.log('success');
            console.log('res : ' + JSON.stringify(res));
            window.location.href=res;
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

});


