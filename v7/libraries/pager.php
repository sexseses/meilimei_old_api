<?php
class pager {
	private $config = array (
	//"first_btn_text" => "首页",
	"pre_btn_text" => "上一页",
	"next_btn_text" => "下一页",
	//"last_btn_text" => "末页",
	'base_url' =>'',
	"record_count" => 0,//每页分页尺寸
	"pager_size" => 10,//当前页码  *必需
	"pager_index" => 1,//每页显示的最大数量按钮
	"max_show_page_size" => 8,//页码在浏览器中传值的名称  默认为page
	"querystring_name" => "page",//URL是否重写 默认为flase
	"enable_urlrewriting" => false,//url重写规则 例如page/{page}  其中{page}就是代表页数
	"urlrewrite_pattern" => "",//分页容器的css名称
	"classname" => "pagelink ",//当前页按钮的class名称
	"current_btn_class" => "cpb",//分页文字描述span标签的css
	"span_text_class" => "stc",
		/*跳转的详细文本
		 *totle代表总页数,
		 *size代表每页数目
		 * goto代表要跳转的输入框
		 * record代表总记录数
		 * index代表当前的页码
		*/
		"jump_info_text" => '',//"共{totle}页，每页{size}条记录，跳转到{goto}页",
			//跳转按钮的文本
	"jump_btn_text" => "确定",
			//是否显示跳转
	"show_jump" => false,
			//是否展示前面的按钮  首页&上一页
	"show_front_btn" => true,
			//是否展示后面的按钮 下一页&末页
	"show_last_btn" => true
	);

	/*
	 * 类的构造函数
	 * $config:该分页类的配置
	 */

    public function init($config){
    	$this->init_config($config);
    }
	function __destruct() {
		unset ($this->config);
	}

	/*
	 * 构造分页主函数
	 */
	public function builder_pager() {
		$pager_arr = array ();
		$pager_size = $this->config["pager_size"];
		$pager_num = $this->config["record_count"] % $pager_size == 0 ? $this->config["record_count"] / $pager_size : floor($this->config["record_count"] / $pager_size) + 1;

		$pager_index = round($this->config["pager_index"]) == 0 ? 1 : round($this->config["pager_index"]);

		$pager_index = $pager_index >= $pager_num ? $pager_num : $pager_index;
		$pager_next = $pager_index >= $pager_num ? $pager_num : ($pager_index +1);
		$url = $this->get_url();
		$classname = $this->config["classname"];
		$pager_arr[] = "<div class=\"$classname\">\n";
		if ($this->config["show_front_btn"]) {
			$attr = $pager_index == 1 ? "disabled=disabled" : "";
			//$pager_arr[] = $this->get_a_html(self :: format_url($url, 1), $this->config["first_btn_text"], $attr);
		}
        $pager_arr[] = $this->get_a_html(self :: format_url($url, $pager_index -1), $this->config["pre_btn_text"], $attr);
		$current_pager_start = $pager_index % $pager_size == 0 ? ($pager_index / $pager_size -1) * $pager_size +1 : floor($pager_index / $pager_size) * $pager_size +1;

		$current_pager_end = ($current_pager_start + $pager_size -1) >= $pager_num ? $pager_num : ($current_pager_start + $pager_size -1);

        $mid = intval($this->config["max_show_page_size"]/2);
        $cstart = $pager_index>$mid?$pager_index-$mid:1;
        $cend = $pager_index>$mid?$pager_index+$mid:$this->config["max_show_page_size"];
        if($cend>$pager_num){
        	$cend = $pager_num;
        }
		for ($i = $cstart; $i <= $cend; $i++) {
			if ($i != $pager_index) {
				$pager_arr[] = $this->get_a_html(self :: format_url($url, $i), $i);

			} else {
				$pager_arr[] = $this->get_span_html($i, $this->config["current_btn_class"]);
			}
		}
       $pager_arr[] = $this->get_a_html(self :: format_url($url, $pager_next), $this->config["next_btn_text"], $attr);
		if ($this->config["show_last_btn"]) {
			$attr = $pager_index >= $pager_num ? "disabled=disabled" : "";
			//$pager_arr[] = $this->get_a_html(self :: format_url($url, $pager_num), $this->config["last_btn_text"], $attr);
		}

		if ($this->config["show_jump"]) {
			$patterns = array (
				"/\{totle\}/",
				"/\{size\}/",
				"/\{goto\}/",
				"/\{record\}/",
				"/\{index\}/",

			);
			$replacements = array (
				$pager_num,
				$pager_size,
				"<input type=\"input\" id=\"jumpNum\" style=\"width:20px;\" name=\"jump\" value=\"" . $pager_next . "\" />\n",
				$this->config["record_count"],
				$this->config["pager_index"]
			);
			$pager_arr[] = preg_replace($patterns, $replacements, $this->config["jump_info_text"]);
			$btn_text = $this->config['jump_btn_text'];
			$pager_arr[] = "<a href=\"javascript:void(0);\" style=\"float:none;\" onclick=\"javascript:jump();\">" . $this->config['jump_btn_text'] . "</a></span>\n";
			$pager_arr[] = $this->get_jumpscript($url);
		}
		$pager_arr[] = "</div>";
		$this->config["pager_index"] = $pager_index;
		return implode($pager_arr);
	}

