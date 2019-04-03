<div class="resource-metabox">

    <!-- Video Resource Fields -->
    <div class="resource-fields" id="resource-video">
        <?php $video_file_name = get_post_meta($post->ID, 'video_file_name', true) ?>
        <div>
            <label for="video_file_name">Video File Name:</label><br>
            <input name="video_file_name" id="video_file_name" value="<?php echo esc_textarea($video_file_name) ?>" type="text" class="widefat" required>
        </div>

        <?php $saved_video = get_post_meta($post->ID, 'video_media', true) ?>
        <div>
            <label for="video_file">Holding Image:</label><br>
            <input type="url" class="large-text" name="video_media" id="video_media" type="button" value="<?php echo esc_attr($saved_video) ?>" readonly><br>
            <button type="button" class="button" id="video_upload_btn" data-media-uploader-target="#video_media">Upload Holding Image</button>
        </div>
        <br>

        <?php $video_description = get_post_meta($post->ID, 'video_description', true) ?>
        <div>
            <label for='video_description'>Video Description:</label><br>
            <textarea name="video_description" id="video_description" value="<?php echo esc_textarea($video_description) ?>" rows="5"><?php echo esc_textarea($video_description) ?></textarea>
        </div>
    </div>


    <!-- PDF Resource Fields -->
    <div class="resource-fields" id="resource-pdf">
        <?php $saved_pdf = get_post_meta($post->ID, 'pdf_media', true) ?>
        <div>
            <label for="pdf_file">PDF File:</label><br>
            <input type="url" class="large-text" name="pdf_media" id="pdf_media" type="button" value="<?php echo esc_attr($saved_pdf) ?>" readonly><br>
            <button type="button" class="button" id="pdf_upload_btn" data-media-uploader-target="#pdf_media">Upload PDF File</button>
        </div>
        <br>

        <?php $pdf_description = get_post_meta($post->ID, 'pdf_description', true) ?>
        <div>
            <label>PDF Description:</label><br>
            <textarea name="pdf_description" id="pdf_description" value="<?php echo esc_textarea($pdf_description) ?>" rows="5"><?php echo esc_textarea($pdf_description) ?></textarea>
        </div>
    </div>
</div> 