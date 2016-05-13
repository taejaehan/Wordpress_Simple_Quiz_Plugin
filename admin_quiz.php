<?php

function youth_qna_quiz(){
  $action = 'new';
  if($_REQUEST['action'] == 'edit') $action = 'edit';
  
  echo($action);
}
?>
<div class="wrap">
  <h2>청춘문답 퀴즈 설정페이지</h2>
  
</div>  

