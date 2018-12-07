<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // security check, don't load file outside WP
}


/**
 * FundaWande Coaching Class
 *
 * All functionality pertaining to the coaching functionality of FundaWande.
 *
 * @package Core
 * @author Pango
 *
 * @since 1.0.00
 */
class FundaWande_Coaching {



    /**
     * Constructor
     */
    public function __construct() {



    }

    /**
     * Update assessments set in the review assessment area.
     *
     * @return boolean true or false.
     */
    function update_assessment($values) {


            if ($values['task'] == 'update_assessment') {
                $user_id = $values['user_id'];
                $lesson_id = $values['lesson_id'];
                $quiz_id = $values['quiz_id'];
                $comment_id = $values['comment_id'];
                $questions = $values['questions'];
                $response = $values['response_feedback'];

                // If assessment has been set to complete then mark as graded, ungraded otherwise
                $assessment_status = $values['assessment_complete'] ? 'graded' : 'ungraded';

                // Update assessment status
                $comment = array(
                    'comment_ID' => $comment_id,
                    'comment_approved' => $assessment_status,
                );
                wp_update_comment($comment);

                $assessment_comment = get_comment($comment_id);
                $learner_id = $assessment_comment->user_id;
                $learner = get_userdata($learner_id);

                // get the assessment title
                $assessment_title = get_the_title($lesson_id);
                $assessment_link = get_the_permalink($lesson_id);
                // error_log('learnerid-' . $learner_id);

                if (!empty($response)) {
                    $time = current_time('mysql');
                    $user = get_userdata(get_current_user_id());
                    $data = array(
                        'comment_post_ID' => $lesson_id,
                        'comment_type' => 'sus_feedback_resp',
                        'user_id' => $user->ID,
                        'comment_date' => $time,
                        'comment_content' => $response,
                        'comment_approved' => '1',
                        'comment_author' => $user->display_name,
                        'comment_author_email' => $user->user_email,
                        'comment_parent' => $comment_id,
                    );

                    $comment_id = wp_insert_comment($data);


                    // $send the entrep an email letting them know the coach responded

                    $message = sprintf(__('Hi %s,'), $learner->first_name) . "<br><br>";
                    $message .= __('Your coach responded to you!') . "<br><br>";
                    $message .= sprintf(__('<b>assessment:</b> <a href="%s">%s</a> '), $assessment_link, $assessment_title) . "<br><br>";
                    $message .= __('Click the link above to go to the assessment to view the response (you need to be logged in).') . "<br><br>";
                    $message .= __('Regards,') . "<br>";
                    $message .= __('Startup School Bot') . "<br><br>";

                    wp_mail($learner->user_email, sprintf(__('SUS assessment response: Your coach has responsed to you.')), $message);

                }

                // If assessment responses are closed set to true or false
                if(isset($values['responses_closed'])) {
                    $assessment_responses_closed = $values['responses_closed'] ? true : false;


                    // update the assessment response status
                    $responses_closed = update_comment_meta($comment_id, 'responses_closed', $assessment_responses_closed);
                }
                if(isset($values['feedback_complete'])) {
                    // If assessment feedback is being released set to true or false
                    $assessment_feedback = $values['feedback_complete'] ? true : false;

                    $already_had_feedback = get_comment_meta($comment_id, 'quiz_has_feedback', true);

                    if (!$already_had_feedback && $assessment_feedback) {

                        // $send the teacher an email letting them know the coach released feedback

                        $message = sprintf(__('Hi %s,'), $learner->first_name) . "<br><br>";
                        $message .= __('Your coach has given you feedback!') . "<br><br>";
                        $message .= sprintf(__('<b>assessment:</b> <a href="%s">%s</a> '), $assessment_link, $assessment_title) . "<br><br>";
                        $message .= __('Click the link above to go to the assessment to view the feedback (you need to be logged in).') . "<br><br>";
                        $message .= __('Regards,') . "<br>";
                        $message .= __('Startup School Bot') . "<br><br>";

                        wp_mail($learner->user_email, sprintf(__('SUS assessment feedback: Your coach has given you feedback.')), $message);

                    }

                    // update the comment post meta for feedback boolean
                    $has_feedback = update_comment_meta($comment_id, 'quiz_has_feedback', $assessment_feedback);
                }

                // Set arrays for feedback, both audio and written
                $all_answers_feedback = array();
                $all_answers_audio_feedback = array();

                // loop through questions
                foreach ($questions as $question) {
                    $question_feedback = '';
                    // if written feedback is set, unslash
                    if (isset($question['feedback'])) {
                        $question_feedback = wp_unslash($question['feedback']);

                    }
                    // assign both arrays
                    $all_answers_feedback[$question['question_id']] = $question_feedback;
                    $all_answers_audio_feedback[$question['question_id']] = $question['audio_feedback'];

                }

                // Encode the data for the feedback as it is potentially long
                $encoded_answers_feedback = array();
                foreach ($all_answers_feedback as $question_id => $feedback) {
                    $encoded_answers_feedback[$question_id] = base64_encode($feedback);
                }

                // save the user data for feedback - audio and written
                $feedback_saved = update_comment_meta($comment_id, 'quiz_answers_feedback', $encoded_answers_feedback);
                $audio_feedback_saved = update_comment_meta($comment_id, 'quiz_answers_audio_feedback', $all_answers_audio_feedback);

                // Were the the question feedback save correctly?
                // This is done by Sensei so it has to be done here.
                if (intval($feedback_saved) > 0) {
                    // save transient to make retrieval faster in future
                    $transient_key = 'sensei_answers_feedback_' . $user_id . '_' . $lesson_id;
                    set_transient($transient_key, $encoded_answers_feedback, 10 * DAY_IN_SECONDS);

                }
            }

    }

