<?php
##############################################
#
##############################################
if (PY_MODE == "BE") {
	
	$callable = array(
	    Backend::getInstance(),
	    "initialize"
	);
	
	PY\HookProvider::getInstance()->registerCall(PY_HOOK_INITIALIZED, $callable);
	
}
