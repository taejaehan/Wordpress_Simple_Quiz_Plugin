
<?php

$url = $_SERVER['REQUEST_URI'];
$parts = parse_url($url);
parse_str($parts['query'], $query);
// echo $query['category'];
$category = intval($query['category']);
if($category === 0 || $category === '' || $category === null){
  $category = 1;
}
$QUIZ_LIMIT = 5;
$ANSWER_LIMIT = 5;
global $wpdb;
$quizzes = $wpdb->get_results( 'SELECT * FROM youth_question WHERE deleted_at is NULL AND c_id='.$category);
$categories = $wpdb->get_results( 'SELECT * FROM youth_category ');
$types = $wpdb->get_results( 'SELECT * FROM youth_type ');
$answers = $wpdb->get_results( 'SELECT * FROM youth_answer ');

// $bannerTypes = $wpdb->get_results( 'SELECT * FROM youth_banner_type');
// $banners = $wpdb->get_results( 'SELECT * FROM youth_banner WHERE c_id='.$category);


$banners = $wpdb->get_results('SELECT *, youth_banner_type.id as t_id, youth_banner.id as b_id FROM youth_banner_type LEFT JOIN youth_banner ON youth_banner_type.id = youth_banner.t_id AND youth_banner.c_id='.$category);

function youth_qna_exam(){
  $action = 'new';
  if($_REQUEST['action'] == 'edit') $action = 'edit';
}
?>
<div class="wrap">
  <h2>청춘문답 퀴즈</h2>
  </br></br>
  
    <div class="postbox-container" style="width:100%;">
    <select id="category_sel" name="category">
      <?php foreach($categories as $c): ?>
      <option value="<?php echo $c->id?>" <?php if($category ===  intval($c->id)) echo 'selected'?>>
      <?php echo $c->name?>
      </option>
      <?php endforeach; ?>
    </select>
    <span >SHORTCODE : <input style="width:200px" readonly value="[youthqna_online_quiz <?php echo($category) ?>]"></input></span>
    <span style="float:right">
      <input  id="category_add_name" type="name" value="">
      <button id="category_add_btn">카테고리 추가하기!!</button>
    </span>
    
    <h3>청춘문답 배너</h3>
    <table class="widefat">
      <thead>
      <tr>
        <th scope="col"><div style="text-align: center;"><?php _e('배너타입ID', 'YOUTH_QNA') ?></div></th>
        <th scope="col"><?php _e('배너위치', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('배너링크', 'YOUTH_QNA') ?></th>
        <th scope="col"><?php _e('숨김', 'YOUTH_QNA') ?></th>
        <th scope="col"><?php _e('Action', 'YOUTH_QNA') ?></th>
      </tr>
      </thead>
      <tbody id="banner-list">
    <?php
    if(count($banners)):
      foreach($banners as $banner):
    ?>  
        <tr>
          <form class='banner-form' method='post' action='/wp-admin/admin-ajax.php'>
          <input name="action" type="hidden" value="youthqna_change_banner" />
          <input name="banner_id" type="hidden" value="<?php echo($banner->b_id) ?>" />
          <input name="banner_type_id" type="hidden" value="<?php echo($banner->t_id) ?>" />
          <input name="category" type="hidden" value="<?php echo($category) ?>" />
          <td>
            <?php echo $banner->t_id;?>
          </td>
          <td>
            <?php echo $banner->name;?>
          </td>
          <td>
            <p>
            <?php 

            $strLink = explode('/',($banner->banner_link));
            $count = count($strLink);
            $fileName = $strLink[$count-1];
            echo($fileName) 
            ?>
            </p>
            <input name="banner_link" type="file" isDirty="FALSE" />
          </td>
          <td>
          <input class="banner-checkbox-input" name="is_hidden" type="checkbox" value="<?php if($banner->is_hidden): echo '1'; else: echo '0'; endif;?>" <?php if($banner->is_hidden) echo 'checked';?> <?php if(intval($banner->t_id) !== 1) echo 'readonly' ;?>>
          </td>
          <td>
            <div><button type="submit" class="banner-save-btn">Save</button></div>
          </td>
          </form>
        </tr>
    <?php 
      endforeach;
    else:
    ?>
      <tr>
        <td colspan="5"><?php _e('No Quizzes found.', 'YOUTH_QNA') ?></td>
      </tr>
    <?php 
    endif;
    ?>
      </tbody>
    </table>
    </br>
    <h3>청춘문답 퀴즈/답 리스트</h3>
    <table class="widefat">
      <thead>
      <tr>
        <th scope="col"><div style="text-align: center;"><?php _e('ID', 'YOUTH_QNA') ?></div></th>
        <th scope="col"><?php _e('타입', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('타입링크', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('문제', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('답변', 'YOUTH_QNA') ?></th>
        <th scope="col"><?php _e('해설', 'YOUTH_QNA') ?></th>
        <th scope="col"><?php _e('Action', 'YOUTH_QNA') ?></th>
      </tr>
      </thead>
      <tbody id="quiz-list">
    <?php
    if(count($quizzes)):
      foreach($quizzes as $quiz):
    ?>  
    <!--edit quiz-->
        <tr>
          <form class='quiz-form' method='post' action='/wp-admin/admin-ajax.php'>
          <input name="action" type="hidden" value="youthqna_change_quiz" />
          <input name="quiz_id" type="hidden" value="<?php echo($quiz->id) ?>" />
          <input name="category" type="hidden" value="<?php echo($category) ?>" />
          <td><?php echo $quiz->id ?></td>
          <td>
            <select class="quiz-type-selctor" name="type">
              <?php foreach($types as $t): ?>
              <option value="<?php echo $t->id?>" <?php if($quiz->type_id ===  $t->id) echo 'selected'?>>
                <?php echo $t->name?>
              </option>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
          <?php
            $q_type_id = intval($quiz->type_id);
            if( $q_type_id === 1):
              //text
          ?>
            <input name="type_link" value="empty" readonly/>
          <?php
            elseif($q_type_id === 2):
              //picture
          ?>
            <p>
            <?php 

            $strLink = explode('/',($quiz->type_link));
            $count = count($strLink);
            $fileName = $strLink[$count-1];
            echo($fileName) 
            ?>
            </p>
            <input name="type_link_file" type="file" isDirty="FALSE" />
            <!-- <input name="type_link" type="hidden" value="" /> -->
          <?php
            else:
              //video
          ?>
            <input name="type_link" type="name" value="<?php echo($quiz->type_link) ?>" />
          <?php
            endif;
          ?>
          </td>
          <td>
            <input name="question" type="name" value="<?php echo($quiz->question) ?>" />
          </td>
          <td>
            <?php 
            $index = 0;
            foreach($answers as $a): 
              if($quiz->id === $a->q_id):
                
            ?>
              <div>
              <input name="answer-id-<?php echo($index) ?>" type="hidden" value="<?php echo($a->id) ?>">
              <span><input name="answer-text-<?php echo($index) ?>" type="text" value="<?php echo($a->answer) ?>"></span>
              <input class="answer-checkbox-input" name="answer-checkbox-<?php echo($index) ?>" type="checkbox" value="<?php echo($a->id) ?>" <?php if($a->is_correct === '1') echo 'checked'?>>
              </div>
            <?php 
              $index++;
              endif;
            endforeach;
            //answer 5개 한정 
            for($index; $index < $QUIZ_LIMIT; $index++):
            ?>
              <div>
              <input name="answer-id-<?php echo($index) ?>" type="hidden" value="new">
              <span><input name="answer-text-<?php echo($index) ?>" type="text" value=""></span>
              <input class="answer-checkbox-input" name="answer-checkbox-<?php echo($index) ?>" type="checkbox" value="new">
              </div>
            <?php 
            endfor; 
            ?>
            <input name="answer-count" type="hidden" value="<?php echo($index) ?>">
          </td>
          <td>
            <input name="explanation" type="name" value="<?php echo($quiz->explanation) ?>">
          </td>
          <td>
            <div><button type="submit" class="quiz-save-btn">Save</button></div>
            <div><button style="margin-top:10px"qId="<?php echo($quiz->id) ?>" type="button" class="quiz-del-btn">Delete</button></div>
          </td>
          </form>
        </tr>
    <?php endforeach;
      else:?>
      <tr>
        <td colspan="5"><?php _e('No Quizzes found.', 'YOUTH_QNA') ?></td>
      </tr>
    <?php endif;
      if(count($quizzes) < $ANSWER_LIMIT):
    ?>
      <!--new quiz 5개 한정-->

      <tr>
          <form class='quiz-form' method='post' action='/wp-admin/admin-ajax.php'>
          <input name="action" type="hidden" value="youthqna_change_quiz" />
          <input name="quiz_id" type="hidden" value="new" />
          <input name="category" type="hidden" value="<?php echo($category) ?>" />
          <td>New</td>
          <td>
            <select name="type">
              <?php foreach($types as $t): ?>
              <option value="<?php echo $t->id?>" >
                <?php echo $t->name?>
              </option>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
            <input name="type_link" type="name" value="">
          </td>
          <td>
            <input name="question" type="name" value="">
          </td>
          <td>
            <?php 
            for($index = 0; $index < 4; $index++):
            ?>
              <div>
              <input name="answer-id-<?php echo($index) ?>" type="hidden" value="new">
              <span><input name="answer-text-<?php echo($index) ?>" type="text" value=""></span>
              <input class="answer-checkbox-input" name="answer-checkbox-<?php echo($index) ?>" type="checkbox" value="new">
              </div>
            <?php 
            endfor; 
            ?>
            <input name="answer-count" type="hidden" value="<?php echo($index) ?>">
          </td>
          <td>
            <input name="explanation" type="name" value="">
          </td>
          <td>
            <div><button type="submit" class="quiz-save-btn">Save</button></div>
          </td>
          </form>
        </tr>
      <?php
      endif;
      ?>
      <!-- <button type="button" id="youth_event_excel">Excel</button> -->
      </tbody>
    </table>
    </div>
</div>

