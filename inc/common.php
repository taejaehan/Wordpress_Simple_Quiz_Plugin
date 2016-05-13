<?php 
/**
 * post 유효성 검사
 * @param string $post_id
 * @param string $msg_name
 * @param string $post_type
 * @param string $not_null
 * @return Ambigous <multitype:, multitype:string >
 */
function request_get($post_id='', $msg_name='', $post_type='string', $not_null=FALSE){
	$rtn_array = array();
	// 앞뒤 공백 제거
	$post_data = trim($_REQUEST[$post_id]);
	// <script>제거
	$post_data = strip_tags($post_data);
	// 빈값 검사

	if($not_null && empty($post_data)){
		$rtn_array = array('target'=>$post_id, 'error'=>'1', 'data'=>'', 'msg'=>$msg_name.'(을)를 입력해야 합니다.');
	}else{
		switch($post_type){
			case 'string':
				$rtn_array = post_get_string($post_id, $post_data);
				break;
			case 'number':
				$rtn_array = post_get_number($post_id, $post_data);
				break;
			case 'phonenumber':
				$rtn_array = post_get_phonenumber($post_id, $post_data);
				break;
			case 'email':
				$rtn_array = post_get_email($post_id, $post_data);
				break;
			case 'ip':
				$rtn_array = post_get_ip($post_id, $post_data);
				break;
			default:
				$rtn_array = array('target'=>$post_id, 'error'=>'1', 'data'=>'', 'msg'=>'존재하지 않는 타입입니다');
		}
	}

	return $rtn_array;
}

/**
 *
 * @param string $post_id
 * @param string $post_data
 * @return multitype:string
 */
function post_get_string($post_id, $post_data){
	return array('target'=>$post_id, 'error'=>'0', 'data'=>(string)$post_data, 'msg'=>'');
}

/**
 * 숫자만 받기
 * @param string $post_data
 * @return multitype:string |multitype:string
 */
function post_get_number($post_id, $post_data){
	if($post_data == intval($post_data)){
		return array('target'=>$post_id, 'error'=>'0', 'data'=>$post_data, 'msg'=>'');
	}else{
		return array('target'=>$post_id, 'error'=>'1', 'data'=>'', 'msg'=>'숫자만 입력 가능합니다');
	}
}

/**
 * 이메일 받기
 * @param string $post_data
 * @return multitype:string |multitype:string
 */
function post_get_email($post_id, $post_data){
	// 유효성 검사
	if($post_data){
		if (filter_var($post_data, FILTER_VALIDATE_EMAIL)) {
			return array('target'=>$post_id, 'error'=>'0', 'data'=>$post_data, 'msg'=>'');
		}else{
			return array('target'=>$post_id, 'error'=>'1', 'data'=>'', 'msg'=>'올바르지 않은 이메일 주소입니다.');
		}
	}else{
		return array('target'=>$post_id, 'error'=>'0', 'data'=>'', 'msg'=>'');
	}
}

/**
 * 전화번호 받기(기호없음)
 * @param string $post_data
 * @return multitype:string mixed
 */
function post_get_phonenumber($post_id, $post_data){
	// 문자, 기호 제거
	return array('target'=>$post_id, 'error'=>'0', 'data'=>preg_replace("/[^0-9]*/s", "", $post_data), 'msg'=>'');
}

/**
 * ip주소 받기
 * @param string $post_data
 * @return multitype:string |multitype:string
 */
function post_get_ip($post_id, $post_data){
	if (filter_var($post_data, FILTER_VALIDATE_IP)) {
		return array('target'=>$post_id, 'error'=>'0', 'data'=>$post_data, 'msg'=>'');
	}else{
		return array('target'=>$post_id, 'error'=>'1', 'data'=>'', 'msg'=>'올바르지 않은 아이피 주소입니다.');
	}
}


/**
 * 페이징 클레스
 * @author Administrator
 *
 */
class paging{
	//--default obj--//
	private $page;					// 현재 페이지
	private $list;					// 화면에 보여질 게시물 갯수
	private $block;					// 하단에 보여질 페이징 갯수 블럭단위 [1]~[5]
	private $limit;					// 게시물 가져올 스타트 페이지
	private $total_row;				// 전체 게시물 row 수

	private $total_page;			// 전체 페이지 수
	private $total_block;			// 전체 블럭 갯수
	private $now_block;				// 현재 블럭
	private $start_page;			// 블럭 이동시 스타트 지점 객체
	private $end_page;				// 블럭의 끝 페이지
	private $is_next = false;		// 다음 페이지 이동을 위한 객체
	private $is_prev = false;		// 이전 페이지 이동을 위한 객체

	//--display(style) obj--//
	private $next_btn	= "[다음]";	// default 다음 이동 버튼
	private $prev_btn	= "[이전]";	// default 이전 이동 버튼
	private $end_btn	= "[끝]";	// default 끝 이동 버튼
	private $start_btn	= "[처음]";	// default 처음 이동 버튼
	private $display_class;			// <a 태그내의 class를 지정할 때
	private $display_id;			// <a 태그내의 id를 지정할 때
	private $display_mode = false;	// 기본 디스플레이 => [1]234 [다음], 풀모드=> [처음][이전][1]234[다음][끝]
	private $display_confirm = false;	// setDisplay 메서드 호출 확인값

	//--etc obj--/
	private $url_confirm = false;	// setUrl 메서드 호출 확인값
	private $html;					// 최종 결과물 리턴 객체


