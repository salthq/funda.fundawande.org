jQuery(document).ready(function () {
    jQuery('body').on('click', '.question-upload-image-button', function () {
        var button = $(this);

        var mediaDialog = wp.media({
            title: 'Insert image',
            library: {
                // uncomment the next line if you want to attach image to the current post
                // uploadedTo : wp.media.view.settings.post.id,
                type: 'image'
            },
            button: {
                text: 'Use this image' // button label text
            },
            multiple: false // for multiple image selection set to true
        });

        mediaDialog.on('select', function () {
            var attachment = mediaDialog.state().get('selection').first().toJSON();
            var parent = button.parent();
            var answerElement = button.closest('.answer');

            // Set image preview.
            var img = parent.find('img');
            if (img.length === 0) {
                img = $('<img>').appendTo(parent);
            }
            img.attr('src', attachment.sizes.thumbnail.url);

            // Set image ID.
            var input = answerElement.find('input');
            var dataPart = button.attr('data-part');
            if (typeof dataPart === 'undefined') {
                // Save ID directly.
                input.val(attachment.id);
            }
            else {
                // Save ID in two parts, separated by a dash: id1-id2.
                var parts = input.val();
                parts = parts ? parts.split('-') : [0, 0];
                console.log(parts);
                parts[dataPart] = attachment.id;
                input.val(parts.join('-'));
            }
        });

        mediaDialog.open();
    });

    jQuery('#add-question-main').on('click', '.add_wrong_multiple_choice_with_images_answer_option', function () {
        var question_counter = jQuery(this).attr('rel');
        var answer_count = jQuery('input[name="question_wrong_answers[]"]').length - 1;
        answer_count++;
        var html = '<label class="answer" for="question_' + question_counter + '_wrong_answer_' + answer_count + '"><span>' + woo_localized_data.wrong_colon + '</span><div><button type="button" class="button question-upload-image-button">Upload option image</button></div> <input type="text" id="question_' + question_counter + '_wrong_answer_' + answer_count + '" name="question_wrong_answers[]" value="" size="25" class="question_answer widefat" /> <a class="remove_answer_option"></a></label>';
        jQuery(this).closest('div').before(html);
    });

    jQuery('#add-question-main').on('click', '.add_right_multiple_choice_with_images_answer_option', function () {
        var question_counter = jQuery(this).attr('rel');
        var answer_count = jQuery('input[name="question_right_answers[]"]').length - 1;
        answer_count++;
        var html = '<label class="answer" for="question_' + question_counter + '_right_answer_' + answer_count + '"><span>' + woo_localized_data.right_colon + '</span><div><button type="button" class="button question-upload-image-button">Upload option image</button></div> <input type="text" id="question_' + question_counter + '_right_answer_' + answer_count + '" name="question_right_answers[]" value="" size="25" class="question_answer widefat" /> <a class="remove_answer_option"></a></label>';
        jQuery(this).closest('div').before(html);
    });

    jQuery('#add-question-main').on('click', '.add_drag_and_drop_non_sequential_answer_option', function () {
        var question_counter = jQuery(this).attr('rel');
        var answer_count = jQuery('input[name="question_right_answers[]"]').length - 1;
        answer_count++;
        var html =
            '<label class="answer" for="question_' + question_counter + '_right_answer_' + answer_count + '">' +
            '<div class="_float-left"><button data-part="0" type="button" class="button question-upload-image-button">Add option image</button></div>' +
            '<div class="_float-right"><button data-part="1" type="button" class="button question-upload-image-button">Add destination image</button></div>' +
            '<input type="text" id="question_' + question_counter + '_right_answer_' + answer_count + '" name="question_right_answers[]" value="" size="25" class="question_answer widefat" />' +
            ' <a class="remove_answer_option"></a>' +
            '</label>';
        jQuery(this).closest('div').before(html);
    });

    jQuery('#add-question-main').on('click', '.add_drag_and_drop_sequential_answer_option', function () {
        var question_counter = jQuery(this).attr('rel');
        var answer_count = jQuery('input[name="question_right_answers[]"]').length - 1;
        answer_count++;
        var html = '<label class="answer" for="question_' + question_counter + '_right_answer_' + answer_count + '"><span></span><div class="_float-left"><button type="button" class="button question-upload-image-button">Upload option image</button></div> <input type="text" id="question_' + question_counter + '_right_answer_' + answer_count + '" name="question_right_answers[]" value="" size="25" class="question_answer widefat" /> <a class="remove_answer_option"></a></label>';
        jQuery(this).closest('div').before(html);
    });
});
