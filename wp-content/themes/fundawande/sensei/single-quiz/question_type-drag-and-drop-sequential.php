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

// Ensqure that each question has its own unique ID.
$uniqueId = \FundaWande\SenseiQuestionTypes::getUniqueId();

?>
<ul class="answers" id="<?= $uniqueId ?>">
    <div class="container-fluid">
        <div>
            <p class="_text-desktop">
                Arrange the following in the correct order by dragging them into the correct order:
            </p>
            <p class="_text-mobile">
                Chose the most appropriate order of the following images (click to enlarge)
            </p>
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
                        Image <?= chr(ord('A') + $count) ?>
                    </div>

                    <div class="_image-container <?php echo esc_attr($option['option_class']); ?>">
                        <?php echo wp_get_attachment_image($parts[0], ['390', '300'], '', ['class' => 'img-responsive', 'data-option' => $count]); ?>
                    </div>
                </div>

                <?php
            }
            ?>
        </div>

        <div class="row _images-answers">
            <?php
            $count = 0;
            foreach ($question_data['answer_options'] as $id => $option) {
                $count++;
                ?>

                <div class="col-sm-3 _answer-container">
                    <div class="_box-image-container _image-container <?php echo esc_attr($option['option_class']); ?>">
                        <div class="_sortable-spot"></div>

                        <div class="_box-container"></div>

                        <div class="_arrow-right">
                            <i class="fa fa-arrow-right" aria-hidden="true"></i>
                        </div>

                        <div class="_arrow-down">
                            <i class="fa fa-arrow-down" aria-hidden="true"></i>
                        </div>
                    </div>

                    <div class="_mobile-images-answers">
                        <div>Which image goes <?= \FundaWande\SenseiQuestionTypes::ordinal($count) ?>:</div>

                        <?php
                        $count2 = 0;
                        foreach ($question_data['answer_options'] as $id2 => $option2) {
                            $count2++;
                            $name = $uniqueId
                            ?>
                            <div>
                                <label>
                                    <input type="radio"
                                           name="<?= 'question_' . $question_data['ID'] . '-option-' . $count ?>"
                                           value="<?= $count2 ?>">
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

<script>
    jQuery(document).ready(function ($) {
        var groupName = '<?=$uniqueId?>';
        var groupElement = $('#' + groupName);
        var optionImages = groupElement.find('._option-images ._image-container');
        var sortableSpots = groupElement.find('._images-answers ._sortable-spot');
        var radioInputs = groupElement.find('input[type=radio]');

        optionImages.each(function () {
            Sortable.create(this, {
                group: {
                    name: groupName,
                    pull: 'clone',
                    put: 'false'
                },
                onMove: function (evt, originalEvent) {
                    // Show answer images, i.e. undo any previous changes.
                    sortableSpots.find('img').css('display', '');

                    // If we're dragging an option inside an answer, hide any other identical answers (i.e. we can't use same image in two different answers).
                    if (!$(evt.to).hasClass('_image-container ')) {
                        var answer = $(evt.dragged).attr('data-option');
                        sortableSpots.find('img[data-option=' + answer + ']').hide();
                    }

                    // Hide all other image options from inside the "to" element, e.g. when we're dragging over an existing image.
                    $(evt.to).find('img').css('display', 'none');
                    $(evt.dragged).css('display', '');
                }
            });
        });

        // Create "drop-zone" for each answer box.
        sortableSpots.each(function () {
            Sortable.create(this, {
                group: groupName,
                onAdd: function (evt) {
                    // Remove all other hidden answers.
                    sortableSpots.find('img:not(:visible)').remove();

                    // Sync input tags.
                    syncInputTags();
                },
            });
        });

        radioInputs.on('change', function () {
            if (!$(this).is(':checked')) {
                return;
            }

            var dataOption = $(this).attr('value');
            var sortableSpot = $(this).closest('._answer-container').find('._sortable-spot');

            // Remove duplicate answers.
            sortableSpots.find('img[data-option=' + dataOption + ']').remove();

            // Find image.
            var image = optionImages.find('[data-option=' + dataOption + ']');

            // Remove any previous image.
            sortableSpot.find('img').remove();
            sortableSpot.append(image.clone());
            
            // Sync input tags.
            syncInputTags();
        });

        function syncInputTags() {
            radioInputs.each(function () {
                var sortableSpot = $(this).closest('._answer-container').find('._sortable-spot');
                var dataOption = sortableSpot.find('img').attr('data-option');
                $(this).prop('checked', dataOption === $(this).attr('value'));
            });
        }
    });
</script>
