<?php
##############################################
#
##############################################

$callable = array(
    Backend::getInstance(),
    "initialize"
);

$system->service()->get('hook')->register(PY_HOOK_INITIALIZED, $callable);