    /**
     * Get all the teacher assessments.
     *
     * @param string $presentation the presentation metakey to determine which presentation to get assessments for.
     *
     * @return array $teacher_assessments Array containing all required info on teacher assessments for coaches.
     */
    public function get_teacher_assessments($course, $coach = false, $module_id , $user_id = null) {

        if ($user_id) {
            $teachers = array();
            $teachers[] = get_user_by('ID',$user_id);
        } else {

            // Set up the $teacher arguments to collect any entreps in the relevant presentation and coach
            if ($coach) {
                $teacher_args = array(
                    'meta_query' => array(
                        'relation' => 'AND',
                        array(
                            'key' => 'fw_coach',
                            'value' => $coach,
                            'compare' => '='
                        )
                    ),
                    // Commented out but keeping incase it's useful
                    // 'role__in' => ['teacher', 'alumni']
                );
            } else {
                $teacher_args = array(
                    // Commented out but keeping incase it's useful
                    // 'meta_key' => 'sus_presentation',
                    // 'meta_value' => $presentation,
                    
                    // 'role__in' => ['teacher', 'alumni']
                );
            }

            // Get the array of teachers using the args set up above
            $teachers = get_users($teacher_args );

        }
        // set up $teacher_assessments array
        $teacher_assessments = [];
        

        // loop through the teachers
        foreach ($teachers as $key => $teacher) {

            // set up teacher object using ID as key
            $teacher_assessments[$teacher->ID] = new stdClass();

            // store the teacher name to $assessment_obj
            $teacher_assessments[$teacher->ID]->name = $teacher->display_name;

            // Get all the assessment IDs for the entreps that relate to assessments off the assessment comments
            // assessments will have the status 'graded' | 'in-progress' | 'ungraded'
            $comment_args = array(
                'status' => array('graded','in-progress','ungraded'),
                'type' => 'sensei_lesson_status',
                'user_id' => $teacher->ID,
            );

            $assessment_comments = get_comments( $comment_args );
            error_log(print_r($assessment_comments,true));

            // Set up assessments variable
            $assessments = [];
            // loop through the assessment comments
            foreach ($assessment_comments as $key2 => $comment) {
                $module = Sensei()->modules->get_lesson_module($comment->comment_post_ID);
                $course_id = get_post_meta( $comment->comment_post_ID, '_lesson_course', true );

                if ($course_id == $course || $course  == '' ) {
                    if ($module->term_id == $module_id || $module_id  == '' ) {
                        $assessments[$key2] = new stdClass();
                        // Store the status, updated date, week name, assessment name, feedback status, open response and ID
                        $assessments[$key2]->status = $comment->comment_approved;
                        $assessments[$key2]->date = $comment->comment_date;
                        $assessments[$key2]->lesson_id = $comment->comment_post_ID;
                        $assessments[$key2]->lesson_name = get_the_title($comment->comment_post_ID);
                        $assessments[$key2]->week = Sensei()->modules->get_lesson_module($comment->comment_post_ID);
                        $assessments[$key2]->quiz_id =  get_post_meta($assessments[$key2]->lesson_id, '_lesson_quiz', true);
                        // $assessments[$key2]->feedback = FundaWande()->assessments->user_can_view_feedback($assessments[$key2]->lesson_id,$teacher->ID);
                        $assessments[$key2]->need_feedback = FundaWande()->quiz->assessment_needs_feedback($assessments[$key2]->lesson_id);
                        // $assessments[$key2]->response_status = FundaWande()->assessments->assessment_response_status($assessments[$key2]->lesson_id,$teacher->ID,$comment->comment_ID);
                        // $assessments[$key2]->submitted = FundaWande()->assessments->user_has_submitted($assessments[$key2]->lesson_id,$teacher->ID);

                    }
                }
            }
            $teacher_assessments[$teacher->ID]->assessments = $assessments;
        }
        return $teacher_assessments;

    } // end get_teacher_assessments()

