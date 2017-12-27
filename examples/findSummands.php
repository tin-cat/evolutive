<?php

	/**
	 * Example of Evolutive https://github.com/tin-cat/evolutive, A library to implement simple evolutive, genetics-based machine learning algorithms by Tin.cat
	 * Using an evolutive, genetics-based machine learning algorithm to find the two sumands that give a specific result.
	 */

	header("Content-type: text/plain; charset=utf-8");
	include "../evolutive.class.php";

	class FindSummandsSpecimen extends Evolutive\Specimen {
		private $result = false;
		public $desiredResult = 10;
		
		public function FindSummandsSpecimen($dna = false) {
			if ($dna)
				$this->dna = $dna;
			else
				$this->dna = new Evolutive\Dna([
					"summandA" => new Evolutive\Gene($this->desiredResult * -1, $this->desiredResult),
					"summandB" => new Evolutive\Gene($this->desiredResult * -1, $this->desiredResult),
				]);
		}

		public function getFitness() {
			$sum = $this->getDna()->getGene("summandA")->getValue() + $this->getDna()->getGene("summandB")->getValue();
			if ($this->desiredResult > $sum)
				$delta = $this->desiredResult - $sum;
			else
				$delta = $sum - $this->desiredResult;
			return $this->desiredResult - $delta;
		}
	}

	$nursery = new Evolutive\Nursery;
	$specimen = $nursery->evolve([
		"isDebug" => true,
		"specimenClassName" => "FindSummandsSpecimen",
		"specimensPerGeneration" => 10,
		"generations" => 1000,
		"maxAttemptsPerGeneration" => 10
	]);

	echo "Solution found by the fittest specimen: ".$specimen->getDna()->getGene("summandA")->getValue()." + ".$specimen->getDna()->getGene("summandB")->getValue()." = ".($specimen->getDna()->getGene("summandA")->getValue() + $specimen->getDna()->getGene("summandB")->getValue())." ≈ ".$specimen->desiredResult;