	/*
	 *获取需要处理的url，支持重写配置，各种参数的url
	 */
	private function get_url() {
	    $url = '';//初始化
		$baseurl = $this->config["base_url"]==''?"http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]:$this->config["base_url"];
		if ($this->config["enable_urlrewriting"]) {
			$file_path = substr($baseurl, 0, strripos($baseurl, "/")) . "/";
			$url = $baseurl . $this->config["urlrewrite_pattern"];
		} else {
			$querystring_name = $this->config['querystring_name'];
			if (strpos($baseurl, "php?")) {
				$pattern = "/$querystring_name=[0-9]*/";
				if (preg_match($pattern, $baseurl)) {
					$url = preg_replace($pattern, "$querystring_name={page}", $url);
				} else {
					$url .= "&$querystring_name={page}";
				}
			} else {
				$url .= "?$querystring_name={page}";
			}
		}
		return $url;
	}

	/*
	 * 得到a标签的html
	 *$url:a标签所要导向的html
	 *$title:a标签的标题
	 **$attr:a标签上的附加属性 可以不写
	 */
	private static function get_a_html($url, $title, $attr = "") {
		return "<a href='$url' $attr style=\"margin-right:5px;\">$title</a>\n";
	}

	/*
	 * 获得span标签的html
	 * $num:span中的文本，即页序号
	 * $classname:span标签的class名称
	 */
	private static function get_span_html($num, $classname) {
		return "<span class=\"" . $classname . "\">$num</span>\n";
	}

	/*
	 * 格式化url
	 * $url 原url
	 * $page 页码
	 */
	private static function format_url($url, $page) {
		return preg_replace("/\{page\}$/", $page, $url);
	}

	/*
	 *初始化分页的配置文件
	 *如果在参数中不含该键值，则默认使用申明的值
	 */
	private function init_config($config) {
		//判断该值是否存在、是否是数组、是否含有记录
		if (isset ($config) && is_array($config) && count($config) > 0) {
			foreach ($config as $key => $val) {
				$this->config[$key] = $val;
			}
		}
	}

	/*
	 * 构造跳转功能脚本的方法
	 *$url:需要跳转的额那个url
	 */
	private function get_jumpscript($url) {
		$scriptstr = "<script type=\"text/javascript\">\n" .
		"function jump(){\n" .
		"var jnum=document.getElementById(\"jumpNum\").value;\n" .
		"if(isNaN(jnum)){\n" .
		"alert(\"在跳转框中请输入数字！\");\n" .
		"}\n" .
		"else{\n" .
		"var re=/\{page\}/\n" .
		"location.href='$url'.replace(re,jnum);\n" .
		"}\n" .
		"}\n" .
		"</script>\n";
		return $scriptstr;
	}

	/*
	 * php中构造类似.net中format方法的函数
	 * 用法:format("hello,{0},{1},{2}", 'x0','x1','x2')
	 */
	private function format() {
		$args = func_get_args();
		if (count($args) == 0) {
			return;
		}
		if (count($args) == 1) {
			return $args[0];
		}
		$str = array_shift($args);
		$str = preg_replace_callback('/\\{(0|[1-9]\\d*)\\}/', create_function('$match', '$args = ' . var_export($args, true) . '; return isset($args[$match[1]]) ? $args[$match[1]] : $match[0];'), $str);
        return $str;
	}

}
?>
