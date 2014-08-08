<?php
##############################################
# register Services
##############################################

$system->service()->prepare("Log", '\ErrorHandlerInterface', new PY\ErrorHandler());
