<?php 
// functions for caching. in the future we should use real caching.
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