	public function paging($page=1, $list=10, $block=10, $total_row=0){	// --default init setting

		if(!$page)	$this->page = 1;
		else		$this->page = $page;
		$this->list = $list;
		$this->block = $block;
		$this->total_row = $total_row;
		$this->limit = ($this->page - 1) * $this->list;
		$this->url = $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] . "?";
		$this->setAuto();

	}

	public function getVar($name){

		if(gettype($this->$name) == "NULL"){
			echo "<script type=\"text/javascript\">alert('" . $name . " 객체는 없습니다.\\n얻고자 하시는 객체명을 확인해주세요.');</script>";
			return;
		}
		else	return $this->$name;

	}

	/* 사용하는 유저가 어쩔수 없이 객체의 값을 직접적으로 바꿔줘야 할 경우..아래 주석을 풀고 사용하면 됨
	 * 객체의 값을 마음대로 컨트롤 가능하나..이 메서드를 사용함으로 인해, 오류가 발생할 확률이 높다고 생각함
	public function setVar($name, $val){

	if(!is_numeric($val)){
	echo "<script type=\"text/javascript\">alert('setVar()메서드는 숫자만 허용합니다.');</script>";
	return;
	}
	else	$this->$name = $val;

	}*/

	private function setAuto(){

		$this->total_page = ceil($this->total_row / $this->list);
		$this->total_block = ceil($this->total_page / $this->block);
		$this->now_block = ceil($this->page / $this->block);

		$this->start_page = ($this->now_block - 1) * $this->block + 1;
		$this->end_page = $this->start_page + $this->block - 1;

		if($this->end_page > $this->total_page) $this->end_page = $this->total_page;
		if($this->now_block < $this->total_block) {
			$this->is_next = true;
		}
		if($this->now_block > 1) {
			$this->is_prev = true;
		}

	}

	public function setUrl($get=false){

		if($this->url_confirm == true){

			echo "<script type=\"text/javascript\">alert('setUrl 메서드는 showPage 메서드 이전에 셋팅하셔야 합니다.');</script>";
			return;

		}
		else if($get){

			$this->url = $this->url . $get ."&";
			$this->url_confirm = true;

		}
		else{

			echo "<script type=\"text/javascript\">alert('unknown error!!');</script>";

		}

	}

	public function setDisplay($name, $val=false){

		if($this->display_confirm == true){

			echo "<script type=\"text/javascript\">alert('setDisplay 메서드는 showPage 메서드 이전에 셋팅하셔야 합니다.');</script>";
			return;

		}
		switch($name){

			case "full"		:	$this->display_mode = true;
			break;

			case "class"	:	$this->display_class = " class=\"{$val}\"";
			break;

			case "id"		:	$this->display_id = " id=\"{$val}\"";
			break;

			case "next_btn"	:	$this->next_btn = $val;
			break;

			case "prev_btn"	:	$this->prev_btn = $val;
			break;

			case "end_btn"	:	$this->end_btn = $val;
			break;

			case "start_btn"	:	$this->start_btn = $val;
			break;

			default :	echo "<script type=\"text/javascript\">alert('[$name] is undefined Object!!');</script>";
			break;

		}

	}

	public function showPage(){

		//이 메서드를 호출하는 순간 setting은 할 수 없게 만듬
		$this->url_confirm = true;
		$this->display_confirm = true;

		/*
		if($this->display_mode && ($this->page != 1)){
			$this->html =  "<a href=\"http://{$this->url}page=1\">{$this->start_btn}</a> ";
		}
		if($this->is_prev){
			$go_prev = $this->start_page - 1;
			$this->html .=  "<a href=\"http://{$this->url}page=$go_prev\">{$this->prev_btn}</a> ";
		}

		for($i = $this->start_page; $i <= $this->end_page; $i++){
			if($i == $this->page)	$this->html .= "<span>$i</span>";
			else					$this->html .= " <a href=\"http://{$this->url}page=$i\"{$this->display_class}{$this->display_id}>{$i}</a> ";
		}

		if($this->is_next){
			$go_next = $this->start_page + $this->block;
			$this->html .= " <a href=\"http://{$this->url}page=$go_next\">{$this->next_btn}</a>";
		}
		if($this->display_mode && ($this->page != $this->total_page)){
			$this->html .= " <a href=\"http://{$this->url}page=$this->total_page\">{$this->end_btn}</a>";
		}
		*/
		
		if($this->display_mode && ($this->page != 1)){
			$this->html =  "<a href=\"#\" page-data=\"1\">{$this->start_btn}</a> ";
		}
		if($this->is_prev){
			$go_prev = $this->start_page - 1;
			$this->html .=  "<a href=\"#\" page-data=\"$go_prev\">{$this->prev_btn}</a> ";
		}
		
		for($i = $this->start_page; $i <= $this->end_page; $i++){
			if($i == $this->page)	$this->html .= "<span>$i</span>";
			else					$this->html .= " <a href=\"#\" page-data=\"$i\"{$this->display_class}{$this->display_id}>{$i}</a> ";
		}
		
		if($this->is_next){
			$go_next = $this->start_page + $this->block;
			$this->html .= " <a href=\"#\" page-data=\"$go_next\">{$this->next_btn}</a>";
		}
		if($this->display_mode && ($this->page != $this->total_page)){
			$this->html .= " <a href=\"#\" page-data=\"$this->total_page\">{$this->end_btn}</a>";
		}
			
		return $this->html;
	}

}
?>