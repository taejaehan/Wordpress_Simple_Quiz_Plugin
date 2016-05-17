<?php

global $wpdb;
//$events = $wpdb->get_results( 'SELECT * FROM youth_event');
$events = $wpdb->get_results( 'SELECT youth_event.id, youth_event.name, youth_event.phone, youth_event.score, youth_event.created_at, youth_category.name as category_name FROM youth_event JOIN youth_category ON youth_event.c_id = youth_category.id');

?>
<div class="wrap">
  <h2>청춘문답 이벤트</h2>
  
    <div class="postbox-container" style="width:100%;">
    <table class="widefat">
      <thead>
      <tr>
        <th scope="col"><div style="text-align: center;"><?php _e('ID', 'YOUTH_QNA') ?></div></th>
        <th scope="col"><?php _e('카테고리', 'YOUTH_QNA') ?></th>
        <th scope="col"><?php _e('이름', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('휴대폰', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('맞춘갯수', 'YOUTH_QNA') ?></th>
        <th scope="col"><?php _e('생성날짜', 'YOUTH_QNA') ?></th>
      </tr>
      </thead>
      <tbody id="quiz-list">
    <?php
    if(count($events)):
      foreach($events as $event):
    ?>  
        <tr>
          <td>
            <?php echo $event->id ?>
          </td>
          <td>
            <?php echo $event->category_name ?>
          </td>
          <td>
            <?php echo $event->name ?>
          </td>
          <td>
            <?php echo $event->phone ?>
          </td>
          <td>
            <?php echo $event->score ?>
          </td>
          <td>
            <?php echo $event->created_at ?>
          </td>
        </tr>
    <?php endforeach;
      else:?>
      <tr>
        <td colspan="5"><?php _e('No events found.', 'YOUTH_QNA') ?></td>
      </tr>
    <?php endif;
      ?>
      </tbody>
      <button type="button" id="youth_event_excel">Excel Download</button>
    </table>
    </div>
</div>

