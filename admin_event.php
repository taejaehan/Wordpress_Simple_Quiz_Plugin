<?php

global $wpdb;
$events = $wpdb->get_results( 'SELECT * FROM youth_event');
?>
<div class="wrap">
  <h2>청춘문답 이벤트</h2>
  
    <div class="postbox-container" style="width:100%;">
    <table class="widefat">
      <thead>
      <tr>
        <th scope="col"><div style="text-align: center;"><?php _e('ID', 'YOUTH_QNA') ?></div></th>
        <th scope="col"><?php _e('이름', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('휴대폰', 'YOUTH_QNA') ?></th>       
        <th scope="col"><?php _e('카테고리', 'YOUTH_QNA') ?></th>
        <th scope="col"><?php _e('맞춘갯수', 'YOUTH_QNA') ?></th>
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
            <?php echo $event->name ?>
          </td>
          <td>
            <?php echo $event->phone ?>
          </td>
          <td>
            <?php echo $event->c_id ?>
          </td>
          <td>
            <?php echo $event->score ?>
          </td>
        </tr>
    <?php endforeach;
      else:?>
      <tr>
        <td colspan="5"><?php _e('No events found.', 'YOUTH_QNA') ?></td>
      </tr>
    <?php endif;
      ?>
      <button type="button" id="youth_event_excel">Excel</button>
      </tbody>
    </table>
    </div>
</div>

