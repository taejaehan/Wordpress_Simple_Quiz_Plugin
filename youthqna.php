<?php
/*
Plugin Name: 청춘문답
Plugin URI: http://blog.samsung.co.kr/
Description: A plugin for the 청춘문답
Version: 1.0
Author: TaeJae Han
Author URI: http://dmajor.kr
*/
/*
워드프레스 플러그인이 활성화 되면 실행
*/
register_activation_hook( __FILE__, 'youth_qna_install' );
add_action('admin_menu', 'add_admin_youth_qna_menu');
add_action( 'wp_enqueue_scripts', 'youth_qna_scripts' );
add_action( 'wp_ajax_youthqna_change_quiz', 'youthqna_change_quiz' );
add_action( 'wp_ajax_nopriv_youthqna_change_quiz', 'youthqna_change_quiz' );

add_action( 'wp_ajax_youthqna_del_quiz', 'youthqna_del_quiz' );
add_action( 'wp_ajax_nopriv_youthqna_del_quiz', 'youthqna_del_quiz' );
add_action( 'wp_ajax_youthqna_add_category', 'youthqna_add_category' );
add_action( 'wp_ajax_nopriv_youthqna_add_category', 'youthqna_add_category' );
add_action( 'wp_ajax_youthqna_get_results', 'youthqna_get_results' );
add_action( 'wp_ajax_nopriv_youthqna_get_results', 'youthqna_get_results' );
add_action( 'wp_ajax_youthqna_join_event', 'youthqna_join_event' );
add_action( 'wp_ajax_nopriv_youthqna_join_event', 'youthqna_join_event' );
add_action( 'wp_ajax_youthqna_change_banner', 'youthqna_change_banner' );
add_action( 'wp_ajax_nopriv_youthqna_change_banner', 'youthqna_change_banner' );


add_shortcode( 'youthqna_online_quiz', 'youthqna_shortcode' );


// add_action( 'wp_ajax_youthqna_excel_down', 'youthqna_excel_down' );
// add_action( 'wp_ajax_nopriv_youthqna_excel_down', 'youthqna_excel_down' );
// function youthqna_excel_down(){
//   error_reporting(E_ALL);
//   ini_set('display_errors', TRUE);
//   ini_set('display_startup_errors', TRUE);
//   ini_set('memory_limit','-1');
//   ini_set("max_execution_time","0");
//   define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');
//   date_default_timezone_set('Asia/Seoul');

//   // 메모리제한 제거
//   // ini_set('memory_limit', -1);

//   header("Content-Type: text/html; charset=utf-8");
//   header("Content-Encoding: utf-8");

//   require_once 'PHPExcel.php';
//   require_once 'PHPExcel/IOFactory.php';
//   $objPHPExcel = new PHPExcel();

//   // Set properties
//   $objPHPExcel->getProperties()
//   ->setCreator("")
//   ->setLastModifiedBy("")
//   ->setTitle("")
//   ->setSubject("")
//   ->setDescription("")
//   ->setKeywords("")
//   ->setCategory("License");

//   $objPHPExcel->setActiveSheetIndex(0)
//   ->setCellValue("A1", "아이디")
//   ->setCellValue("B1", "이름")
//   ->setCellValue("C1", "휴대폰")
//   ->setCellValue("D1", "카테고리")
//   ->setCellValue("E1", "맞춘갯수")
//   ->setCellValue("F1", "생성날짜");

//   global $wpdb;
//   $events = $wpdb->get_results( 'SELECT id, name, phone, c_id, score, created_at FROM youth_event');
  
//   $i = 2;
//   foreach($events as $e){
//     $objPHPExcel->setActiveSheetIndex(0)
//     ->setCellValue("A$i", $e->id)
//     ->setCellValue("B$i", $e->name)
//     ->setCellValue("C$i", $e->phone)
//     ->setCellValue("D$i", $e->c_id)
//     ->setCellValue("E$i", $e->score)
//     ->setCellValue("F$i", $e->created_at);
//     $i++;
//   }

//   $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
//   $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
//   $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
//   $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
//   $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(10);
//   $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);

//   // 파일의 저장형식이 utf-8일 경우 한글파일 이름은 깨지므로 euc-kr로 변환해준다.
//   $filename = iconv("UTF-8", "EUC-KR", "youthqna");

