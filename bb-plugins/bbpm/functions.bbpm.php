<?php 

// adapted from core bb_update_meta
function bbpm_update_meta($object_id, $meta_key, $meta_value) {
    global $bbdb;
    
    $meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);
    
    $meta_value = $_meta_value = bb_maybe_serialize($meta_value);
	$meta_value = bb_maybe_unserialize($meta_value);

	$cur = $bbdb->get_row($bbdb->prepare("SELECT * FROM {$bbdb->bbpm_meta} WHERE bbpm_id = %d AND meta_key = %s", $object_id, $meta_key));
	
	if (!$cur) {
		$bbdb->insert($bbdb->bbpm_meta, array('bbpm_id' => $object_id, 'meta_key' => $meta_key, 'meta_value' => $_meta_value));
	} elseif ($cur->meta_value != $meta_value) {
		$bbdb->update($bbdb->bbpm_meta, array('meta_value' => $_meta_value), array('bbpm_id' => $object_id, 'meta_key' => $meta_key));
	}
	
	return true;
}

// adapted from core bb_delete_meta
function bbpm_delete_meta($object_id, $meta_key, $meta_value = '') {
    global $bbdb;
    
    $meta_key = preg_replace('|[^a-z0-9_]|i', '', $meta_key);

    $meta_value = bb_maybe_serialize($meta_value);

	$meta_sql = empty($meta_value) ? 
		$bbdb->prepare("SELECT meta_id FROM {$bbdb->bbpm_meta} WHERE bbpm_id = %d AND meta_key = %s", $type_id, $meta_key) :
		$bbdb->prepare("SELECT meta_id FROM {$bbdb->bbpm_meta} WHERE bbpm_id = %d AND meta_key = %s AND meta_value = %s", $type_id, $meta_key, $meta_value);

	if (!$meta_id = $bbdb->get_var($meta_sql))
		return false;

	$bbdb->query($bbdb->prepare("DELETE FROM {$bbdb->bbpm_meta} WHERE meta_id = %d", $meta_id));
    
    return true;
}

// we don't need bbpm_get_meta because there is a method called get_thread_meta that does all the work.

// function for caching. in the future we should use real caching.
$bbpm_cache = array();

function bbpm_cache_add($key, $value, $group = '') {
    global $bbpm_cache;
    
    if (!empty($group))
        $key = $group . '_' . $key;
        
    if (isset($bbpm_cache[$key]))
        return false;
            
    $bbpm_cache[$key] = $value;
    return $value;
}

function bbpm_cache_set($key, $value, $group = '') {
    global $bbpm_cache;
    
    if (!empty($group))
        $key = $group . '_' . $key;
            
    $bbpm_cache[$key] = $value;
    return $value;
}

function bbpm_cache_get($key, $group = '') {
    global $bbpm_cache;
    
    if (!empty($group))
        $key = $group . '_' . $key;
    
    if (isset($bbpm_cache[$key]))
        return $bbpm_cache[$key];
    return false;
}

function bbpm_cache_delete($key, $group = '') {
    global $bbpm_cache;
    
    if (!empty($group))
        $key = $group . '_' . $key;
    
    if (isset($bbpm_cache[$key])) {
        unset($bbpm_cache[$key]);
        return true;
    }
    
    return false;
}

function bbpm_cache_flush($group = '') {
    //TODO: flush only group
    global $bbpm_cache;
    unset($bbpm_cache);
    $bbpm_cache = array();
}

