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

add_shortcode( 'youthqna_online_quiz', 'youthqna_shortcode' );



function jetpack_og_tags_youthqna( $tags ) {
  // unset( $tags['og:image'] );
  unset( $tags['og:url'] );
  unset( $tags['og:title'] );
  unset( $tags['og:description'] );
  unset( $tags['twitter:title'] );
  unset( $tags['twitter:description'] );

  // $fb_home_img = plugin_dir_url( __FILE__ )."imgs/thumb/region".rand(1,8).".jpg";
  // $tags['og:image'] = esc_url( $fb_home_img );
  $tags['og:url'] = "http://samsungblog.major-apps-1.com/youthqna_online_quiz/?".rand(123,9999999);
  $tags['og:title'] = "2016 삼성 플레이 더 챌린지– 청춘문답";
  $tags['og:description'] = "아는만큼 보인다!경제경영, 과학기술, 인문사회, 문화예술 다양한 분야의 퀴즈를실제 풀어보고 전문가들의 해설을 듣는 라이브 퀴즈 콘서트 <청춘문답>";
  $tags['twitter:title'] = "2016 삼성 플레이 더 챌린지– 청춘문답";
  $tags['twitter:description'] = "아는만큼 보인다!경제경영, 과학기술, 인문사회, 문화예술 다양한 분야의 퀴즈를실제 풀어보고 전문가들의 해설을 듣는 라이브 퀴즈 콘서트 <청춘문답>";
  // $tags['twitter:card']
  // $tags['twitter:image']
  return $tags;
}

add_filter( 'jetpack_open_graph_tags', 'jetpack_og_tags_youthqna' );

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
function youthqna_html_form_code(){
  global $wpdb;
  //임시로 카테고리 아이디 지정
  $categoryId = 1;
  $quizzes = $wpdb->get_results( 'SELECT id,type_id,type_link,question FROM youth_question WHERE deleted_at is NULL AND c_id='.$categoryId);
  $answers = $wpdb->get_results( 'SELECT id,q_id,answer FROM youth_answer ');
  date_default_timezone_set('Asia/Seoul');
  ?>
  <div id="youthqna_full_wrap">
    <h3>quiz online!!!!!!!!!!</h3>
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo site_url(); ?>/youthqna_online_quiz/" class="fb" target="_blank">facebook</a>
    <a  onclick="window.open('https://twitter.com/intent/tweet?original_referer=http%3A%2F%2Fblog.samsung.com%2F5562%2F&amp;text=<?php echo urlencode(html_entity_decode('아는만큼 보인다!경제경영, 과학기술, 인문사회, 문화예술 다양한 분야의 퀴즈를실제 풀어보고 전문가들의 해설을 듣는 라이브 퀴즈 콘서트 <청춘문답> '.site_url().'/youthqna_online_quiz/'));?>','twitter_share_dialog','width=626 height=436'); return false;" class="twitter" target="_blank">twitter</a>
    <div id="youthqna_quiz_wrap">
    <input id="youth_quiz_category" type="hidden" value="<?php echo $categoryId ?>">
  <?php
    $agent = $_SERVER['HTTP_USER_AGENT'];
    $ie8    = stripos($agent,"MSIE 8.0");
    if($ie8){
     $useragent_class = 'ie8';
    }
    if(count($quizzes)):
      $qindex = 1;
      foreach($quizzes as $quiz):quizId
  ?>  
    <!--quiz-->
    <div class="youth-quiz <?php if($qindex !==  1) echo'youth-hidden' ?>" id="youth_quiz_<?php echo $qindex ?>" quizIndex="<?php echo $qindex ?>" quizId="<?php echo $quiz->id ?>" userSelOp="">

      <div>
      Q<?php echo $qindex ?>
      <?php echo $quiz->question ?>
      </div>
      <div class="youth_quiz_answer">
        <ol class="youth_quiz_op" >
    <?php 
    $aindex = 1;
    foreach($answers as $a): 
      if($quiz->id === $a->q_id):
    ?>
        <li class="youthqna-op">
          <button class="youthqna-op-btn" value="<?php echo($a->id) ?>">
            <?php echo($aindex) ?>
            <?php echo($a->answer) ?>
          </button>
        </li>
    <?php
      $aindex++;
      endif;
    endforeach;
    ?>
        </ol>
      </div>
      <button class="youthqna-next-btn">NEXT</button>
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
    <div id="youthqna_result_wrap" class="youth-hidden">
      <div id="youthqna_result"></div>
      <button id="quiz_share_btn">공유 하기</button>
      <button id="join_event_btn">제출 하기</button>
      <button id="show_correct_answer_btn">정답 및 해설보기</button>
    </div>
    <!--quiz event-->
    <div id="youthqna_event_wrap" class="youth-hidden">
      <div class="form-contact">
          <label for="user_name" class="contact-label">이 름</label>
          <input type="text" name="user_name" id="user_name" value="" placeholder="이름을 입력해주세요">
      </div>
      <div class="form-contact">
          <label for="user_phone" class="contact-label">휴대폰 번호</label>
          <input type="number" name="user_phone" id="user_phone" value="" placeholder="'-'없이 입력해주세요" onkeypress='return event.charCode >= 48 && event.charCode <= 57'>
      </div>
      <div class="form-private">
        <label>개인정보 수집 및 이용 동의</label>
        <div class="form-agree">
            <input type="checkbox" id="checkbox-01" class="form-checkbox" name="private-allow-01" value="">
            <span></span>
            <label for="cb-private-allow">개인정보 수집 및 이용에 동의합니다</label>
        </div>
      </div>
      <button type="button" id="event-submit-btn">제출하기</button>
    </div>
    <!--quiz correct answer-->
    <div id="show_correct_answer_wrap" class="youth-hidden" >
      <?php
          $qindex = 1;
          foreach($quizzes as $quiz):
      ?>  
        <!--edit quiz-->
        <div id="youth_quiz_<?php echo $quiz->id ?>" >
          <div>
          Q<?php echo $qindex ?>
          <?php echo $quiz->question ?>
          </div>
          <div id="youth_quiz_answer_<?php echo $quiz->id ?>">

          </div>
        </div>
      <?php
        $qindex++;
        endforeach;
      ?> 
    </div>
    
    <button id="restart_btn" class="youth-hidden">다시 풀기</button>

  </div> 
  <!--youthqna_full_wrap-->
  <?php
}
/*
 * [퀴즈 페이지 shortcode]
 */