//   // Redirect output to a client’s web browser (Excel5)
//   header('Content-Type: application/vnd.ms-excel');
//   header('Content-Disposition: attachment;filename="' . $filename . '.xls"');
//   header('Cache-Control: max-age=0');

//   $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
//   // $objWriter->save('php://output');
//   $objWriter->save($filename . '.xls');
//   // echo(plugins_url().'/youth_qna/youth_qna_excel.php');
//   echo ('phpExcel/'.$filename . '.xls');
//   exit;
// }


/**
 * [이벤트에 참여합니다]
 */
function youthqna_join_event(){
  global $wpdb;
  $nonce = wp_create_nonce( 'my-nonce' );
  $nonce=$_POST['_wpnonce_name'];

  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ipInfo = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ipInfo = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ipInfo = $_SERVER['REMOTE_ADDR'];
  };
  $userName = $_POST['user_name'];
  $userPhone = $_POST['user_phone'];
  $userScore = $_POST['user_score'];
  $categoryId = $_POST['category_id'];
  // echo($userName);
  // echo($userPhone);
  // echo($userScore);
  // echo($categoryId);
  // return;
  $results = $wpdb->insert( 
    'youth_event', 
    array( 
      'name' => $userName,  
      'phone' => $userPhone,  
      'score' => $userScore,  
      'c_id' => $categoryId,
      'created_at' => current_time( 'mysql' )
    ), 
    array( 
      '%s', 
      '%s', 
      '%d', 
      '%d', 
      '%s' 
    ) 
  );
  if($results === FALSE){
    echo($results);
  }else{
    echo(TRUE);
  }
  wp_die();
}

/*
 * [퀴즈 페이지 view를 생성합니다]
 */
