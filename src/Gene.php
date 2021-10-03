<?php

namespace Evolutive;

class Gene {
	private $value;
	private $mutagen;
	private $min;
	private $max;

	public function __construct($min = false, $max = false) {
		$this->value = .5;
		$this->min = $min;
		$this->max = $max;
		$this->mutagen = new Mutagen;
	}

	public function __clone() {
		$this->mutagen = clone $this->mutagen;
	}

	public function getValue() {
		if ($this->min !== false && $this->max !== false)
			return $this->scale($this->value, -1, 1, $this->min, $this->max);
		else
			return $this->value;
	}

	public function scale($valueIn, $baseMin, $baseMax, $limitMin, $limitMax) {
		return (($limitMax - $limitMin) * ($valueIn - $baseMin) / ($baseMax - $baseMin)) + $limitMin;
	}

	public function mutate() {
		$this->value = $this->mutagen->alterValue($this->value);
	}

	public function alterMutagen() {
		$this->mutagen->alter();
	}

	public function getDebug($isDebugMutagen = false) {
		return $this->getValue().($isDebugMutagen ? " ".$this->mutagen->getDebug() : null);
	}
}
