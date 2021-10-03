<?php

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