function youthqna_html_form_code($categoryId = ''){
  global $wpdb;
  //임시로 카테고리 아이디 지정
  // $categoryId = 1;
  $quizzes = $wpdb->get_results( 'SELECT id,type_id,field_id,type_link,question FROM youth_question WHERE deleted_at is NULL AND c_id='.$categoryId);
  $answers = $wpdb->get_results( 'SELECT id,q_id,answer FROM youth_answer ');
  $banners = $wpdb->get_results( 'SELECT * FROM youth_banner WHERE c_id='.$categoryId);
  date_default_timezone_set('Asia/Seoul');
  ?>
  <div id="youthqna_full_wrap">
    
    <div id="youthqna_top_wrap">
      <section id="youthqna_top_section">
        <div id="youthqna_top_common">
          <img src="<?php echo plugins_url(); ?>/youth_qna/imgs/top_common_bg.png" alt="뇌섹청춘! 퀴즈왕에 도전하라! 2016PLAY THE CHALLENGE 청춘문답 맛보기 퀴즈"/>
        </div>
        <?php
          if(intval($banners[0]->is_hidden) === 0):
        ?>
        <a href="<?php echo $banners[0]->banner_href; ?>"><img src="<?php echo $banners[0]->banner_link; ?>" alt="<?php echo $banners[0]->banner_alt; ?>"/></a>
        <?php
          endif;
        ?>
      </section>
      <!-- quiz section -->
      <section id="youthqna_quiz_section" class="youthqna-step-1">
        <!--quiz-->
        <div id="youthqna_quiz_wrap">
          <input id="youth_quiz_category" type="hidden" value="<?php echo $categoryId ?>">
          <div id="youthqna_quiz_start_wrap" class="quiz-step" >
            <img src="<?php echo plugins_url(); ?>/youth_qna/imgs/quiz_start_bg_01.jpg" alt="new청춘문답 퀴즈시작을 누르시면 총 5문제의 퀴즈가 출제됩니다 맛보기 퀴즈만 풀기 아쉽다면? 실제 '라이브 퀴즈 콘서트' 청춘문답 우승에 도전해 보세요! 지식충전 이벤트! 퀴즈에 참여자 중, 추첨을 통해 아메리카노 기프트콘을 드립니다! -참여기간: 2016년5월23일~6월3일 -경품:아메리카노 기프티콘(100명) -당첨자는 개인 정보 입력한 휴대전화로 개별 연락 드립니다." />
            <button id="youth_quiz_start"></button>
          </div>
        <?php
          $agent = $_SERVER['HTTP_USER_AGENT'];
          $ie8    = stripos($agent,"MSIE 8.0");
          if($ie8){
           $useragent_class = 'ie8';
          }
          if(count($quizzes)):
            $qindex = 1;
            foreach($quizzes as $quiz):
        ?>  
          <div class="quiz-step youth-quiz" id="youth_quiz_<?php echo $qindex ?>" quizIndex="<?php echo $qindex ?>" quizId="<?php echo $quiz->id ?>" userSelOp="">

            <div class="youth-quiz-index-wrap">
              <!-- Q<?php echo $qindex ?> -->
              <img class="youth-quiz-index" src="<?php echo plugins_url(); ?>/youth_qna/imgs/q_0<?php echo $qindex ?>.png" />
              <img class="youth-quiz-field" src="<?php echo plugins_url(); ?>/youth_qna/imgs/field_0<?php echo $quiz->field_id ?>.png" />
            </div>
            <p class="youth-quiz-question">
            <?php echo $quiz->question ?>
            </p>
            <div class="youth-quiz-refer">
          <?php
            $typeId = intval($quiz->type_id);
            if( $typeId === 2):
          ?>
            <img class="youth-quiz-refer-content" src="<?php echo($quiz->type_link)?>">
          <?php
            elseif( $typeId === 3):
          ?>
            
              <iframe class="youth-quiz-refer-content" width="560" height="315" src="https://www.youtube.com/embed/<?php echo $quiz->type_link; ?>" frameborder="0" allowfullscreen></iframe>
            
          <?php
            endif;
          ?>
            </div>
            <div class="youth-quiz-answer">
              <ol class="youth-quiz-op" >
          <?php 
            $aindex = 1;
            foreach($answers as $a): 
              if($quiz->id === $a->q_id):
          ?>
              <li class="youthqna-op">
                <button class="youthqna-op-btn" value="<?php echo($a->id) ?>">
                  <!-- <?php echo($aindex) ?> -->
                  <label class="youthqna-op-index op-index-<?php echo($aindex) ?>"></label>
                  <span class="youthqna-op-answer"><?php echo($a->answer) ?></span>
                </button>
              </li>
          <?php
              $aindex++;
              endif;
            endforeach;
          ?>
              </ol>
            </div>
            <div class="youth-quiz-bottom-wrap">
              <img class="youth-quiz-current" src="<?php echo plugins_url(); ?>/youth_qna/imgs/q_current_0<?php echo $qindex ?>.png" />
              <button class="youthqna-next-btn" disabled></button>
            </div>
          </div>
        <?php
          $qindex++;
          endforeach;
          else:
        ?>
          <div><?php _e('No Quizzes found.', 'YOUTH_QNA') ?></div>
        <?php 
          endif;
        ?> 
        </div>
        <!--quiz result-->
        <div id="youthqna_result_wrap" pluginImgUrl="<?php echo plugins_url(); ?>/youth_qna/imgs/" class="quiz-step">
          <div>
          <img id="youthqna_result_score" src="<?php echo plugins_url(); ?>/youth_qna/imgs/q_result_00.png" />
          <img id="youthqna_result_text" src="<?php echo plugins_url(); ?>/youth_qna/imgs/q_results_empty.png" />
          </div>
          <div class="youthqna-result-bottom">
          <button class="restart-btn">다시 풀기</button>
          <button id="join_event_btn">참여 완료</button>
          </div>
        </div>

        <!--quiz event-->
        <div id="youthqna_event_wrap" class="quiz-step">
          <div class="form-contact">
              <label for="user_name" class="contact-label">이 름</label>
              <input type="text" name="user_name" id="user_name" value="" placeholder="이름을 입력해주세요">
          </div>
          <div class="form-contact">
              <label for="user_phone" class="contact-label">휴대폰 번호</label>
              <input type="number" name="user_phone" id="user_phone" value="" placeholder="'-'없이 입력해주세요" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
          </div>
          <div id="youthqna_faq_agree" class="form-private">
            <!-- <label>개인정보 수집 및 이용 동의</label>
            <div class="form-agree">
                <input type="checkbox" id="checkbox-01" class="form-checkbox" name="private-allow-01" value="">
                <span></span>
                <label for="cb-private-allow">동의합니다</label>
            </div>
            <div class="form-agree">
                <input type="checkbox" id="checkbox-02" class="form-checkbox" name="private-allow-02" value=""checked>
                <span></span>
                <label for="cb-private-allow">동의하지 않음</label>
            </div> -->
            <input id="event_faq_agree_1" class="event-checkbox" type="checkbox"><label></label>
            <p>동의합니다</p>
            <input id="event_faq_agree_2" class="event-checkbox" type="checkbox" checked><label></label> 
            <p>동의하지 않음</p>
          </div>
          <button type="button" id="event-submit-btn">제출하기</button>
        </div>

        <!--quiz event done-->
        <div id="youthqna_event_result_wrap" class="quiz-step">
          <div>참여해주셔서 감사합니다</div>
          <!-- <button id="quiz_share_btn">공유 하기</button> -->
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo site_url(); ?>/answer<?php echo $categoryId; ?>/" class="fb" target="_blank">facebook</a>
          <a  onclick="window.open('https://twitter.com/intent/tweet?original_referer=http%3A%2F%2Fblog.samsung.com%2F5562%2F&amp;text=<?php echo urlencode(html_entity_decode('아는만큼 보인다!경제경영, 과학기술, 인문사회, 문화예술 다양한 분야의 퀴즈를실제 풀어보고 전문가들의 해설을 듣는 라이브 퀴즈 콘서트 <청춘문답> '.site_url().'/answer'.$categoryId.'/'));?>','twitter_share_dialog','width=626 height=436'); return false;" class="twitter" target="_blank">twitter</a>
          <button id="show_correct_answer_btn">정답 및 해설보기</button>
          <button class="restart-btn">다시 풀기</button>
        </div>

        <!--quiz correct answer-->
        <div id="show_correct_answer_wrap" class="quiz-step" >
          <?php
              $qindex = 1;
              foreach($quizzes as $quiz):
          ?>  
            <!--edit quiz-->
            <div id="youth_correct_quiz_<?php echo $quiz->id ?>" >
              <div>
              Q<?php echo $qindex ?>
              <?php echo $quiz->question ?>
              </div>
              <div id="youth_correct_quiz_answer_<?php echo $quiz->id ?>">

              </div>
            </div>
          <?php
            $qindex++;
            endforeach;
          ?> 
          <button class="restart-btn">다시 풀기</button>
        </div>

      </section>
    </div>
    <section id="youthqna_bottom_section">
      <!-- <img src="<?php echo plugins_url(); ?>/youth_qna/imgs/bottom_banner_01.png"/> -->
      <div id="youthqna_bottom_img_wrap">
      <?php
        for($i=1; $i < 4; $i++):
      ?>
        <a href="<?php echo $banners[$i]->banner_href; ?>"><img src="<?php echo $banners[$i]->banner_link; ?>" alt="<?php echo $banners[$i]->banner_alt; ?>"/></a>
      <?php
        endfor;
      ?>
      </div>
    </section>
  </div> 

  <!--youthqna_full_wrap-->
  <?php

  

  
}