    /**
     * Get the assessment data for reviewing the assessment.
     *
     * @param integer $assessment_id the ID of the assessment to get data for.
     * @param integer $user_id the ID of the user to reivew.
     *
     * @return object $assessment_obj Object containing all the data relevant to reveiwing the assessment.
     */
    public function get_assessment_review($assessment_id, $user_id) {

        // set up $teacher object
        $teacher = get_user_by('ID', $user_id);


        // Get all the assessment IDs for the entreps that relate to assessments off the assessment comments
        // assessments will have the status 'graded' | 'in-progress' | 'ungraded'
        $comment_args = array(
            'number' => 1,
            'status' => array('graded','in-progress','ungraded'),
            'type' => 'sensei_lesson_status',
            'user_id' => $teacher->ID,
            'post_id' => $assessment_id
        );

        $assessment_comment = get_comments( $comment_args );

        if ( is_array($assessment_comment) && 1 == count($assessment_comment) ) {
            $assessment_comment = array_shift($assessment_comment);
        }

        // Set up assessments variable
        $assessment_obj = new stdClass();

        // Store the status, updated date, week name, assessment name, feedback status, open response and ID
        $assessment_obj->user_name = $teacher->display_name;
        $assessment_obj->user_id = $teacher->ID;
        $assessment_obj->status = $assessment_comment->comment_approved;
        $assessment_obj->date = $assessment_comment->comment_date;
        $assessment_obj->comment_id = $assessment_comment->comment_ID;
        $assessment_obj->lesson_id = $assessment_comment->comment_post_ID;
        $assessment_obj->lesson_name = get_the_title($assessment_comment->comment_post_ID);
        $assessment_obj->model_answer = get_field('_lesson_model_answer',$assessment_obj->lesson_id);
        $assessment_obj->quiz_id = get_post_meta($assessment_obj->lesson_id, '_lesson_quiz', true);

        $assessment_obj->week = Sensei()->modules->get_lesson_module($assessment_comment->comment_post_ID);
        $assessment_obj->responses = FundaWande()->assessments->get_assessment_feedback_responses($teacher->ID,$assessment_obj->lesson_id);

        // get the audio feedback
        $audio_feeback =   get_comment_meta($assessment_comment->comment_ID,'quiz_answers_audio_feedback',true);

        // set up questions array - in case more than one question in assessment
        $assessment_obj->questions = array();

        // get the quiz questions
        $questions = Sensei_Utils::sensei_get_quiz_questions($assessment_obj->quiz_id);

        foreach ($questions as $key => $question) {
            $question_data = WooThemes_Sensei_Question::get_template_data( $question->ID, $assessment_obj->quiz_id );
            $question_answer_feedback = Sensei()->quiz->get_user_question_feedback($assessment_obj->lesson_id, $question->ID, $user_id);
            $user_answer_content = Sensei()->quiz->get_user_question_answer( $assessment_obj->lesson_id,  $question->ID , $user_id );
            // Set up the question object to store the data for the specific question
            $assessment_obj->questions[$key] = new stdClass();
            $assessment_obj->questions[$key]->ID = $question->ID;

            // Check audio feedback is set
            if ($audio_feeback) {
                // assign audio feedback to the question object
                $assessment_obj->questions[$key]->audio = $audio_feeback[$question->ID];
            }

            // Get uploaded file
            if( $user_answer_content ) {
                $attachment_id = $user_answer_content;
                $answer_media_url = $answer_media_filename = '';
                if( 0 < intval( $attachment_id ) ) {
                    $answer_media_url = wp_get_attachment_url( $attachment_id );
                    $answer_media_filename = basename( $answer_media_url );
                    if( $answer_media_url && $answer_media_filename ) {
                        $user_answer_content = '<a href="' . esc_url( $answer_media_url ) . '" target="_blank">' . esc_html( $answer_media_filename ) . '</a>';
                    }
                }
            } else {
                $user_answer_content = '';
            }
            // Store the user answer to the question object
            $assessment_obj->questions[$key]->answer = $user_answer_content;

            // If there is feedback, assign to the question object
            if ($question_answer_feedback) {

                $assessment_obj->questions[$key]->feedback = $question_answer_feedback;
            }
        }

        // Get the feedback release boolean to which determines whether to release feedback to user
        $assessment_obj->feedback = get_comment_meta( $assessment_comment->comment_ID,'quiz_has_feedback',true);

        // Get the feedback release boolean to which determines whether to release feedback to user
        $assessment_obj->responses_closed = get_comment_meta( $assessment_comment->comment_ID,'responses_closed',true);


        return $assessment_obj;

    } // end get_assessment_review()


} // end FundaWande_Coaching