function youthqna_shortcode(){
  ob_start();
  youthqna_html_form_code();
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
        $correctIndex = i + 1;
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
 * [관리자 페이지에서 퀴즈를 추가하거나 수정합니다]
 */
function youthqna_change_quiz(){

  // echo('youthqna_change_quiz ok');
  $quiz_id = $_POST['quiz_id'];
  $category = $_POST['category'];
  $type = $_POST['type'];
  $type_link = $_POST['type_link'];
  $question = $_POST['question'];
  $answerCount = $_POST['answer-count'];
  $explanation = $_POST['explanation'];
  $answer = new ArrayObject();
  $answerId = new ArrayObject();
  $answerCheck = new ArrayObject();

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
  if($quiz_id === 'new'){
    $results = $wpdb->insert( 
      'youth_question', 
      array( 
        'c_id' => $category,  
        'type_id' => $type,
        'type_link' => $type_link, 
        'question' => $question,
        'explanation' => $explanation
      ), 
      array( 
        '%d', 
        '%d', 
        '%s', 
        '%s',
        '%s'
      ) 
    );
    $quiz_id = $wpdb->insert_id;
  }else{
    $results = $wpdb->update( 
      'youth_question', 
      array( 
        'c_id' => $category,  
        'type_id' => $type,
        'type_link' => $type_link, 
        'question' => $question,
        'explanation' => $explanation 
      ), 
      array( 'id' => $quiz_id ), 
      array( 
        '%d', 
        '%d', 
        '%s', 
        '%s',
        '%s' 
      ), 
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
  $sql = "CREATE TABLE IF NOT EXISTS youth_type (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(50) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 카테고리 테이블
  $sql0 = "CREATE TABLE IF NOT EXISTS youth_category (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(100) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 문제 테이블
  $sql1 = "CREATE TABLE IF NOT EXISTS youth_question (
    id int(11) NOT NULL AUTO_INCREMENT,
    c_id int(11) NOT NULL,
    type_id int(11) NOT NULL,
    type_link varchar(300) DEFAULT '' NOT NULL,
    question varchar(200) NOT NULL,
    points int(11) DEFAULT 20 NOT NULL,
    explanation varchar(300) NOT NULL,
    deleted_at datetime,
    PRIMARY KEY (id),
    FOREIGN KEY(c_id) REFERENCES youth_category(id) ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY(type_id) REFERENCES youth_type(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 답 테이블
  $sql2 = "CREATE TABLE IF NOT EXISTS youth_answer (
    id int(11) NOT NULL AUTO_INCREMENT,
    q_id int(11),
    answer varchar(200) NOT NULL,
    is_correct int(11) DEFAULT 0 NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY(q_id) REFERENCES youth_question(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 유저 이벤트 테이블
  $sql3 = "CREATE TABLE IF NOT EXISTS youth_event (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(50) NOT NULL,
    phone varchar(50) NOT NULL,
    c_id int(11) NOT NULL,
    score int(11) DEFAULT 0 NOT NULL,
    created_at datetime NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY(c_id) REFERENCES youth_category(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  //청춘문답 오프라인 카테고리 테이블
  $sql4 = "CREATE TABLE IF NOT EXISTS youth_offline_category (
    id int(11) NOT NULL AUTO_INCREMENT,
    name varchar(200) NOT NULL,
    PRIMARY KEY (id)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 오프라인 문제 테이블
  $sql5 = "CREATE TABLE IF NOT EXISTS youth_offline_question (
    id int(11) NOT NULL AUTO_INCREMENT,
    c_o_id int(11) NOT NULL,
    question varchar(200) NOT NULL,
    explanation varchar(300) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY(c_o_id) REFERENCES youth_offline_category(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
  //청춘문답 오프라인 답 테이블
  $sql6 = "CREATE TABLE IF NOT EXISTS youth_offline_answer (
    id int(11) NOT NULL AUTO_INCREMENT,
    q_o_id int(11),
    answer varchar(200) NOT NULL,
    is_correct int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    FOREIGN KEY(q_o_id) REFERENCES youth_offline_question(id) ON UPDATE CASCADE ON DELETE CASCADE
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

  //default type setting
  $sql7 = "INSERT INTO youth_type (`name`) VALUES ('Text'),('Picture'),('Video')";

  $wpdb->query($sql);
  $wpdb->query($sql0);
  $wpdb->query($sql1);
  $wpdb->query($sql2);
  $wpdb->query($sql3);
  $wpdb->query($sql4);
  $wpdb->query($sql5);
  $wpdb->query($sql6);
  $wpdb->query($sql7);
}

//자바스크립트 포함, Ajax처리
function youth_qna_admin_init(){

  wp_enqueue_script('jquery-ui-datepicker');
  wp_register_script('youth_qna_excel_export', plugins_url( '/js/admin.js' , __FILE__ ), array('jquery'));

  wp_localize_script('youth_qna_excel_export', 'youth_qna_excel_export',
    array(
        //엑셀 저장
        'save_excel'=>plugins_url().'/youth_qna/youth_qna_excel.php'
    ));
  wp_enqueue_script('youth_qna_excel_export');

  wp_enqueue_style( 'youth_qna_admin_css', plugins_url() . '/youth_qna/css/admin.css', '', '1.0');
  wp_enqueue_style( 'youth_qna_quiz_css', plugins_url() . '/youth_qna/css/quiz.css', '', '1.0');
  // wp_enqueue_script( 'youth_qna_admin', plugins_url() . '/youth_qna/js/admin.js', '', '1.0', true );
  wp_enqueue_script( 'youth_qna_quiz', plugins_url() . '/youth_qna/js/quiz.js', '', '1.0', true );

}
add_action('admin_init', 'youth_qna_admin_init');

/**
 * [css, js 파일을 세팅]
 */
function youth_qna_scripts() {

 
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