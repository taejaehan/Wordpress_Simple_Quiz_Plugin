
<?php

$url = $_SERVER['REQUEST_URI'];
$parts = parse_url($url);
parse_str($parts['query'], $query);
// echo $query['category'];
$category = intval($query['category']);
if($category === 0 || $category === '' || $category === null){
  $category = 1;
}
global $wpdb;
$quizzes = $wpdb->get_results( 'SELECT * FROM youth_question WHERE deleted_at is NULL AND c_id='.$category);
$categories = $wpdb->get_results( 'SELECT * FROM youth_category ');
$types = $wpdb->get_results( 'SELECT * FROM youth_type ');
$answers = $wpdb->get_results( 'SELECT * FROM youth_answer ');

function youth_qna_exam(){
  $action = 'new';
  if($_REQUEST['action'] == 'edit') $action = 'edit';
}
?>
<div class="wrap">
  <h2>청춘문답 퀴즈</h2>
  
    <div class="postbox-container" style="width:100%;">
    <select id="category_sel" name="category">
      <?php foreach($categories as $c): ?>
      <option value="<?php echo $c->id?>" <?php if($category ===  intval($c->id)) echo 'selected'?>>
      <?php echo $c->name?>
      </option>
      <?php endforeach; ?>
    </select>
    <input id="category_add_name" type="name" value="">
    <button id="category_add_btn">카테고리 추가하기</button>
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
            <select name="type">
              <?php foreach($types as $t): ?>
              <option value="<?php echo $t->id?>" <?php if($quiz->type_id ===  $t->id) echo 'selected'?>>
                <?php echo $t->name?>
              </option>
              <?php endforeach; ?>
            </select>
          </td>
          <td>
            <input name="type_link" type="name" value="<?php echo($quiz->type_link) ?>">
          </td>
          <td>
            <input name="question" type="name" value="<?php echo($quiz->question) ?>">
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
              <input name="answer-checkbox-<?php echo($index) ?>" type="checkbox" value="<?php echo($a->id) ?>" <?php if($a->is_correct === '1') echo 'checked'?>>
              </div>
            <?php 
              $index++;
              endif;
            endforeach;
            for($index; $index < 4; $index++):
            ?>
              <div>
              <input name="answer-id-<?php echo($index) ?>" type="hidden" value="new">
              <span><input name="answer-text-<?php echo($index) ?>" type="text" value=""></span>
              <input name="answer-checkbox-<?php echo($index) ?>" type="checkbox" value="new">
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
      if(count($quizzes) < 5):
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
              <input name="answer-checkbox-<?php echo($index) ?>" type="checkbox" value="new">
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

