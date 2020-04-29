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
                Arrange the following in the correct order by dragging them into the correct order:
            </p>
            <p class="_text-mobile">
                Chose the most appropriate order of the following images (click to enlarge)
            </p>
        </div> -->

        <div class="row _option-images">
            <?php
            $count = 0;
            foreach ($question_data['answer_options'] as $id => $option) {
                $imageId = $option['answer'];
                $imageHash = FundaWande()->question->getImageHash($imageId);

                ?>

                <div class="col-6 col-md _option-image d-flex justify-content-center">
                    <div class="_image-letters">
                        Image <?= chr(ord('A') + $count) ?>
                    </div>

                    <div class="_image-container <?php echo esc_attr($option['option_class']); ?>">
                        <?php echo wp_get_attachment_image($imageId, ['390', '300'], '', ['class' => 'img-responsive', 'data-option' => $imageHash]); ?>
                    </div>
                </div>

                <?php
                $count++;
            }
            ?>
        </div>

        <div class="row _images-answers">
            <?php
            $count = 0;
            foreach ($question_data['answer_options'] as $id => $option) {
                ?>
                <?php if ($count > 0)  { ?>
                    <div class="col-sm-auto px-0 _arrow d-flex align-items-center justify-content-center">
                        <img class="d-md-block d-none" src="/wp-content/themes/fundawande/assets/lms/right_arrow.svg">
                        <img class="d-md-none d-block" src="/wp-content/themes/fundawande/assets/lms/down_arrow.svg">
                    </div>
                <?php } ?>
                <div class="col-6 col-md my-4 mt-md-0 px-4 _answer-container">
                    <div class="_box-image-container _image-container <?php echo esc_attr($option['option_class']); ?>">
                        <div class="_sortable-spot"></div>

                        <div class="_sortable-label"><em><?php echo ($count+1).'.'; ?></em></div>

                        <div class="_box-container"></div>
                    </div>

                    <div class="_mobile-images-answers">
                        <div>Which image goes <?= FundaWande()->question->ordinal($count + 1) ?>:</div>

                        <?php
                        $count2 = 0;
                        foreach ($question_data['answer_options'] as $id2 => $option2) {
                            $imageId = $option2['answer'];
                            $imageHash = FundaWande()->question->getImageHash($imageId);
                            $checked = array_key_exists($count, $userAnswers) ? $userAnswers[$count] === $imageHash : false;
                            ?>
                            <div class=" custom-control custom-radio">
                                <input id="<?= 'question_' . $question_data['ID'] . '-'.$count.'-' . '-option-' . $count2 ?>"
                                       class="custom-control-input" type="radio" <?= $checked ? 'checked' : '' ?>
                                       data-index="<?= $count ?>"
                                       name="<?= 'question_' . $question_data['ID'] . '-option-' . $count ?>"
                                       value="<?= $imageHash ?>"
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
    </div>
</div>

<?php
FundaWande()->question_dnd_js->echoJavascript($uniqueId);
?>