/**
 * [공유 meta 설정]
 */
function jetpack_og_tags_youthqna( $tags ) {

  
  // if($tags['og:url']=="http://blog.samsung.com/cool2015/")
  // 
  // $currentUrl = $tags['og:url'];
  global $post;
  $slug = get_post( $post )->post_name;
  $currentPostNum = substr($slug, -1);
  if($slug === 'answer1'){
    // unset( $tags['og:image'] );
    unset( $tags['og:url'] );
    unset( $tags['og:title'] );
    unset( $tags['og:description'] );
    unset( $tags['twitter:title'] );
    unset( $tags['twitter:description'] );

    // $fb_home_img = plugin_dir_url( __FILE__ )."imgs/thumb/region".rand(1,8).".jpg";
    // $tags['og:image'] = esc_url( $fb_home_img );
    // $tags['og:url'] = plugins_url()."/youth_qna/imgs/youthqna_share_".$currentPostNum.".png?".rand(123,9999999);
    $fb_share_img = plugins_url()."/youth_qna/imgs/youthqna_share_".$currentPostNum.".png";
    $tags['og:image'] = esc_url( $fb_share_img );
    $tags['og:title'] = "2016 삼성 플레이 더 챌린지– 청춘문답";
    $tags['og:description'] = "아는만큼 보인다!경제경영, 과학기술, 인문사회, 문화예술 다양한 분야의 퀴즈를실제 풀어보고 전문가들의 해설을 듣는 라이브 퀴즈 콘서트 <청춘문답>";
    $tags['twitter:title'] = "2016 삼성 플레이 더 챌린지– 청춘문답";
    $tags['twitter:description'] = "아는만큼 보인다!경제경영, 과학기술, 인문사회, 문화예술 다양한 분야의 퀴즈를실제 풀어보고 전문가들의 해설을 듣는 라이브 퀴즈 콘서트 <청춘문답>";
    // $tags['twitter:card']
    // $tags['twitter:image']
  }
  return $tags;
  
}

