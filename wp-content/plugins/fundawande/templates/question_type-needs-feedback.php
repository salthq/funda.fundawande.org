<?php
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * The Template for displaying File Upload Questions.
 *
 * Override this template by copying it to yourtheme/sensei/single-quiz/question_type-file-upload.php
 *
 * @author 		Automattic
 * @package 	Sensei
 * @category    Templates
 * @version     1.9.0
 */
?>

<?php

/**
 * Get the question data with the current quiz id
 * All data is loaded in this array to keep the template clean.
 */
$question_id = sensei_get_the_question_id();
$question_data = WooThemes_Sensei_Question::get_template_data( $question_id, get_the_ID() );
$lesson_id = get_post_meta(get_the_ID(),'_quiz_lesson',true);
$user_id = get_current_user_id();
$completed = FundaWande()->quiz->user_completed_lesson($lesson_id,$user_id);

//Get the user's language preference to ensure the text strings are shown in the correct language. 
$lang_preference = get_user_meta($user_id, 'language_preference', true);
$lang = FundaWande()->language->get_language($lang_preference);


?>

<?php if ( !empty($question_data[ 'answer_media_url' ]) || !empty($question_data[ 'user_answer_entry' ]) ){ ?>
<div class="background-secondary p-4 mb-4">
    <h4 class="lbreaker-lms-purple mb-3"><?php the_field($lang->prefix . 'quiz_feedback_summary_title', 'options') ?></h4>
    <?php if ($completed) { ?>
        <p>
            <b><?php the_field($lang->prefix . 'quiz_activity_status_label', 'options') ?>:</b> <?php the_field($lang->prefix . 'quiz_completed_label', 'options') ?> 
            <img class="ml-2" src="/wp-content/themes/fundawande/assets/tick_green.svg">
        </p>

    <?php } else { ?>
        <p>
            <b><?php the_field($lang->prefix . 'quiz_activity_status_label', 'options') ?>:</b> <?php the_field($lang->prefix . 'quiz_submitted_label', 'options') ?>
        </p>

    <?php }
    
    $has_feedback = FundaWande()->quiz->user_can_view_feedback($lesson_id,$user_id);

    
    ?>
    <?php if ( $has_feedback) { ?>
        <p><b><?php the_field($lang->prefix . 'quiz_feedback_status_label', 'options') ?>:</b> <?php the_field($lang->prefix . 'feedback_given_label', 'options') ?> <img class="ml-2" src="/wp-content/themes/fundawande/assets/tick_green.svg"></p>
        <p class="text-italic"><?php the_field($lang->prefix . 'feedback_given_message', 'options') ?></p>

    <?php } else { ?>
        <p><b><?php the_field($lang->prefix . 'feedback_status_label', 'options') ?>:</b> <?php the_field($lang->prefix . 'awaiting_feedback_label', 'options') ?></p>
    <?php } ?>
    <p><b><?php the_field($lang->prefix . 'question_label', 'options') ?>: </b><?php echo $question_data[ 'title' ]; ?></p>


    <?php if ( !empty($question_data[ 'answer_media_url' ])  &&  $question_data[ 'answer_media_filename' ]  ) { ?>
            <p class="submitted_file">

            <?php

            printf( __( '<b>Submitted file: %1$s</b>', 'woothemes-sensei' ), '<a href="' . esc_url(  $question_data[ 'answer_media_url' ] )
                . '" target="_blank">'
                . esc_html(  $question_data[ 'answer_media_filename' ] ) . '</a>' );
            ?>

        </p>

    <?php } elseif ($question_data[ 'user_answer_entry' ])  {?>
        <p class="submitted_file">

            <?php

            printf( __( '<b>Submission: </b> </br> %1$s', 'woothemes-sensei' ),  $question_data[ 'user_answer_entry' ]  );
            ?>

        </p>
    <?php } ?>
</div>

<!-- If the activity has not been set to completed by the coach, the entrepreneur can still remove and resubmit -->
<?php if (!$completed) { ?>
    <form method="POST" action="<?php echo esc_url_raw( get_permalink() ); ?>" enctype="multipart/form-data">

        <?php
        do_action( 'sensei_single_quiz_questions_after', get_the_id() );
        ?>
    </form>
<?php } ?>
<?php } 

$question_answer_feedback = Sensei()->quiz->get_user_question_feedback($lesson_id, $question_id, $user_id);

    if ($question_answer_feedback && !empty($question_answer_feedback) ) {
        ?>
<div class="p-3 my-4" style="border:1px solid #d8d8d8;">
    <?php
    
        echo ' <h4 class="lbreaker-lms-purple  mb-3">Our feedback for you</h4>';

    ?>
    <div id="" class="px-3 content-wrapper" >
        <?php echo $question_answer_feedback; ?>

    </div>
     
  
</div>
<?php } ?>
<?php
?>
