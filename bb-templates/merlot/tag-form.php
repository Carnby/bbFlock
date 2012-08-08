
<input name="tag" type="text" id="tag" size="10" maxlength="30" class="input-large" placeholder="<?php _e('Tag'); ?>"/>
<button type="submit" name="submit" class="btn btn-primary"><i class="icon icon-tags icon-white"></i> <?php _e('Add Tags'); ?></button>
<input type="hidden" name="id" value="<?php topic_id(); ?>" />

<script type="text/javascript">
$('#tag-form').on('submit', function() {
    var current_tag = $('#tag').attr('value');
    if (!current_tag) {
        return false;
    }
});
</script>