add_filter( 'jetpack_open_graph_tags', 'jetpack_og_tags_youthqna' );

/*
 * [퀴즈 페이지 shortcode]
 */
function youthqna_shortcode($attr){
  $categoryId = $attr[0];
  ob_start();
  youthqna_html_form_code($categoryId);
  return ob_get_clean();
}



/*
 * [퀴즈 페이지에서 퀴즈 결과를 요청합니다]
 */
function youthqna_get_results(){
  global $wpdb;
  $nonce = wp_create_nonce( 'my-nonce' );
  $nonce=$_POST['_wpnonce_name'];

  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ipInfo = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ipInfo = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ipInfo = $_SERVER['REMOTE_ADDR'];
  };
  $quizAnswer = $_POST['quiz_answer'];
  $qCount = count($quizAnswer);

  $correctNum = 0;
  $quizResults = array();

  for($i=0; $i < $qCount; $i++){
    $userAnswer = $quizAnswer[$i];
    $qId = intval($userAnswer['quiz_id']);
    $aId = intval($userAnswer['answer_id']);
    $answers = $wpdb->get_results("SELECT * FROM youth_answer WHERE q_id=".$qId);
    $question = $wpdb->get_results("SELECT id, explanation FROM youth_question WHERE id=".$qId);
    // var_dump($answers);
    $aCount = count($answers);
    $correctId = 0;
    $correctAnswer = '';
    $correctIndex = 0;
    for($j=0; $j < $aCount; $j++){
      $dbAnswer = $answers[$j];
      $currectAnswerId = 0;
      if($dbAnswer->is_correct){
        $correctIndex = $j + 1;
        $correctId = intval($dbAnswer->id);
        $correctAnswer = $dbAnswer->answer;
        if($aId === intval($dbAnswer->id)){
          $correctNum++;
        }
      }
    }
    $quizResults[$i]->correct_id = $correctId;
    $quizResults[$i]->correct_index = $correctIndex;
    $quizResults[$i]->correct_answer = $correctAnswer;
    $quizResults[$i]->user_id = $aId;
    $quizResults[$i]->explanation = $question[0]->explanation;
    $quizResults[$i]->q_id = $question[0]->id;
  };

  $return = array(
      'correctNum' => $correctNum,
      'results'    => $quizResults
  );
  wp_send_json($return);
  wp_die();
}
/**
 * [관리자 페이지에서 카테고리를 추가합니다]
 */
function youthqna_add_category(){
  global $wpdb;
  $nonce = wp_create_nonce( 'my-nonce' );
  $nonce=$_POST['_wpnonce_name'];

  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ipInfo = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ipInfo = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ipInfo = $_SERVER['REMOTE_ADDR'];
  };
  $c_name = $_POST['c_name'];
  $results = $wpdb->insert( 
    'youth_category', 
    array( 
      'name' => $c_name,  
    ), 
    array( 
      '%s', 
    ) 
  );
  if($results === FALSE){
    echo($results);
  }else{
    echo(TRUE);
  }
  wp_die();
}
/**
 * [관리자 페이지에서 퀴즈를 삭제합니다]
 */
function youthqna_del_quiz(){
  global $wpdb;
  $nonce = wp_create_nonce( 'my-nonce' );
  $nonce=$_POST['_wpnonce_name'];

  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ipInfo = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ipInfo = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ipInfo = $_SERVER['REMOTE_ADDR'];
  };
  $quiz_id = intval($_POST['quiz_id']);
  $results = $wpdb->update( 
    'youth_question', 
    array( 
      'deleted_at' => current_time( 'mysql' ),  
    ), 
    array( 'id' => $quiz_id), 
    array( 
      '%s' 
    ), 
    array( '%d' ) 
  );
  if($results === FALSE){
    echo($results);
  }else{
    echo(TRUE);
  }
  wp_die();
}


/**
 * [관리자 페이지에서 배너를 추가하거나 수정합니다]
 */
