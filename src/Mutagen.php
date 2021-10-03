<?php

namespace TinCat\Evolutive;

class Mutagen {
	private $offset;

	public function __construct($offset = 0) {
		$this->offset = $offset;
	}

	public function alter() {
		$this->offset = $this->offset + (rand(-100000000000000, 100000000000000) / 100000000000000);
	}

	public function alterValue($value) {
		$value += $this->offset;
		return $value;
	}

	public function getDebug() {
		return "M".$this->offset;
	}
}
