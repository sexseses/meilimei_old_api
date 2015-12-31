<?php
class city {
	private $data = array (
		'北京',
		'哈尔滨',
		'呼和浩特',
		'吉林',
		'沈阳',
		'石家庄',
		'太原',
		'天津',
		'大连',
		'包头',
		'长春',
		'鞍山',
		'丹东',
		'上海',
		'福州',
		'杭州',
		'合肥',
		'济南',
		'南昌',
		'南京',
		'宁波',
		'青岛',
		'厦门',
		'苏州',
		'徐州',
		'温州',
		'台州',
		'金华',
		'广州',
		'深圳',
		'武汉',
		'长沙',
		'南宁',
		'郑州',
		'东莞',
		'佛山',
		'宜昌',
		'重庆',
		'成都',
		'贵阳',
		'昆明',
		'兰州',
		'乌鲁木齐',
		'西安',
		'西宁',
		'绵阳'
	);
	public function GCS($city='',$id=''){
		$html = '<select data-id="'.$id.'" class="hcity" name="hcity">';
		foreach($this->data as $r){
            $html.='<option '.($r==$id?'selected="selected"':'').' value="'.$r.'">'.$r.'</option>';
		}
		return $html.'</select>';
	}
}
?>
