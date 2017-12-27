<?php

/**
 * A library to implement simple evolutive, genetics-based machine learning algorithms
 * by Tin.cat
 */

namespace Evolutive;

class Nursery {
	public function evolve($setup) {
		if (!$setup["specimensPerGeneration"])
			$setup["specimensPerGeneration"] = 10;
		if (!$setup["generations"])
			$setup["generations"] = 10;
		if (!$setup["maxAttemptsPerGeneration"])
			$setup["maxAttemptsPerGeneration"] = 3;

		$this->specimenClassName = $setup["specimenClassName"];
		$this->specimensPerGeneration = $setup["specimensPerGeneration"];
		$this->generations = $setup["generations"];
		$this->runParameters = $setup["runParameters"];

		// Create a specimen here to get the default dna
		$baseSpecimen = $this->createSpecimen();
		$previousBreed = false;
		$attemptsCount = 0;
		for ($generation = 1; $generation <= $setup["generations"]; $generation ++) {
			$breed = new Breed($baseSpecimen, $this->specimensPerGeneration, $setup["runParameters"]);
			if ($setup["isDebug"]) {
				echo "Generation #".$generation."-".$attemptsCount."\n".$breed->getDebug();
				echo "Fittest specimen: ".$breed->getFittestSpecimen()->getFitness()."\n";
			}
			if ($previousBreed) {
				if ($breed->getFittestSpecimen()->getFitness() < $previousBreed->getFittestSpecimen()->getFitness()) {
					if ($attemptsCount < $setup["maxAttemptsPerGeneration"]) {
						$attemptsCount ++;
						$generation --;
						continue;
					}
				}
			}
			$previousBreed = $breed;
			$baseSpecimen = $breed->getFittestSpecimen();
			$attemptsCount = 0;
		}
        return $baseSpecimen;
	}

	private function createSpecimen() {
		return new $this->specimenClassName();
	}
}

class Breed {
	private $specimens;

	// Creates a Breed with the specified number of specimens, all genetically related to the provided $baseSpecimen
	public function __construct($baseSpecimen, $numberOfSpecimens, $runParameters) {
		for ($i = 0; $i < $numberOfSpecimens; $i ++)
			$this->specimens[] = $baseSpecimen->breed($runParameters);
	}

	public function getFittestSpecimen() {
		$bestFit = null;
		$bestFitSpecimen = false;
		foreach ($this->specimens as $specimen) {
			$fitness = $specimen->getFitness();
			if ($fitness > $bestFit || $bestFit == null) {
				$bestFit = $fitness;
				$bestFitSpecimen = $specimen;
			}
		}
		reset($this->specimens);
		return $bestFitSpecimen;
	}

	public function getAverageFitness() {
		foreach ($this->specimens as $specimen)
			$fitness += $specimen->getFitness();
		reset($this->specimens);
		return $fitness / sizeof($this->specimens);
	}

	public function getDebug() {
		foreach ($this->specimens as $specimen)
			$r .= $specimen->getDebug()."\n";
		return $r;
	}
}

class Specimen {
	public $dna;

	public function Specimen($dna = false) {
		if ($dna)
			$this->dna = $dna;
	}

	public function breed($runParameters = false) {
		$son = clone $this;
		$son->alterMutagen();
		$son->mutate();
		if ($runParameters)
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
		if ($this->min && $this->max)
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