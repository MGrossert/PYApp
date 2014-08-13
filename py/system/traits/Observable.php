<?php

trait Observable {
	private $_OBSERVERS = array();

	public function attachObserver(ObserverInterface $observer) {
		$this->_OBSERVERS[] = $observer;
	}

	public function detachObserver(ObserverInterface $observer) {
		$idx = array_search($observer, $this->_OBSERVERS);
		if ($idx) {
			unset($this->_OBSERVERS[$idx]);
		}
	}

	private function notifyObservers($notificationType = null) {
		foreach ($this->_OBSERVERS as $observer) {
			if (method_exists($observer, "onUpdate")) {
				$observer->onUpdate($this, $notificationType);
			}
		}
	}

}
