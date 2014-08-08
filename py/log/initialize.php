<?php
##############################################
# register Services
##############################################

$system->service()->prepare("Log", '\LoggerInterface', new PY\Logger());
