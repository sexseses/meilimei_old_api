<?php
/**
 * Dropinn Email_model Class
 *
 * Email settings information in database.
 *
 * @package		Dropinn
 * @subpackage	Models
 * @category	Settings
 * @author		Cogzidel Product Team
 * @version		Version 1.5
 * @link		http://www.cogzidel.com

 */
	 class Email_model extends CI_Model
		{

	/**
	 * Constructor
	 *
	 */

	  function Email_model()
	  {
      parent::__construct();
   }//Controller End

	// --------------------------------------------------------------------

	/**
	 * Get Email settings from database
	 *
	 * @access	private
	 * @param	nil
	 * @return	array	payment settings informations in array format
	 */
	 function getEmailSettings($conditions=array())
	 {
	 	if(count($conditions)>0)
	 		$this->db->where($conditions);

	 $this->db->from('email_templates');
		$this->db->select('email_templates.id,email_templates.type,email_templates.title,email_templates.mail_subject,email_templates.email_body_text,email_templates.email_body_html');
		$result = $this->db->get();
		return $result;

	 }//End of getEmailSettings Function


	/**
	 * Add Email Settings
	 *
	 * @access	private
	 * @param	array	an associative array of insert values
	 * @return	void
	 */
	 function addEmailSettings($insertData=array())
	 {
	 	$this->db->insert('email_templates', $insertData);
		return;
	 }//End of getGroups Function
	 // --------------------------------------------------------------------

	 /**
	 * delete Email Settings
	 *
	 * @access	private
	 * @param	array	an associative array of insert values
	 * @return	void
	 */
	 function deleteEmailSettings($condition=array())
	 {
	    if(isset($condition) and count($condition) > 0)
			$this->db->where($condition);

	 	$this->db->delete('email_templates');
		return;
	 }//End of getGroups Function
	 //------------------------------------------------------------------------

	 /**
	 * Send Mail
	 *
	 * @access	private
	 * @param	array
	 * @return	array	site settings informations in array format
	 */
	function sendMail($to = '', $from_email = '', $from_name = '', $email_name = '', $splvars = array(), $cc = '', $bcc = '', $type = 'html')
	{
		// load Email Library
		$this->load->library('email');

		$mailer_type     = $this->db->get_where('email_settings', array('code' => 'MAILER_TYPE'))->row()->value;

		$smtp_port       = $this->db->get_where('email_settings', array('code' => 'SMTP_PORT'))->row()->value;

		$smtp_user       = $this->db->get_where('email_settings', array('code' => 'SMTP_USER'))->row()->value;

		$smtp_pass       = $this->db->get_where('email_settings', array('code' => 'SMTP_PASS'))->row()->value;

		$mailer_mode     = $this->db->get_where('email_settings', array('code' => 'MAILER_MODE'))->row()->value;

		$logo            = $this->db->get_where('settings',array('code' => 'SITE_LOGO'))->row()->string_value;
		$slogan          = $this->db->get_where('settings',array('code' => 'SITE_SLOGAN'))->row()->string_value;

		if($mailer_type == 2)
		{
		$config['protocol']  = 'smtp';
        $config['smtp_host'] = 'shawmail.vc.shawcable.net';
		$config['smtp_port'] = $smtp_port;
		$config['smtp_user'] = $smtp_user;
		$config['smtp_pass'] = $smtp_pass;
		}
		else if($mailer_type == 3)
		{
		$config['protocol']  = 'smtp';
        $config['smtp_host'] = 'ssl://smtp.googlemail.com';
		$config['smtp_port'] = $smtp_port;
		$config['smtp_user'] = $smtp_user;
		$config['smtp_pass'] = $smtp_pass;
		}

		$subject = '';
		$message = '';

		if($email_name != '')
		{
		$conditionUserMail = array('email_templates.type' => $email_name);
		$result            = $this->getEmailSettings($conditionUserMail);
		$rowUserMailConent = $result->row();

		$subject     = strtr($rowUserMailConent->mail_subject, $splvars);

					if($mailer_mode == 'html')
					{
					$config['mailtype'] = 'html';

					$message = '<table cellspacing="0" cellpadding="0" width="678" style="border:3px solid #dd2962; background:#fff; font-family:Arial, Helvetica, sans-serif;">
	            <tr> <td> <table background="'.base_url().'images/email/email_head.png" width="685" height="100" cellspacing="0" cellpadding="0">
						 <tr> 	<td style="width:130px; "> </td> <td style=" padding:0 10px 0 0; font-size:12px;vertical-align:bottom; padding-bottom:10px;color:#fff;"> </td> </tr> </table> </td> </tr>
			 <tr> <td style="padding:0 10px; font-size:14px;">';
					$message .= strtr($rowUserMailConent->email_body_html, $splvars);
					$message .= '</td></tr><tr><td><table cellpadding="0" cellspacing="0" width="685" height="108" style="background:#f6f6f6;"><tr>
							 <td style="font-size:13px; padding:6px 0 0 12px; color:#333333;line-height:22px">  </td>
							 </tr></table></td></tr></table>';
					}
					else
					{
					$config['mailtype'] = 'text';
					$message = strtr($rowUserMailConent->email_body_text, $splvars);
					}
			}

		$config['wordwrap'] = TRUE;

		$this->email->initialize($config);

		$this->email->to($to);
        $this->email->from($from_email,$from_name);
		$this->email->cc($cc);
		$this->email->bcc($bcc);
        $this->email->subject($subject);
        $this->email->message($message);

			if (!$this->email->send())
			{
			echo $this->email->print_debugger();
			}
			return true;

	} // Function sendmail End

	/**
	 * Update Email Settings
	 *
	 * @access	private
	 * @param	array	an associative array of insert values
	 * @return	void
	 */
	 function updateEmailSettings($id=0,$updateData=array())
	 {
	 	$this->db->where('id', $id);
	 	$this->db->update('email_templates', $updateData);

	 }//End of editGroup Function

	 function sendHtmlMail($to ='',$from ='',$subject='',$message='',$cc='')
	 {
		// load Email Library
		$this->load->library('email');

		$config['mailtype'] = 'html';
		$config['wordwrap'] = TRUE;

		$this->email->initialize($config);

		$this->email->to($to);
    		$this->email->from($from);
		$this->email->cc($cc);
   		$this->email->subject($subject);
    		$this->email->message($message);
		if ( ! $this->email->send())
                          {
		echo $this->email->print_debugger();
		}
	}


}
// End Email_model Class

/* End of file Email_model.php */
/* Location: ./app/models/Email_model.php */