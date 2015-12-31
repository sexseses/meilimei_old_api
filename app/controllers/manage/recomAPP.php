<?php
class recomAPP extends CI_Controller {
	private $notlogin = true, $uid = '';
	public function __construct() {
		parent :: __construct();
		if ($this->wen_auth->is_logged_in()) {
			$this->notlogin = false;
			$this->uid = $this->wen_auth->get_user_id();
		}else{
			redirect('');
		}
		$this->load->model('privilege');
		$this->privilege->init($this->uid);
       if(!$this->privilege->judge('recomAPP')){
          die('Not Allow');
       }
	}

    /**
     * 列表
     */
    public function index() {

        $where = ' 1 ';
        if($keywords  = trim($this->input->get("keywords"))){
              $where .=" and (name like '%{$keywords}%' or download like '%{$keywords}%' or content like '%{$keywords}%') ";
        }

        $result = $this->db->query("select * from recom_app where {$where} order by ctime desc")->result_array();

        $data['result'] = $result;
		$data['notlogin'] = $this->notlogin;
        $data['message_element'] = "recomAPP";
		$this->load->view('manage', $data);
	}

    /**
     * 增加、修改
     */
    public function edit(){
        if($this->input->post()){
            if($this->input->post('id')){   //修改
                if($_FILES['picture']['size']>0){
                    //上传图片
                    $newPath = '/upload/'.uniqid().'.'.array_pop(explode('.',$_FILES['picture']['name']));
                    move_uploaded_file($_FILES['picture']['tmp_name'],'.'.$newPath);
                    $insert['picture'] = $newPath;
                }

                $insert = array();
                $insert['name'] = $_POST['name'];
                $insert['content'] = $_POST['content'];
                $insert['download'] = $_POST['download'];
                $this->common->updateTableData("recom_app",$this->input->post('id'),'',$insert);

                $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '修改成功！'));
                redirect("manage/recomAPP/index",'reflesh');
                die;

            }else{    //增加
                if($_FILES['picture']['size']==0){
                    echo "请选择图片文件";
                    die;
                }
                //上传图片
                $newPath = '/upload/'.uniqid().'.'.array_pop(explode('.',$_FILES['picture']['name']));
                move_uploaded_file($_FILES['picture']['tmp_name'],'.'.$newPath);
                $insert = array();
                $insert['name'] = $_POST['name'];
                $insert['picture'] = $newPath;
                $insert['content'] = $_POST['content'];
                $insert['download'] = $_POST['download'];
                $insert['ctime'] = time();
                $newId = $this->common->insertData("recom_app",$insert);
                if($newId){
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '添加成功！'));
                    redirect("manage/recomAPP/index",'reflesh');
                    die;
                }else{
                    $this->session->set_flashdata('flash_message', $this->common->flash_message('error', '添加失败！'));
                    redirect("manage/recomAPP/edit",'reflesh');
                    die;
                }
            }


        }
        if($id = $this->input->get('id')){
            $row = $this->db->query('select * from recom_app where id = '.$id)->row_array();
            $data['row'] = $row;
        }
        $data['notlogin'] = $this->notlogin;
        $data['message_element'] = "recomAPP_edit";
        $this->load->view('manage', $data);
    }

    /**
     * 删除
     */
    public function del(){
        $id = $this->input->get('id');
        $condition = array (
            'id' => $id
        );
        $this->common->deleteTableData('recom_app', $condition);
        $this->session->set_flashdata('flash_message', $this->common->flash_message('success', '已成功删除！'));
        redirect('manage/recomAPP', 'refresh');
    }
}
?>
