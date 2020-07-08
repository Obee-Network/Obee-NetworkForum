<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2018 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
$response_data = array(
    'api_status' => 400
);

$required_fields =  array(
                        'create',
                        'edit',
                        'apply',
                        'search',
                        'get_apply'
                    );

$job_type = array('full_time','part_time','internship','volunteer','contract');
$salary_date = array('per_hour','per_day','per_week','per_month','per_year');
$question_type = array('free_text_question','yes_no_question','multiple_choice_question');

if (!empty($_POST['type']) && in_array($_POST['type'], $required_fields)) {
    if ($_POST['type'] == 'create') {
    	if (!empty($_POST['job_title']) && !empty($_POST['description']) && !empty($_POST['location']) && !empty($_POST['job_type']) && in_array($_POST['job_type'], $job_type) && !empty($_POST['category']) && in_array($_POST['category'], array_keys($wo['job_categories'])) && !empty($_POST['page_id'])) {

    		$page_data = $db->where('page_id',Wo_Secure($_POST['page_id']))->getOne(T_PAGES);

    		if (!empty($page_data) && $page_data->user_id == $wo['user']['id']) {

	    		$insert_array = array();

	    		if (!empty($_POST['job_title'])) {
	    			$insert_array['title'] = Wo_Secure($_POST['job_title']);
	    		}
	    		if (!empty($_POST['location'])) {
	    			$insert_array['location'] = Wo_Secure($_POST['location']);
	    		}
	    		if (!empty($_POST['lat'])) {
	    			$insert_array['lat'] = Wo_Secure($_POST['lat']);
	    		}
	    		if (!empty($_POST['lng'])) {
	    			$insert_array['lng'] = Wo_Secure($_POST['lng']);
	    		}
	    		if (!empty($_POST['minimum']) && is_numeric($_POST['minimum']) && $_POST['minimum'] > 0) {
	    			$insert_array['minimum'] = Wo_Secure($_POST['minimum']);
	    		}
	    		if (!empty($_POST['maximum']) && is_numeric($_POST['maximum']) && $_POST['maximum'] > 0) {
	    			$insert_array['maximum'] = Wo_Secure($_POST['maximum']);
	    		}
	    		if (!empty($_POST['salary_date']) && in_array($_POST['salary_date'], $salary_date)) {
	    			$insert_array['salary_date'] = Wo_Secure($_POST['salary_date']);
	    		}
	    		if (!empty($_POST['job_type'])) {
	    			$insert_array['job_type'] = Wo_Secure($_POST['job_type']);
	    		}
	    		if (!empty($_POST['category'])) {
	    			$insert_array['category'] = Wo_Secure($_POST['category']);
	    		}

                if (isset($_POST['currency'])) {
                    if (in_array($_POST['currency'], array_keys($wo['currencies']))) {
                        $insert_array['currency'] = Wo_Secure($_POST['currency']);
                    }
                }

	    		if (!empty($_POST['question_one'])) {
	    			if (!empty($_POST['question_one_type']) && in_array($_POST['question_one_type'], $question_type)) {
	    				if ($_POST['question_one_type'] != 'multiple_choice_question') {
	    					$insert_array['question_one'] = Wo_Secure($_POST['question_one']);
	    					$insert_array['question_one_type'] = Wo_Secure($_POST['question_one_type']);
	    				}
	    				elseif ($_POST['question_one_type'] == 'multiple_choice_question' && !empty($_POST['question_one_answers'])) {
	    					$insert_array['question_one'] = Wo_Secure($_POST['question_one']);
	    					$insert_array['question_one_type'] = Wo_Secure($_POST['question_one_type']);
	    					$answers = explode(',', $_POST['question_one_answers']);
	    					$answers = (object) $answers;
	    					$insert_array['question_one_answers'] = json_encode($answers);
	    				}
	    			}
	    		}

	    		if (!empty($_POST['question_two'])) {
	    			if (!empty($_POST['question_two_type']) && in_array($_POST['question_two_type'], $question_type)) {
	    				if ($_POST['question_two_type'] != 'multiple_choice_question') {
	    					$insert_array['question_two'] = Wo_Secure($_POST['question_two']);
	    					$insert_array['question_two_type'] = Wo_Secure($_POST['question_two_type']);
	    				}
	    				elseif ($_POST['question_two_type'] == 'multiple_choice_question' && !empty($_POST['question_two_answers'])) {
	    					$insert_array['question_two'] = Wo_Secure($_POST['question_two']);
	    					$insert_array['question_two_type'] = Wo_Secure($_POST['question_two_type']);
	    					$answers = explode(',', $_POST['question_two_answers']);
	    					$answers = (object) $answers;
	    					$insert_array['question_two_answers'] = json_encode($answers);
	    				}
	    			}
	    		}

	    		if (!empty($_POST['question_three'])) {
	    			if (!empty($_POST['question_three_type']) && in_array($_POST['question_three_type'], $question_type)) {
	    				if ($_POST['question_three_type'] != 'multiple_choice_question') {
	    					$insert_array['question_three'] = Wo_Secure($_POST['question_three']);
	    					$insert_array['question_three_type'] = Wo_Secure($_POST['question_three_type']);
	    				}
	    				elseif ($_POST['question_three_type'] == 'multiple_choice_question' && !empty($_POST['question_three_answers'])) {
	    					$insert_array['question_three'] = Wo_Secure($_POST['question_three']);
	    					$insert_array['question_three_type'] = Wo_Secure($_POST['question_three_type']);
	    					$answers = explode(',', $_POST['question_three_answers']);
	    					$answers = (object) $answers;
	    					$insert_array['question_three_answers'] = json_encode($answers);
	    				}
	    			}
	    		}

	    		if (!empty($_POST['description'])) {
	    			$insert_array['description'] = Wo_Secure($_POST['description']);
	    		}

	    		$insert_array['image'] = '';

	    		if (!empty($_POST['image_type']) && $_POST['image_type'] == 'cover') {
	    			$insert_array['image'] = $page_data->cover;
                    $insert_array['image_type'] = 'cover';
	    		}
	    		elseif (!empty($_POST['image_type']) && $_POST['image_type'] == 'upload' && !empty($_FILES['thumbnail'])) {
	    			$fileInfo      = array(
                        'file' => $_FILES["thumbnail"]["tmp_name"],
                        'name' => $_FILES['thumbnail']['name'],
                        'size' => $_FILES["thumbnail"]["size"],
                        'type' => $_FILES["thumbnail"]["type"],
                        'types' => 'jpeg,jpg,png,bmp'
                    );
                    $media         = Wo_ShareFile($fileInfo);
                    $insert_array['image'] = $media['filename'];
                    $insert_array['image_type'] = 'upload';
	    		}

	    		if (!empty($insert_array['image'])) {

	    			$insert_array['page_id'] = $page_data->page_id;
		    		$insert_array['user_id'] = $page_data->user_id;
		    		$insert_array['time'] = time();

		    		$job_id = $db->insert(T_JOB,$insert_array);

		    		$post_id = $db->insert(T_POSTS,array('page_id' => $page_data->page_id,
				    	                                 'postText' => $insert_array['title'],
				    	                                 'job_id' => $job_id,
                                                         'postType' => 'job'));
		    		$db->where('id',$post_id)->update(T_POSTS,array('post_id' => $post_id));

		    		if (!empty($post_id)) {
		    			$post = Wo_PostData($post_id);
		    			$response_data = array(
                                    'api_status' => 200,
                                    'data' => $post
                                );
		    		}
	    		}
	    		else{
	    			$error_code    = 7;
		            $error_message = 'Unable to upload a file: This file type is not supported.';
	    		}
	    	}
	    	else{
	    		$error_code    = 6;
	            $error_message = 'Page not found';
	    	}
    	}
    	else{
    		$error_code    = 5;
            $error_message = 'Please check your details.';
    	}
    }

    if ($_POST['type'] == 'edit') {
    	if (!empty($_POST['job_id']) && is_numeric($_POST['job_id']) && $_POST['job_id'] > 0) {
	        $job = Wo_GetJobById($_POST['job_id']);
	        if (!empty($job) && ($job['page']['is_page_onwer'] || $job['user_id'] == $wo['user']['id'])) {

	            $insert_array = array();

	            if (!empty($_POST['job_title'])) {
	                $insert_array['title'] = Wo_Secure($_POST['job_title']);
	            }
	            if (!empty($_POST['location'])) {
	                $insert_array['location'] = Wo_Secure($_POST['location']);
	            }
	            if (!empty($_POST['minimum']) && is_numeric($_POST['minimum']) && $_POST['minimum'] > 0) {
	                $insert_array['minimum'] = Wo_Secure($_POST['minimum']);
	            }
	            if (!empty($_POST['maximum']) && is_numeric($_POST['maximum']) && $_POST['maximum'] > 0) {
	                $insert_array['maximum'] = Wo_Secure($_POST['maximum']);
	            }
	            if (!empty($_POST['salary_date']) && in_array($_POST['salary_date'], $salary_date)) {
	                $insert_array['salary_date'] = Wo_Secure($_POST['salary_date']);
	            }
	            if (!empty($_POST['job_type'])) {
	                $insert_array['job_type'] = Wo_Secure($_POST['job_type']);
	            }
	            if (!empty($_POST['category'])) {
	                $insert_array['category'] = Wo_Secure($_POST['category']);
	            }
	            if (!empty($_POST['description'])) {
	                $insert_array['description'] = Wo_Secure($_POST['description']);
	            }

	            $db->where('id',$job['id'])->update(T_JOB,$insert_array);
	            $response_data = array(
			                        'api_status' => 200,
			                        'message_data' => 'job successfully edited'
			                    );
	        }
	        else{
	        	$error_code    = 9;
	            $error_message = 'job not found';
	        }
	    }
	    else{
	    	$error_code    = 8;
		    $error_message = 'job_id can not be empty';
	    }
    }

    if ($_POST['type'] == 'apply') {
    	if (!empty($_POST['job_id']) && is_numeric($_POST['job_id']) && $_POST['job_id'] > 0) {
	    	$job = Wo_GetJobById($_POST['job_id']);
	    	if (!empty($job) && !empty($_POST['user_name']) && !empty($_POST['phone_number']) && !empty($_POST['location']) && !empty($_POST['email']) && $job['apply'] == false) {
	    		$insert = true;
	    		$insert_data = array();

	    		if (!empty($job['question_one'])) {
	    			if ($job['question_one_type'] == 'yes_no_question' && !empty($_POST['question_one_answer']) && in_array($_POST['question_one_answer'], array('yes','no'))) {
	    				$insert_data['question_one_answer'] = Wo_Secure($_POST['question_one_answer']);
	    			}
	    			elseif ($job['question_one_type'] == 'multiple_choice_question' && in_array($_POST['question_one_answer'], array_keys($job['question_one_answers']))) {
	    				$insert_data['question_one_answer'] = Wo_Secure($_POST['question_one_answer']);
	    			}
	    			elseif ($job['question_one_type'] == 'free_text_question' && !empty($_POST['question_one_answer'])) {
	    				$insert_data['question_one_answer'] = Wo_Secure($_POST['question_one_answer']);
	    			}
	    			else{
	    				$insert = false;
	    			}
	    		}


	    		if (!empty($job['question_two'])) {
	    			if ($job['question_two_type'] == 'yes_no_question' && in_array($_POST['question_two_answer'], array('yes','no'))) {
	    				$insert_data['question_two_answer'] = Wo_Secure($_POST['question_two_answer']);
	    			}
	    			elseif ($job['question_two_type'] == 'multiple_choice_question' && in_array($_POST['question_two_answer'], array_keys($job['question_two_answers']))) {
	    				$insert_data['question_two_answer'] = Wo_Secure($_POST['question_two_answer']);
	    			}
	    			elseif ($job['question_two_type'] == 'free_text_question') {
	    				$insert_data['question_two_answer'] = Wo_Secure($_POST['question_two_answer']);
	    			}
	    			else{
	    				$insert = false;
	    			}
	    		}

	    		if (!empty($job['question_three'])) {
	    			if ($job['question_three_type'] == 'yes_no_question' && in_array($_POST['question_three_answer'], array('yes','no'))) {
	    				$insert_data['question_three_answer'] = Wo_Secure($_POST['question_three_answer']);
	    			}
	    			elseif ($job['question_three_type'] == 'multiple_choice_question' && in_array($_POST['question_three_answer'], array_keys($job['question_three_answers']))) {
	    				$insert_data['question_three_answer'] = Wo_Secure($_POST['question_three_answer']);
	    			}
	    			elseif ($job['question_three_type'] == 'free_text_question') {
	    				$insert_data['question_three_answer'] = Wo_Secure($_POST['question_three_answer']);
	    			}
	    			else{
	    				$insert = false;
	    			}
	    		}

	    		if ($insert == true) {
	    			$insert_data['user_name'] = Wo_Secure($_POST['user_name']);
	    			$insert_data['phone_number'] = Wo_Secure($_POST['phone_number']);
	    			$insert_data['location'] = Wo_Secure($_POST['location']);
	    			$insert_data['email'] = Wo_Secure($_POST['email']);
	    			$insert_data['job_id'] = Wo_Secure($_POST['job_id']);
	    			$insert_data['user_id'] = $wo['user']['id'];
	    			$insert_data['page_id'] = $job['page_id'];
	                $insert_data['time'] = time();

	    			if (!empty($_POST['position'])) {
	    				$insert_data['position'] = Wo_Secure($_POST['position']);
	    			}

	    			if (!empty($_POST['where_did_you_work'])) {
	    				$insert_data['where_did_you_work'] = Wo_Secure($_POST['where_did_you_work']);
	    			}

	    			if (!empty($_POST['experience_description'])) {
	    				$insert_data['experience_description'] = Wo_Secure($_POST['experience_description']);
	    			}
	                if (!empty($_POST['position']) && !empty($_POST['where_did_you_work']) && !empty($_POST['experience_start_date'])) {
	                    $insert_data['experience_start_date'] = Wo_Secure($_POST['experience_start_date']);
	                }
	                else{
	                    $insert_data['experience_start_date'] = '';
	                }

	    			if (!empty($_POST['i_currently_work']) && $_POST['i_currently_work'] == 'on') {
	    				$insert_data['experience_end_date'] = '';
	    			}
	    			else{
	                    if (!empty($_POST['position']) && !empty($_POST['where_did_you_work']) && !empty($_POST['experience_end_date'])) {
	                        $insert_data['experience_end_date'] = Wo_Secure($_POST['experience_end_date']);
	                    }
	                    else{
	                        $insert_data['experience_end_date'] = '';
	                    }
	    			}

	    			$db->insert(T_JOB_APPLY,$insert_data);

	                $notification_data_array = array(
	                    'recipient_id' => $job['page']['user_id'],
	                    'type' => 'apply_job',
	                    'url' => 'index.php?link1=timeline&u=' . $job['page']['page_name'].'&type=job_apply&id='.$insert_data['job_id']
	                );
	                Wo_RegisterNotification($notification_data_array);

	                $response_data = array(
			                        'api_status' => 200,
			                        'message_data' => 'Applied job successfully'
			                    );
	    		}
	    		else{
	    			if ($job['apply'] == true) {
	    				$error_code    = 11;
					    $error_message = 'You applied this job before';
	    			}
	    			else{
	    				$error_code    = 10;
					    $error_message = 'Please answer the question';
	    			}
	    		}
	    	}
	    	else{
	    		$error_code    = 5;
	            $error_message = 'Please check your details.';
	    	}
	    }
	    else{
	    	$error_code    = 8;
		    $error_message = 'job_id can not be empty';
	    }
    }

    if ($_POST['type'] == 'search') {
    	$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0 ? Wo_Secure($_POST['offset']) : 0);
        $limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);
        $array = array(
            'limit' => $limit
        );
        if (!empty($_POST['c_id'])) {
            $array['c_id'] = Wo_Secure($_POST['c_id']);
        }
        if (!empty($_POST['keyword'])) {
            $array['keyword'] = $_POST['keyword'];
        }
        if (!empty($_POST['length'])) {
            $array['length'] = $_POST['length'];
        }
        if (!empty($_POST['job_type'])) {
            $array['type'] = $_POST['job_type'];
        }
        if (!empty($offset)) {
            $array['after_id'] = $offset;
        }
        $result = Wo_GetAllJobs($array);
        $response_data = array(
                                    'api_status' => 200,
                                    'data' => $result
                                );
    }

    if ($_POST['type'] == 'get_apply') {
    	if (!empty($_POST['job_id']) && is_numeric($_POST['job_id']) && $_POST['job_id'] > 0) {

    		$offset = (!empty($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0 ? Wo_Secure($_POST['offset']) : 0);
	        $limit = (!empty($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0 && $_POST['limit'] <= 50 ? Wo_Secure($_POST['limit']) : 20);
	        $job_id = Wo_Secure($_POST['job_id']);

	        $job_apply = Wo_GetApplyJob(array('job_id' => $job_id,'offset' => $offset,'limit' => $limit));
	        foreach ($job_apply as $key => $value) {
	        	foreach ($non_allowed as $key4 => $value4) {
                  unset($job_apply[$key]['user_data'][$value4]);
                }
	        }

	        $response_data = array(
                                    'api_status' => 200,
                                    'data' => $job_apply
                                );
    	}
    	else{
	    	$error_code    = 8;
		    $error_message = 'job_id can not be empty';
	    }
    }


}
else{
    $error_code    = 4;
    $error_message = 'type can not be empty';
}