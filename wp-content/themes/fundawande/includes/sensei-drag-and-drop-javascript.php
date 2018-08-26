<?php

namespace FundaWande;

class SenseiDragAndDropJavaScript
{
    public static function echoJavascript($uniqueId)
    {
        ?>
        <script>
            jQuery(document).ready(function ($) {
                var groupName = '<?=$uniqueId?>';
                var groupElement = $('#' + groupName);
                var optionImages = groupElement.find('._option-images ._image-container');
                var sortableSpots = groupElement.find('._images-answers ._sortable-spot');
                var radioInputs = groupElement.find('input[type=radio]');

                // Make each option-image sortable.
                optionImages.each(function () {
                    Sortable.create(this, {
                        group: {
                            name: groupName,
                            pull: 'clone',
                            put: false
                        },
                        onMove: function (evt, originalEvent) {
                            // Show answer images, i.e. undo any previous changes.
                            sortableSpots.find('img').css('display', '');

                            // If we're dragging an option inside an answer, hide any other identical answers (i.e. we can't use same image in two different answers).
                            if (!$(evt.to).hasClass('_image-container')) {
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
                        group: {
                            name: groupName,
                            pull: false,
                            put: true
                        },
                        onAdd: function (evt) {
                            // Remove all other hidden answers.
                            sortableSpots.find('img:not(:visible)').remove();

                            // Sync input tags.
                            syncInputTags();
                        },
                    });
                });

                // When clicking on a radio-input, update the associated image, and also remove duplicates.
                radioInputs.on('change', function () {
                    onRadioInputChange(this);
                    syncInputTags();
                });

                // Initialize the user's previous answers.
                radioInputs.each(function () {
                    onRadioInputChange(this);
                });
                syncInputTags();

                function onRadioInputChange(input) {
                    if (!$(input).is(':checked')) {
                        return;
                    }

                    var dataOption = $(input).attr('value');
                    var sortableSpot = $(input).closest('._answer-container').find('._sortable-spot');

                    // Remove duplicate answers.
                    sortableSpots.find('img[data-option=' + dataOption + ']').remove();

                    // Find image.
                    var image = optionImages.find('[data-option=' + dataOption + ']');

                    // Remove any previous image.
                    sortableSpot.find('img').remove();
                    sortableSpot.append(image.clone());
                }

                // Update input tags based on their associated images.
                function syncInputTags() {
                    // Select the correct radio buttons.
                    radioInputs.each(function () {
                        var sortableSpot = $(this).closest('._answer-container').find('._sortable-spot');
                        var dataOption = sortableSpot.find('img').attr('data-option');
                        $(this).prop('checked', dataOption === $(this).attr('value'));
                    });

                    // Save values.
                    var values = {};
                    radioInputs.filter(':checked').each(function () {
                        values[$(this).attr('data-index')] = $(this).attr('value');
                    });
                    groupElement.find('input[type=hidden]').val(JSON.stringify(values));
                }
            });
        </script>
        <?php
    }
}