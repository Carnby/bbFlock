
<div class="control-group">
    <label for="tag" class="control-label"><?php _e('Insert one or more tags, separated by commas.'); ?></label>
    <div class="controls">
    <input name="tag" type="text" id="tag" size="10" maxlength="30" class="input-large" placeholder="<?php _e('Tag'); ?>"/></p>
    <p><button type="submit" name="submit" class="btn btn-primary"><i class="icon icon-tags icon-white"></i> <?php _e('Add Tags'); ?></button></p>
    </div>
</div>

<input type="hidden" name="id" value="<?php topic_id(); ?>" />

<script type="text/javascript">
$('#tag-form').on('submit', function() {
    var current_tag = $('#tag').attr('value');
    if (!current_tag) {
        return false;
    }
});
</script>