function youthqna_change_banner(){

  $banner_link = null;
  
  $banner_id = $_POST['banner_id'];
  $banner_type_id = $_POST['banner_type_id'];
  $category = $_POST['category'];
  $banner_alt = $_POST['banner_alt'];
  $banner_href = $_POST['banner_href'];
  $is_hidden = $_POST['is_hidden'];

  if($is_hidden === null || $is_hidden === ''){
    $is_hidden = 0 ;
  };
  $uploadedfile = $_FILES['banner_link'];
  if($uploadedfile !== null){
    if ( ! function_exists( 'wp_handle_upload' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    $upload_overrides = array( 'test_form' => false );

    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

    if ( $movefile && ! isset( $movefile['error'] ) ) {
        // echo "File is valid, and was successfully uploaded.\n";
        // var_dump( $movefile );
        // wp_send_json($movefile);
        $banner_link = $movefile['url'];
    } else {
        /**
         * Error generated by _wp_handle_upload()
         * @see _wp_handle_upload() in wp-admin/includes/file.php
         */
        echo $movefile['error'];
    };
  };

  global $wpdb;
  $nonce = wp_create_nonce( 'my-nonce' );
  $nonce=$_POST['_wpnonce_name'];

  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ipInfo = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ipInfo = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ipInfo = $_SERVER['REMOTE_ADDR'];
  };

  $dbValueArr = array( 
        'c_id' => $category,  
        't_id' => $banner_type_id,
        'is_hidden' => $is_hidden,
        'banner_href' => $banner_href,
        'banner_alt' => $banner_alt,
      );
  $dbTypeArr = array( 
        '%d', 
        '%d',
        '%d',
        '%s',
        '%s'
      );
  $results;
  //type링크가 변경될 경우에만 type_link포함
  if($banner_link !== null){
    $dbValueArr['banner_link'] = $banner_link;
    array_push($dbTypeArr, "%s");
  };


  if($banner_id === ''){
    $results = $wpdb->insert( 
      'youth_banner', 
      $dbValueArr, 
      $dbTypeArr
    );
  }else{
    $results = $wpdb->update( 
      'youth_banner', 
      $dbValueArr, 
      array( 'id' => $banner_id ), 
      $dbTypeArr, 
      array( '%d' ) 
    );
  }

  if($results === FALSE){
    echo($results);
    return;
  }
  echo(TRUE);
  wp_die();
}

/**
 * [관리자 페이지에서 퀴즈를 추가하거나 수정합니다]
 */
function youthqna_change_quiz(){

  
  // echo('youthqna_change_quiz ok');
  $quiz_id = $_POST['quiz_id'];
  $category = $_POST['category'];
  $type = $_POST['type'];
  $type_link = $_POST['type_link'];
  $img_alt = $_POST['img_alt'];
  $field = $_POST['field'];
  $question = $_POST['question'];
  $answerCount = $_POST['answer-count'];
  $explanation = $_POST['explanation'];
  $answer = new ArrayObject();
  $answerId = new ArrayObject();
  $answerCheck = new ArrayObject();

  $uploadedfile = $_FILES['type_link_file'];
  if($uploadedfile !== null){
    if ( ! function_exists( 'wp_handle_upload' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/file.php' );
    }
    $upload_overrides = array( 'test_form' => false );

    $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );

    if ( $movefile && ! isset( $movefile['error'] ) ) {
        // echo "File is valid, and was successfully uploaded.\n";
        // var_dump( $movefile );
        // wp_send_json($movefile);
        $type_link = $movefile['url'];
    } else {
        /**
         * Error generated by _wp_handle_upload()
         * @see _wp_handle_upload() in wp-admin/includes/file.php
         */
        echo $movefile['error'];
    };
  };

  echo('$category : '.$category);
  for($i=0; $i < $answerCount; $i++){

    $answer->append($_POST['answer-text-'.$i]);
    $answerId->append($_POST['answer-id-'.$i]);
    $answerCheck->append(empty($_POST['answer-checkbox-'.$i]) ? 0 : 1);
    // var_dump($answer);
    // var_dump($answerCheck);
    // echo('$answer->$i : '.$answer[$i]);
    // echo('$answerCheck->$i : '.$answerCheck[$i]);
  };

  global $wpdb;
  $nonce = wp_create_nonce( 'my-nonce' );
  $nonce=$_POST['_wpnonce_name'];

  if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ipInfo = $_SERVER['HTTP_CLIENT_IP'];
  } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ipInfo = $_SERVER['HTTP_X_FORWARDED_FOR'];
  } else {
      $ipInfo = $_SERVER['REMOTE_ADDR'];
  };

  //$wpdb->update( $table, $data, $where, $format = null, $where_format = null );

  // $wpdb->query($wpdb->prepare("UPDATE youth_question
  //       SET c_id=%d, type_id=%d,type_link=%s, question=%s  
  //       WHERE id=%d", $category, $type, $type_link, 
  //       $question, $quiz_id));
  //       
  $dbValueArr = array( 
        'c_id' => $category,  
        'type_id' => $type,
        'field_id' => $field,
        'question' => $question,
        'explanation' => $explanation
      );
  $dbTypeArr = array( 
        '%d', 
        '%d', 
        '%d', 
        '%s',
        '%s'
      );
  //type링크가 변경될 경우에만 type_link포함
  if($type_link !== null){
    $dbValueArr['type_link'] = $type_link;
    array_push($dbTypeArr, "%s");
  };
  //img_alt이 있을 경우에만 img_alt포함
  if($img_alt !== null){
    $dbValueArr['img_alt'] = $img_alt;
    array_push($dbTypeArr, "%s");
  };
  if($quiz_id === 'new'){
    $results = $wpdb->insert( 
      'youth_question', 
      $dbValueArr, 
      $dbTypeArr
    );
    $quiz_id = $wpdb->insert_id;
  }else{
    $results = $wpdb->update( 
      'youth_question', 
      $dbValueArr, 
      array( 'id' => $quiz_id ), 
      $dbTypeArr, 
      array( '%d' ) 
    );
  }
  if($results === FALSE){
    echo($results);
    return;
  }
  for($i=0; $i < $answerCount; $i++){
    // $wpdb->query($wpdb->prepare("UPDATE youth_answer
    //     SET answer=%s, is_correct=%d
    //     WHERE id=%d", $answer[$i], $answerCheck[$i], $answerId[$i]));
    //     
    if($answerId[$i] === 'new'){
      $results = $wpdb->insert( 
        'youth_answer', 
        array( 
          'q_id' => $quiz_id,  
          'answer' => $answer[$i],  
          'is_correct' => $answerCheck[$i]
        ), 
        array( 
          '%d',
          '%s', 
          '%d' 
        ) 
      );
    }else{
      $results = $wpdb->update( 
        'youth_answer', 
        array( 
          'answer' => $answer[$i],  
          'is_correct' => $answerCheck[$i]
        ), 
        array( 'id' => $answerId[$i] ), 
        array( 
          '%s', 
          '%d', 
        ), 
        array( '%d' ) 
      );
    }
    if($results === FALSE){
      echo($results);
      return;
    }
  }
  echo(TRUE);
  wp_die();
}

