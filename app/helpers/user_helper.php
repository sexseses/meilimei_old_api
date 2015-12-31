<?php
if (!defined('BASEPATH'))
	exit ('No direct script access allowed');

function get_user_by_id($id) {
	$CI = & get_instance();

	$query = $CI->db->get_where('users', array (
		'id' => $id
	));

	return $query->row();
}

function get_user_timezone() {
	$CI = & get_instance();

	$id = $CI->wen_auth->get_user_id();

	$query = $CI->db->get_where('users', array (
		'id' => $id
	));

	$timezone = $query->row()->timezone;

	if ($timezone == '')
		$timezone = 'UTC';

	return $timezone;
}

function get_user_timezoneL($id) {
	$CI = & get_instance();

	$query = $CI->db->get_where('users', array (
		'id' => $id
	));

	$timezone = $query->row()->timezone;

	if ($timezone == '')
		$timezone = 'UTC';

	return $timezone;
}

function get_list_by_id($id) {
	$CI = & get_instance();
	$query = $CI->db->get_where('list', array (
		'id' => $id
	), 1);
	if ($query->num_rows() > 0) {
		return $query->row();
	} else {
		return false;
	}
}

function getreviewoflist($userid = '', $listid = '') {
	$CI = & get_instance();
	$CI->load->model('Trips_model');
	$conditions = array (
		'list_id' => $listid,
		'userto' => $userid
	);
	$CI = & get_instance();

	$result = $CI->Trips_model->get_review($conditions);

	$conditions = array (
		'list_id' => $listid,
		'userto' => $userid
	);
	$stars = $CI->Trips_model->get_review_sum($conditions)->row();

	$overall = 0;
	if ($result->num_rows() > 0) {
		$accuracy = (($stars->accuracy * 2) * 10) / $result->num_rows();
		$cleanliness = (($stars->cleanliness * 2) * 10) / $result->num_rows();
		$communication = (($stars->communication * 2) * 10) / $result->num_rows();
		$checkin = (($stars->checkin * 2) * 10) / $result->num_rows();
		$location = (($stars->location * 2) * 10) / $result->num_rows();
		$value = (($stars->value * 2) * 10) / $result->num_rows();

		$overall = ($accuracy + $cleanliness + $communication + $checkin + $location + $value) / 6;

		$overall = round($overall, 2);
	}
	return $overall;
}

function getDaysInBetween($startdate, $enddate) {
	$period = (strtotime($enddate) - strtotime($startdate)) / (60 * 60 * 24);

	$dateinfo = get_gmt_time(strtotime($startdate));

	do {
		$days[] = $dateinfo;
		$dateinfo = date('m/d/Y', $dateinfo);
		$pre_dateinfo = date('m/d/Y', strtotime('+1 day', strtotime($dateinfo)));
		$dateinfo = get_gmt_time(strtotime($pre_dateinfo));
		$period--;
	} while ($period >= 0);

	return $days;
}

function getDaysInBetweenC($startdate, $enddate) {
	$period = (strtotime($enddate) - strtotime($startdate)) / (60 * 60 * 24);

	$dateinfo = $startdate;

	do {
		$days[] = $dateinfo;

		$dateinfo = date('m/d/Y', strtotime('+1 day', strtotime($dateinfo)));
		$period--;
	} while ($period >= 0);

	return $days;
}

function getListImage($list_id) {
	$CI = & get_instance();
	$condition = array (
		"is_featured" => 1
	);
	$list_image = $CI->Gallery->get_imagesG($list_id, $condition)->row();

	if (isset ($list_image->name)) {
		$url = base_url() . 'images/' .round($list_id/2000).'/'. $list_id . '/' . $list_image->name;
	} else {
		$url = base_url() . 'images/no_image.jpg';
	}

	return $url;
}

function get_userPayout($user_id = '') {

	$CI = & get_instance();

	$query = $CI->Common_model->getTableData('payout_preferences', array (
		'user_id' => $user_id,
		"is_default" => 1
	));

	return $query->row();
}
?>