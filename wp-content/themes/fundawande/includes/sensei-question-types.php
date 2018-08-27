<?php

namespace FundaWande;

add_filter('sensei_question_types', __NAMESPACE__ . '\\SenseiQuestionTypes::sensei_question_types', 10, 1);
add_filter('sensei_autogradable_question_types', __NAMESPACE__ . '\\SenseiQuestionTypes::sensei_autogradable_question_types', 10, 1);
add_filter('sensei_grade_question_auto', __NAMESPACE__ . '\\SenseiQuestionTypes::sensei_grade_question_auto', 10, 4);
add_filter('quiz_panel_question_field', __NAMESPACE__ . '\\SenseiQuestionTypes::quiz_panel_question_field', 10, 5);

class SenseiQuestionTypes
{
    public static function sensei_question_types($types)
    {
        $types['multiple-choice-with-images'] = 'Multiple-choice with Images';
        $types['drag-and-drop-non-sequential'] = 'Drag-and-drop Non-Sequential';
        $types['drag-and-drop-sequential'] = 'Drag-and-drop Sequential';

        return $types;
    }

    public static function sensei_autogradable_question_types($types)
    {
        $types[] = 'multiple-choice-with-images';
        $types[] = 'drag-and-drop-non-sequential';
        $types[] = 'drag-and-drop-sequential';

        return $types;
    }

    public static function sensei_grade_question_auto($question_grade, $question_id, $question_type, $answer)
    {
        switch ($question_type) {
            case 'drag-and-drop-non-sequential':
                // Get right answers.
                $right_answer = (array)get_post_meta($question_id, '_question_right_answer', true);

                // Get user's answers, strip slashes, and then decode as an array.
                try {
                    $answer = json_decode(stripslashes($answer), true);
                } catch (\Exception $e) {
                    $answer = '';
                }

                // Check each answer.
                if (!is_array($answer) || count($answer) !== count($right_answer)) {
                    $correct = false;
                } else {
                    $correct = true;
                    foreach ($right_answer as $index => $parts) {
                        $parts = explode('-', $parts);
                        $hash0 = self::getImageHash($parts[0]);
                        $hash1 = self::getImageHash($parts[1]);

                        if (!array_key_exists($hash1, $answer) || $hash0 !== $answer[$hash1]) {
                            $correct = false;
                            break;
                        }
                    }
                }

                // Apply grade.
                if ($correct) {
                    $question_grade = Sensei()->question->get_question_grade($question_id);
                }
                break;

            case 'drag-and-drop-sequential':
                // Get right answers.
                $right_answer = (array)get_post_meta($question_id, '_question_right_answer', true);

                // Get user's answers, strip slashes, and then decode as an array.
                try {
                    $answer = json_decode(stripslashes($answer), true);
                } catch (\Exception $e) {
                    $answer = '';
                }

                // Check each answer.
                if (!is_array($answer) || count($answer) !== count($right_answer)) {
                    $correct = false;
                } else {
                    $correct = true;
                    foreach ($right_answer as $index => $imageId) {
                        $hash = self::getImageHash($imageId);

                        if ($hash !== $answer[$index]) {
                            $correct = false;
                            break;
                        }
                    }
                }

                // Apply grade.
                if ($correct) {
                    $question_grade = Sensei()->question->get_question_grade($question_id);
                }
                break;
        }

        return $question_grade;
    }

