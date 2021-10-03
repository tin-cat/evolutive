<?php

namespace Evolutive;

class Dna {
	private $genes;

	public function __construct($genes) {
		$this->genes = $genes;
	}

	public function __clone() {
		$oldGenes = $this->genes;
		$this->genes = [];
		foreach ($oldGenes as $geneName => $gene)
			$this->genes[$geneName] = clone $gene;
	}

	public function mutate() {
		foreach ($this->genes as $gene)
			$gene->mutate();
		reset($this->genes);
	}

	public function alterMutagen() {
		foreach ($this->genes as $gene)
			$gene->alterMutagen();
		reset($this->genes);
	}

	public function getGene($geneName) {
		return $this->genes[$geneName];
	}

	public function getDebug() {
		if (!is_array($this->genes))
			return "No genes";
		foreach ($this->genes as $geneName => $gene)
			$r .= "[".$geneName.":".$gene->getDebug()."] ";
		reset($this->genes);
		return $r;
	}
}