/**
 * [워드프레스 DB를 생성합니다.]
 */
function youth_qna_install()
{
  global $wpdb;
  //청춘문답 타입 테이블
  $sql1 = "CREATE TABLE IF NOT EXISTS youth_type (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(50) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 카테고리 테이블
  $sql2 = "CREATE TABLE IF NOT EXISTS youth_category (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 필드(문제영역) 테이블
  $sql3 = "CREATE TABLE IF NOT EXISTS youth_field (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 문제 테이블
  $sql4 = "CREATE TABLE IF NOT EXISTS youth_question (
    id int(11) NOT NULL AUTO_INCREMENT,
    c_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    field_id int(11) NOT NULL,
    type_link varchar(300) DEFAULT '' NOT NULL,
    img_alt varchar(100) DEFAULT NULL,
    question varchar(200) NOT NULL,
    points int(11) DEFAULT 20 NOT NULL,
    explanation varchar(300) NOT NULL,
    deleted_at datetime,
    PRIMARY KEY (id),
    FOREIGN KEY(c_id) REFERENCES youth_category(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(type_id) REFERENCES youth_type(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(field_id) REFERENCES youth_field(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 답 테이블
  $sql5 = "CREATE TABLE IF NOT EXISTS youth_answer (
    id int(11) NOT NULL AUTO_INCREMENT,
    q_id int(11),
    answer varchar(200) NOT NULL,
    is_correct int(11) DEFAULT 0 NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY(q_id) REFERENCES youth_question(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 유저 이벤트 테이블
  $sql6 = "CREATE TABLE IF NOT EXISTS youth_event (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(50) NOT NULL,
    phone varchar(50) NOT NULL,
    c_id int(11) NOT NULL,
    score int(11) DEFAULT 0 NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY(c_id) REFERENCES youth_category(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  //청춘문답 배너 카테고리 테이블
  $sql7 = "CREATE TABLE IF NOT EXISTS youth_banner_type (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  //청춘문답 온라인 배너 테이블
  $sql8 = "CREATE TABLE IF NOT EXISTS youth_banner (
    id int(11) NOT NULL AUTO_INCREMENT,
    c_id int(11) NOT NULL,
    t_id int(11) NOT NULL,
    banner_link varchar(300) NOT NULL,
    banner_alt varchar(300) NOT NULL,
    banner_href varchar(300) NOT NULL,
    is_hidden int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY(c_id) REFERENCES youth_category(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(t_id) REFERENCES youth_banner_type(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  //default type setting
  // $sql9 = "INSERT INTO youth_type (`name`) VALUES ('Text'),('Picture'),('Video');
  // INSERT INTO youth_banner_type (`name`) VALUES ('상단 배너'),('하단 첫번째'),('하단 두번째'),('하단 세번째');
  // INSERT INTO youth_category (`name`) VALUES ('첫번째 온라인 퀴즈'),('두번째 온라인 퀴즈');
  // INSERT INTO youth_field (`name`) VALUES ('시사 응용 영역'),('경제 기초 영역'),('기타 영역');";


  $wpdb->query($sql1);
  $wpdb->query($sql2);
  $wpdb->query($sql3);
  $wpdb->query($sql4);
  $wpdb->query($sql5);
  $wpdb->query($sql6);
  $wpdb->query($sql7);
  $wpdb->query($sql8);
  // $wpdb->query($sql9);
}

//자바스크립트 포함, Ajax처리
function youth_qna_admin_init(){

  wp_enqueue_script('jquery-ui-datepicker');
  wp_register_script('youth_qna_excel_export', plugins_url( '/js/admin.js' , __FILE__ ), array('jquery'));

  wp_localize_script('youth_qna_excel_export', 'youth_qna_excel_export',
    array(
        //엑셀로 저장
        'save_excel'=>plugins_url().'/youth_qna/youth_qna_excel.php'
    ));
  wp_enqueue_script('youth_qna_excel_export');

  // wp_enqueue_style( 'youth_qna_admin_css', plugins_url() . '/youth_qna/css/admin.css', '', '1.0');
  // wp_enqueue_style( 'youth_qna_quiz_css', plugins_url() . '/youth_qna/css/quiz.css', '', '1.0');
  // wp_enqueue_script( 'youth_qna_admin', plugins_url() . '/youth_qna/js/admin.js', '', '1.0', true );
  // wp_enqueue_script( 'youth_qna_quiz', plugins_url() . '/youth_qna/js/quiz.js', '', '1.0', true );

}
add_action('admin_init', 'youth_qna_admin_init');

/**
 * [css, js 파일을 세팅]
 */
function youth_qna_scripts() {
  wp_enqueue_style( 'youth_qna_admin_css', plugins_url() . '/youth_qna/css/admin.css', '', '1.0');
  wp_enqueue_style( 'youth_qna_quiz_css', plugins_url() . '/youth_qna/css/quiz.css', '', '1.0');
  wp_enqueue_script( 'youth_qna_admin', plugins_url() . '/youth_qna/js/admin.js', '', '1.0', false );
  wp_enqueue_script( 'youth_qna_quiz', plugins_url() . '/youth_qna/js/quiz.js', '', '1.0', false );
 
}
/**
 * [워드프레스 관리자 메뉴를 생성합니다]
 */
function add_admin_youth_qna_menu() {
  add_menu_page( '청춘문답', '청춘문답', 'manage_options', 'youth_qna/admin.php');
  add_submenu_page( 'youth_qna/admin.php', '청춘문답', '온라인 퀴즈', 'manage_options', 'youth_qna/admin.php');
  add_submenu_page( 'youth_qna/admin.php', '청춘문답', '온라인 이벤트', 'manage_options', 'youth_qna/admin_event.php');
  add_submenu_page( 'youth_qna/admin.php', '청춘문답', '오프라인 정답해설', 'manage_options', 'youth_qna/admin_offline.php');
  add_submenu_page( NULL, '청춘문답', '엑셀', 'manage_options', 'youth_qna/youth_qna_excel.php');
}
?>