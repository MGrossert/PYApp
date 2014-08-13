<?php
##############################################
#
##############################################

$callable = array(
    Backend::getInstance(),
    "execute"
);

$system->service()->get('hook')->register(PY_HOOK_INITIALIZED, $callable);
