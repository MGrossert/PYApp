<?php
##############################################
# PROVIDE HOOKS
##############################################

Const PY_HOOK_INITIALIZED = "py-initialized";
$system->registerHook(PY_HOOK_INITIALIZED, "Called after initialized.");

##############################################
# 
##############################################


// var_export($system->readStructure(true));
// echo microtime(true)-START;
// var_dump(opcache_get_configuration());
// die(PY_HOOK_INITIALIZED);