<?php

namespace TinCat\Evolutive;

class Specimen {
	protected $dna;
	protected $baseDna;

	public function Specimen($dna = false) {
		$this->dna = $dna ? $dna : $this->baseDna;
	}

	public function breed($runParameters = false) {
		$son = clone $this;
		$son->alterMutagen();
		$son->mutate();
		$son->run($runParameters);
		return $son;
	}

	public function __clone() {
		$this->dna = clone $this->dna;
	}

	public function mutate() {
		$this->getDna()->mutate();
	}

	public function alterMutagen() {
		$this->GetDna()->alterMutagen();
	}

	public function getDna() {
		return $this->dna;
	}

	// Executes any code that defines the specimen's reason to live. Overloaded implementations can accept many parameters, that are configured by passing the "runParameters" setup key to Nursery::evolve
	public function run($parameters) {}

	// Returns a value that expresses how fit this specimen is, where higher values mean better fitness.
	public function getFitness() {}

	public function getDebug() {
		return get_class($this).": ".$this->dna->getDebug()." Fitness:".$this->getFitness();
	}
}