    public static function quiz_panel_question_field($html, $that, $question_type, $question_id, $question_counter)
    {
        $right_answer = '';
        $wrong_answers = array();
        $answer_order_string = '';
        $answer_order = array();
        if ($question_id) {
            $right_answer = get_post_meta($question_id, '_question_right_answer', true);
            $wrong_answers = get_post_meta($question_id, '_question_wrong_answers', true);
            $answer_order_string = get_post_meta($question_id, '_answer_order', true);
            $answer_order = array_filter(explode(',', $answer_order_string));
            $question_class = '';
        } else {
            $question_id = '';
            $question_class = 'answer-fields question_required_fields hidden';
        }

        switch ($question_type) {
            case 'multiple-choice-with-images':
                $html .= '<div class="question_multiple_choice_with_images_fields multiple-choice-answers ' . esc_attr($question_class) . '">';

                $right_answers = (array)$right_answer;
                // Calculate total right answers available (defaults to 1)
                $total_right = 0;
                if ($question_id) {
                    $total_right = get_post_meta($question_id, '_right_answer_count', true);
                }
                if (0 == intval($total_right)) {
                    $total_right = 1;
                }
                for ($i = 0; $i < $total_right; $i++) {
                    if (!isset($right_answers[$i])) {
                        $right_answers[$i] = '';
                    }
                    $right_answer_id = $that->get_answer_id($right_answers[$i]);
                    // Right Answer
                    $right_answer = '
                        <label class="answer" for="question_' . esc_attr($question_counter) . '_right_answer_' . $i . '">
                        <span>' . esc_html__('Right:', 'woothemes-sensei') . '</span>
                        <div class="_float-left">' . self::getUploadImageButton(null, 'Upload option image', $right_answers[$i]) . '</div>
                        <input rel="' . esc_attr($right_answer_id) . '" type="text" id="question_' . esc_attr($question_counter) . '_right_answer_' . esc_attr($i) . '" name="question_right_answers[]" value="' . esc_attr($right_answers[$i]) . '" size="25" class="question_answer widefat" />
                        <a class="remove_answer_option"></a>
                        </label>
                    ';

                    if ($question_id) {
                        $answers[$right_answer_id] = $right_answer;
                    } else {
                        $answers[] = $right_answer;
                    }
                }

                // Calculate total wrong answers available (defaults to 4)
                $total_wrong = 0;
                if ($question_id) {
                    $total_wrong = get_post_meta($question_id, '_wrong_answer_count', true);
                }
                if (0 == intval($total_wrong)) {
                    $total_wrong = 1;
                }

                // Setup Wrong Answer HTML
                foreach ($wrong_answers as $i => $answer) {
                    $answer_id = $that->get_answer_id($answer);
                    $wrong_answer = '
                        <label class="answer" for="question_' . esc_attr($question_counter) . '_wrong_answer_' . esc_attr($i) . '">
                        <span>' . esc_html__('Wrong:', 'woothemes-sensei') . '</span>
                        <div>' . self::getUploadImageButton(null, 'Upload option image', $answer) . '</div>
                        <input rel="' . esc_attr($answer_id) . '" type="text" id="question_' . esc_attr($question_counter) . '_wrong_answer_' . esc_attr($i) . '" name="question_wrong_answers[]" value="' . esc_attr($answer) . '" size="25" class="question_answer widefat" /> 
                        <a class="remove_answer_option"></a>
                        </label>
                    ';

                    if ($question_id) {
                        $answers[$answer_id] = $wrong_answer;
                    } else {
                        $answers[] = $wrong_answer;
                    }
                } // end for each

                $answers_sorted = $answers;
                if ($question_id && count($answer_order) > 0) {
                    $answers_sorted = array();
                    foreach ($answer_order as $answer_id) {
                        if (isset($answers[$answer_id])) {
                            $answers_sorted[$answer_id] = $answers[$answer_id];
                            unset($answers[$answer_id]);
                        }
                    }

                    if (count($answers) > 0) {
                        foreach ($answers as $id => $answer) {
                            $answers_sorted[$id] = $answer;
                        }
                    }
                }

                foreach ($answers_sorted as $id => $answer) {
                    $html .= $answer;
                }

                $html .= '<input type="hidden" class="answer_order" name="answer_order" value="' . esc_attr($answer_order_string) . '" />';
                $html .= '<span class="hidden right_answer_count">' . esc_html($total_right) . '</span>';
                $html .= '<span class="hidden wrong_answer_count">' . esc_html($total_wrong) . '</span>';

                $html .= '<div class="add_answer_options">';
                $html .= '<a class="add_right_multiple_choice_with_images_answer_option add_answer_option button" rel="' . esc_attr($question_counter) . '">' . esc_html__('Add right answer', 'woothemes-sensei') . '</a>';
                $html .= '<a class="add_wrong_multiple_choice_with_images_answer_option add_answer_option button" rel="' . esc_attr($question_counter) . '">' . esc_html__('Add wrong answer', 'woothemes-sensei') . '</a>';
                $html .= '</div>';

                $html .= $that->quiz_panel_question_feedback($question_counter, $question_id, 'multiple-choice');

                $html .= '</div>';
                break;

            case 'drag-and-drop-non-sequential':
                $html .= '<div class="question_drag_and_drop_non_sequential_fields multiple-choice-answers ' . esc_attr($question_class) . '">';
                $html .= '<p>Add the question options and their matching images.</p>';

                $right_answers = (array)$right_answer;
                // Calculate total right answers available (defaults to 1)
                $total_right = 0;
                if ($question_id) {
                    $total_right = get_post_meta($question_id, '_right_answer_count', true);
                }
                if (0 == intval($total_right)) {
                    $total_right = 1;
                }
                for ($i = 0; $i < $total_right; $i++) {
                    if (!isset($right_answers[$i])) {
                        $right_answers[$i] = '';
                    }
                    $right_answer_id = $that->get_answer_id($right_answers[$i]);
                    // Right Answer
                    $right_answer = '
                        <label class="answer" for="question_' . esc_attr($question_counter) . '_right_answer_' . $i . '">
                            <div class="_float-left">' . self::getUploadImageButton(0, 'Add option image', $right_answers[$i]) . '</div>
                            <div class="_float-right">' . self::getUploadImageButton(1, 'Add destination image', $right_answers[$i]) . '</div>
                            <input rel="' . esc_attr($right_answer_id) . '" type="text" id="question_' . esc_attr($question_counter) . '_right_answer_' . esc_attr($i) . '" name="question_right_answers[]" value="' . esc_attr($right_answers[$i]) . '" size="25" class="question_answer widefat" /> 
                            <a class="remove_answer_option"></a>
                        </label>
                    ';
                    if ($question_id) {
                        $answers[$right_answer_id] = $right_answer;
                    } else {
                        $answers[] = $right_answer;
                    }
                }

                // Calculate total wrong answers available (defaults to 4)
                $total_wrong = 0;
                if ($question_id) {
                    $total_wrong = get_post_meta($question_id, '_wrong_answer_count', true);
                }
                if (0 == intval($total_wrong)) {
                    $total_wrong = 1;
                }

                $answers_sorted = $answers;
                if ($question_id && count($answer_order) > 0) {
                    $answers_sorted = array();
                    foreach ($answer_order as $answer_id) {
                        if (isset($answers[$answer_id])) {
                            $answers_sorted[$answer_id] = $answers[$answer_id];
                            unset($answers[$answer_id]);
                        }
                    }

                    if (count($answers) > 0) {
                        foreach ($answers as $id => $answer) {
                            $answers_sorted[$id] = $answer;
                        }
                    }
                }

                foreach ($answers_sorted as $id => $answer) {
                    $html .= $answer;
                }

                $html .= '<input type="hidden" class="answer_order" name="answer_order" value="' . esc_attr($answer_order_string) . '" />';
                $html .= '<span class="hidden right_answer_count">' . esc_html($total_right) . '</span>';
                $html .= '<span class="hidden wrong_answer_count">' . esc_html($total_wrong) . '</span>';

                $html .= '<div class="add_answer_options">';
                $html .= '<a class="add_drag_and_drop_non_sequential_answer_option add_answer_option button" rel="' . esc_attr($question_counter) . '">' . esc_html__('Add another option', 'woothemes-sensei') . '</a>';
                $html .= '</div>';

                $html .= $that->quiz_panel_question_feedback($question_counter, $question_id, 'multiple-choice');

                $html .= '</div>';
                break;

            case 'drag-and-drop-sequential':
                $html .= '<div class="question_drag_and_drop_sequential_fields multiple-choice-answers ' . esc_attr($question_class) . '">';
                $html .= '<p>Add the question options in the correct order. They will be shuffled in the question.</p>';

                $right_answers = (array)$right_answer;
                // Calculate total right answers available (defaults to 1)
                $total_right = 0;
                if ($question_id) {
                    $total_right = get_post_meta($question_id, '_right_answer_count', true);
                }
                if (0 == intval($total_right)) {
                    $total_right = 1;
                }
                for ($i = 0; $i < $total_right; $i++) {
                    if (!isset($right_answers[$i])) {
                        $right_answers[$i] = '';
                    }
                    $right_answer_id = $that->get_answer_id($right_answers[$i]);
                    // Right Answer
                    $right_answer = '
                        <label class="answer" for="question_' . esc_attr($question_counter) . '_right_answer_' . $i . '">
                        <span></span>
                        <div class="_float-left">' . self::getUploadImageButton(null, 'Upload option image', $right_answers[$i]) . '</div> 
                        <input rel="' . esc_attr($right_answer_id) . '" type="text" id="question_' . esc_attr($question_counter) . '_right_answer_' . esc_attr($i) . '" name="question_right_answers[]" value="' . esc_attr($right_answers[$i]) . '" size="25" class="question_answer widefat" /> 
                        <a class="remove_answer_option"></a>
                        </label>
                    ';
                    if ($question_id) {
                        $answers[$right_answer_id] = $right_answer;
                    } else {
                        $answers[] = $right_answer;
                    }
                }

                // Calculate total wrong answers available (defaults to 4)
                $total_wrong = 0;
                if ($question_id) {
                    $total_wrong = get_post_meta($question_id, '_wrong_answer_count', true);
                }
                if (0 == intval($total_wrong)) {
                    $total_wrong = 1;
                }

                $answers_sorted = $answers;
                if ($question_id && count($answer_order) > 0) {
                    $answers_sorted = array();
                    foreach ($answer_order as $answer_id) {
                        if (isset($answers[$answer_id])) {
                            $answers_sorted[$answer_id] = $answers[$answer_id];
                            unset($answers[$answer_id]);
                        }
                    }

                    if (count($answers) > 0) {
                        foreach ($answers as $id => $answer) {
                            $answers_sorted[$id] = $answer;
                        }
                    }
                }

                foreach ($answers_sorted as $id => $answer) {
                    $html .= $answer;
                }

                $html .= '<input type="hidden" class="answer_order" name="answer_order" value="' . esc_attr($answer_order_string) . '" />';
                $html .= '<span class="hidden right_answer_count">' . esc_html($total_right) . '</span>';
                $html .= '<span class="hidden wrong_answer_count">' . esc_html($total_wrong) . '</span>';

                $html .= '<div class="add_answer_options">';
                $html .= '<a class="add_drag_and_drop_sequential_answer_option add_answer_option button" rel="' . esc_attr($question_counter) . '">' . esc_html__('Add another option', 'woothemes-sensei') . '</a>';
                $html .= '</div>';

                $html .= $that->quiz_panel_question_feedback($question_counter, $question_id, 'multiple-choice');

                $html .= '</div>';
                break;
        }

        return $html;
    }

    public static function getUploadImageButton($dataPart, $text, $imageId)
    {
        if ($imageId) {
            if ($dataPart !== null) {
                $parts = explode('-', $imageId);
                $imageId = $parts[$dataPart];
            }

            $img = wp_get_attachment_image($imageId, 'thumbnail');
        } else {
            $img = '';
        }

        ob_start();
        ?>
        <button data-part="<?= $dataPart ?>"
                type="button"
                class="button question-upload-image-button">
            <?= $text ?>
        </button>
        <?= $img ?>
        <?php
        return ob_get_clean();
    }

    // https://stackoverflow.com/a/3110033
    public static function ordinal($number)
    {
        $ends = array('th', 'st', 'nd', 'rd', 'th', 'th', 'th', 'th', 'th', 'th');
        if ((($number % 100) >= 11) && (($number % 100) <= 13))
            return $number . 'th';
        else
            return $number . $ends[$number % 10];
    }

    // Get a unique ID on subsequent calls.
    public static function getUniqueId()
    {
        static $id = 0;
        return 'unique_id_' . $id++;
    }

    // Get a unique hash based on the image's URL. We use this to check if the user answered corectly or not.
    public static function getImageHash($imageId)
    {
        return md5(wp_get_attachment_url($imageId));
    }
}