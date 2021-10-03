<?php

namespace Evolutive;

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
