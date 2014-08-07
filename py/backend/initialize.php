<?php
##############################################
#
##############################################

if (PY_MODE == "BE") {
	
	HookList::getInstance()->registerCall(PY_HOOK_INITIALIZED, array(
	            Backend::getInstance(), "initialize"
	            ));
	    
    }
