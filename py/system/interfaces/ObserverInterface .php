<?php

interface ObserverInterface {

	abstract function onUpdate($object, $notificationType);

}
