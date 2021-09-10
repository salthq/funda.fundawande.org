<?php
if (!defined('ABSPATH')) exit;
/**
 * The Template for displaying Multiple Choice Questions.
 *
 * @author        Automattic
 * @package    Sensei
 * @category    Templates
 * @version     1.9.0
 */
?>

<?php

/**
 * Get the question data with the current quiz id
 * All data is loaded in this array to keep the template clean.
 */
$question_data = WooThemes_Sensei_Question::get_template_data(sensei_get_the_question_id(), get_the_ID());
shuffle($question_data['answer_options']);

// Get user answers.
try {
    $userAnswers = json_decode($question_data['user_answer_entry'], true);
} catch (Exception $e) {
}
if (!is_array($userAnswers)) {
    $userAnswers = [];
}

// Ensqure that each question has its own unique ID.
$uniqueId = FundaWande()->question->getUniqueId();

?>
<div class="answers" id="<?= $uniqueId ?>">
    <input type="hidden" name="sensei_question[<?= $question_data['ID'] ?>]">

    <div class="container-fluid">
        <!-- <div>
            <p class="_text-desktop">
                Match the following pages with the correct option given below:
            </p>
            <p class="_text-mobile">
                View the following images and match them below (click image to enlarge)
            </p>
        </div> -->

        <div class="row _option-images">
            <?php
            $count = 0;
            foreach ($question_data['answer_options'] as $id => $option) {
                $parts = explode('-', $option['answer']);
                $imageHash = FundaWande()->question->getImageHash($parts[0]);
                ?>

                <div class="col-6 col-md _option-image d-flex justify-content-center">
                    <div class="_image-letters">
                        Image <?= chr(ord('A') + $count) ?>
                    </div>

                    <div class="_image-container <?php echo esc_attr($option['option_class']); ?>">
                        <?php echo wp_get_attachment_image($parts[0], ['390', '300'], '', ['class' => 'img-responsive', 'data-option' => $imageHash]); ?>
                    </div>
                </div>
                <?php
                $count++;
            }
            ?>
        </div>
        

        <div class="_top-mobile-title">
            <p class="_text-mobile">
                Which image best relates to the following
            </p>
        </div>

        <div class="row _images-answers ">
            <?php
            // Shuffle answers again before echoing destination images.
            shuffle($question_data['answer_options']);
            $count = 0;
            foreach ($question_data['answer_options'] as $id => $option) {
                $parts = explode('-', $option['answer']);
                $imageHash = FundaWande()->question->getImageHash($parts[1]);
                ?>

                <div class="col-6 col-md row no-gutters _answer-container justify-content-center">
                    <div class="col mr-md-0 mr-4<?php echo esc_attr($option['option_class']); ?> _image-container <?php echo array_key_exists($imageHash, $userAnswers) ?  'chosen' : '' ;?>">
                        <?php echo wp_get_attachment_image($parts[1], ['390', '300'], '', ['class' => 'img-responsive']); ?>

                        <div class="_sortable-spot"></div>
                    </div>

                    <div class="col-6 _mobile-images-answers">
                        <?php
                        $count2 = 0;
                        foreach ($question_data['answer_options'] as $id2 => $option2) {
                            $parts2 = explode('-', $option2['answer']);
                            $imageHash2 = FundaWande()->question->getImageHash($parts2[0]);
                            $checked = array_key_exists($imageHash, $userAnswers) ? $userAnswers[$imageHash] === $imageHash2 : false;
                            ?>
                            <div class=" custom-control custom-radio">
                                <input id="<?= 'question_' . $question_data['ID'] . '-'.$count.'-' . '-option-' . $count2 ?>"
                                       class="custom-control-input"
                                       type="radio" <?= $checked ? 'checked' : '' ?>
                                       data-index="<?= $imageHash ?>"
                                       name="<?= 'question_' . $question_data['ID'] . '-option-' . $count ?>"
                                       value="<?= $imageHash2 ?>"
                                />

                                <label class="custom-control-label" for="<?= 'question_' . $question_data['ID'] . '-'.$count.'-' .  '-option-' . $count2 ?>">
                                    Image <?= chr(ord('A') + $count2) ?>
                                </label>

                            </div>
                            <?php
                            $count2++;
                        }
                        ?>
                    </div>
                </div>

                <?php
                $count++;
            }
            ?>
        </div>
        <div >
            <p class="_text-desktop"><small>*To change your answers, drag from the top (choices) to the bottom (answers) again</small></p>
        </div>
    </div>
</div>

<?php
FundaWande()->question_dnd_js->echoJavascript($uniqueId);
?>

