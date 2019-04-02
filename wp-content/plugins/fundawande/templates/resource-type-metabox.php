<div class="resource-metabox">
    <?php $resource_type = get_post_meta($post->ID, 'resource_type', true) ?>
    <p>
        <label for="resource_type">Resource Type</label><br>
        <select name="resource_type" id="resource_type" class="postbox">
            <option value="Video" <?php selected($resource_type, 'Video'); ?>>Video</option>
            <option value="PDF" <?php selected($resource_type, 'PDF'); ?>>PDF</option>
        </select>
    </p>
</div> 