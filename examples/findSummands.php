<?php

	/**
	 * Example of Evolutive https://github.com/tin-cat/evolutive, A library to implement simple evolutive, genetics-based machine learning algorithms by Tin.cat
	 * Using an evolutive, genetics-based machine learning algorithm to find the two sumands that give a specific result.
	 */

	header("Content-type: text/plain; charset=utf-8");
	include "../evolutive.class.php";

	class FindSummandsSpecimen extends Evolutive\Specimen {
		public $desiredResult = 10;
		public $result;
		
		public function FindSummandsSpecimen($dna = false) {
			$this->baseDna = new Evolutive\Dna([
				"summandA" => new Evolutive\Gene(),
				"summandB" => new Evolutive\Gene(),
			]);
			parent::Specimen($dna);
		}

		public function run($parameters) {
			$this->result = $this->getDna()->getGene("summandA")->getValue() + $this->getDna()->getGene("summandB")->getValue();
		}

		public function getFitness() {
			if ($this->desiredResult > $this->result)
				$delta = $this->desiredResult - $this->result;
			else
				$delta = $this->result - $this->desiredResult;
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