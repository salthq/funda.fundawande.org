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

?>
<ul class="answers">
    <div class="container-fluid">
        <div>
            <p>Match the following pages with the correct option given below:</p>
        </div>

        <div class="row _option-images">
            <?php
            $count = 0;
            foreach ($question_data['answer_options'] as $id => $option) {
                $parts = explode('-', $option['answer']);
                $count++;
                ?>

                <div class="col-sm-3 _option-image">
                    <div class="_image-letters">
                        Image <?= chr(ord('A') + $count - 1) ?>
                    </div>

                    <li class=" _image-container <?php echo esc_attr($option['option_class']); ?>">
                        <input type="<?php echo $option['type']; ?>"
                               id="<?php echo esc_attr('question_' . $question_data['ID']) . '-option-' . $count; ?>"
                               name="<?php echo esc_attr('sensei_question[' . $question_data['ID'] . ']'); ?>[]"
                               value="<?php echo esc_attr($option['answer']); ?>" <?php echo $option['checked']; ?>
                            <?php echo is_user_logged_in() ? '' : ' disabled'; ?>
                        />

                        <label for="<?php echo esc_attr('question_' . $question_data['ID']) . '-option-' . $count; ?>">
                            <?php echo wp_get_attachment_image($parts[0], array('390', '300'), "", array("class" => "img-responsive")); ?>
                        </label>

                    </li>

                </div>
                <?php
            }
            ?>
        </div>

        <div class="_top-mobile-title">
            <p>Which image best relates to the following</p>
        </div>

        <div class="row _images-answers">
            <?php
            $count = 0;
            foreach ($question_data['answer_options'] as $id => $option) {
                $parts = explode('-', $option['answer']);

                $count++;
                ?>

                <div class="col-sm-3 _answer-container">
                    <li class="<?php echo esc_attr($option['option_class']); ?> _image-container">
                        <input type="<?php echo $option['type']; ?>"
                               id="<?php echo esc_attr('question_' . $question_data['ID']) . '-option-' . $count; ?>"
                               name="<?php echo esc_attr('sensei_question[' . $question_data['ID'] . ']'); ?>[]"
                               value="<?php echo esc_attr($option['answer']); ?>" <?php echo $option['checked']; ?>
                            <?php echo is_user_logged_in() ? '' : ' disabled'; ?>
                        />

                        <label for="<?php echo esc_attr('question_' . $question_data['ID']) . '-option-' . $count; ?>">
                            <?php echo wp_get_attachment_image($parts[1], array('390', '300'), "", array("class" => "img-responsive")); ?>
                        </label>

                    </li>

                    <div class="_mobile-images-answers">
                        <?php
                        $count2 = 0;
                        foreach ($question_data['answer_options'] as $id2 => $option2) {
                            $count2++;
                            ?>
                            <div>
                                <label>
                                    <input type="radio">
                                    Image <?= chr(ord('A') + $count2 - 1) ?>
                                </label>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</ul>



