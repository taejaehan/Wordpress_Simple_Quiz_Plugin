<?php
/*
꿀잼 관리자 페이지 (SNS Trackers)
*/
add_action('show_vote_state' ,'showVoteState' );
function showVoteTotal($type){
  global $wpdb;
  $result = $wpdb->get_results( 'SELECT * FROM gguljam_vote WHERE vote = '.$type, OBJECT );
  echo count($result);
}

function showVoteState(){
  global $wpdb;
  $result = $wpdb->get_results( 'SELECT * FROM gguljam_vote ', OBJECT );
  return $result;
}

class Gguljam_Tracking_List_Table extends WP_List_Table {
  function __construct(){
    global $status, $page;
    parent::__construct( array(
      'singular'  => 'tracking',     //singular name of the listed records
      'plural'    => 'trackings',    //plural name of the listed records
      'ajax'      => true        //does this table support ajax?
    ) );

  }

  function column_default($item, $column_name){
    switch($column_name){
      case 'id':
        return $item[$column_name];
        break;
      case 'facebook_id':
        return $item[$column_name];
        break;
      case 'time':
        return $item[$column_name];
        break;
      // case 'shared_num':
      //  return $item[$column_name];
      //  break;
      case 'day':return $item[$column_name];break;
      // case 'snsFacebook':return '<input type="text" id="fb-'.$item["id"].'" value="'.$item[$column_name].'" style="width:100%" />';break;
      // case 'snsTwitter':return '<input type="text" id="twitter-'.$item["id"].'" value="'.$item[$column_name].'"  style="width:100%" />';break;
      // case 'snsGplus':return '<input type="text" id="gplus-'.$item["id"].'" value="'.$item[$column_name].'" style="width:100%" />';break;
      // case 'participant':return $item[$column_name];break;
      // case 'total':return $item[$column_name];
      default:
        return print_r($item,true); //Show the whole array for troubleshooting purposes
    }
  }
  function column_cb($item){
    return sprintf(
      '<button class="csr_update" name="%1$s[]" value="%2$s" style="background:url(/wp-admin/images/yes.png) no-repeat;border:0px; width:20px; height:20px; cursor: pointer"  /></button>',
      /*$1%s*/ $this->_args['singular'],  //Let's simply repurpose the table's singular label ("movie")
      /*$2%s*/ $item['id']                //The value of the checkbox should be the record's id
    );
  }

  function get_columns(){
    $columns = array(
      'id'     => 'ID',
      'facebook_id'  => 'Facebook ID',
      'time'  => 'Date',
      // 'shared_num'  => 'Shared',
      // 'cb'        => '', //Render a checkbox instead of text
    );
    return $columns;
  }

  function get_sortable_columns() {
    $sortable_columns = array(

      'snsFacebook'    => array('snsFacebook',false),
      // 'snsTwitter'    => array('snsTwitter',false),
      // 'snsGplus'    => array('snsGplus',false),
      'participant'    => array('participant',false),
      'total'    => array('total',false)
    );
    return $sortable_columns;
  }

  function prepare_items() {
    global $wpdb; //This is used only if making any database queries

    $per_page = 10;
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    $data = $wpdb->get_results( "SELECT * FROM gguljam_tracker" , ARRAY_A);
    $i = 0;
    foreach($data as $line)
    {
      $res = 0;

      foreach($line as $index=>$val)
      {
        if( is_numeric($val) && $index!='id' )
          $res += $val;
      }
      $line['total']=$res;
      $data[$i] = $line;
      $i++;
    }
    function usort_reorder($a,$b){
      $orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'id'; //If no sort, default to title
      $order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'asc'; //If no order, default to asc
      //id일 경우 1 10 100 2 3 와 같이 정의되어 재정의 함 
      if($orderby === 'id'){
        $result = ($a['id'] > $b['id']) ? 1 : -1;
      }else{
        $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
      }
      return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
    }
    usort($data, 'usort_reorder');
    $current_page = $this->get_pagenum();
    $total_items = count($data);
    $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
    $this->items = $data;
    $this->set_pagination_args( array(
      'total_items' => $total_items,                  //WE have to calculate the total number of items
      'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
      'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
    ) );
  }

}


$testListTable = new Gguljam_Tracking_List_Table();
$testListTable->prepare_items();

global $wpdb;
$data = $wpdb->get_results( "SELECT vote FROM csr_config" , ARRAY_A);
$add = 0;
foreach($data as $line)
{
$add = $line['vote'];
}

function showTotalNumber(){
  global $wpdb;
  $result = $wpdb->get_results( 'SELECT * FROM gguljam_tracker ' , OBJECT );
  echo count($result);
}
?>

<div class="wrap">
  <div id="icon-users" class="icon32"><br/></div>
  <h2>꿀잼펜션 Facebook Tracking</h2>
  <br /><br />
    
    <div style="font-size: 20px">
      Total Count: <?php showTotalNumber() ?>
   </div>
  <!-- <label>Add </label><input  type="text" id="setCsrCount" value="<?php echo $add;?>" style="width: 60px"> participants from 0:00 to 6:00am. <button id="csr_config_update">submit</button> (max 2000)-->
  <?php $testListTable->display() ?> 
  
  <script>
   /* jQuery('.csr_update' ).on('click', function(){
      var id=jQuery(this).val();
      var data = {
        action: 'updateSns',
        id: jQuery(this).val(),
        snsFacebook: jQuery('#fb-'+id ).val(),
        // snsTwitter: jQuery('#twitter-'+id ).val(),
        // snsGplus: jQuery('#gplus-'+id  ).val()
      };

      jQuery.ajax({
        type: "POST",
        url: '/wp-admin/admin-ajax.php',
        data:data,
        complete: function(){
          alert('Saved !');
        },
        timeout: 60000
      });
    });


    jQuery('#csr_config_update' ).on('click', function(){

      var data = {
        action: 'setCsrCount',
        count: jQuery('#setCsrCount').val()
      };

      jQuery.ajax({
        type: "POST",
        url: '/wp-admin/admin-ajax.php',
        data:data,
        complete: function(){
          alert('Saved !');
        },
        timeout: 60000
      });
    });

  </script>
</div>

