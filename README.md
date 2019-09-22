# Evolutive
A library to learn about evolutive, genetics-based machine learning algorithms and object-oriented programming by [Tin.cat](https://tin.cat).

## The entities
Evolutive is built upon the following classes that represent the main entities that take part in the mechanism:
- **Nursery** Performs all the breeding of Specimens, simulating an evolutive process.
- **Breed** Represents a group of Specimens that have been breed from an original Dna.
- **Specimen** Represents a unique Specimen with a specific Dna.
- **Dna** Represents all the differential characteristics of the Specimen it belongs to. It holds Genes.
- **Gene** Represents one characteristic of a Specimen. It holds a Mutagen.
- **Mutagen** Represents the external influence that causes Specimens to be different from each other by mutating a gene. Mutagens change Genes in a specific way (an offset) each time the Gene is mutated (mutation occurs each time a Specimen is breeded). The simulated evolutive process tries to keep Mutagens that are known to cause better fitness, and mutates Mutagens that are less fitted.

## The process
1. **The zero specimen** A first specimen is created that will be used as the starting point for breeding.
2. **Generation breeding** Generations of specimens are breed one at a time, using the best fitted Specimen from the previous generation as the base Specimen for the next one. For the first generation, the zero specimen is used as the base Specimen.
3. **Evolution at work** Each Specimen in a generation is a mutation from the generation's base Specimen. If the best fitted Specimen from a given generation is less fitted than the previous generation's, the entire generation is discarded and breed again. This only can happen an specific maximum number of times.
4. **Mutagen** Mutagens mutate Genes in a specific way. When a Mutagen is altered, the Gene will be mutated in a randomly different way (to generate diversity). An unaltered Mutagen will mutate the Gene in the same way (to keep Mutagens that are believed to produce better fitted Specimens). Mutagens are altered for each new breeded Specimen.

## How to use
Let's consider the example provided in examples/findSummands.php, where we want to code an evolutive algorithm that finds the two numbers which, when added, give a specific result:
1. First, create a class for your specimen extended from the provided Evolutive\Specimen class, like so:
```php
class FindSummandsSpecimen extends Evolutive\Specimen {
	public $desiredResult = 10;
	private $result;
}
```
> In our example, since our purpose is to find two numbers that give a specific result when added, we setup this desired result as the $desiredResult class property. We also add a $result property that will hold this Specimen's specific solution to the problem.
2. Specify the DNA structure of your Specimens by assigning the baseDna property in the class constructor. Use the Evolutive\Dna class to do it, and don't forget to call the parent's constructor afterwards, like so:
```php
class FindSummandsSpecimen extends Evolutive\Specimen {
	public $desiredResult = 10;
	private $result;

	public function FindSummandsSpecimen($dna = false) {
		$this->baseDna = new Evolutive\Dna();
		parent::Specimen($dna);
	}
}
```
3. Setup the Genes for your base Dna passing a hash array of Evolutive\Gene objects to the Evolutive\Dna constructor, use the keys of the array to assign each Gene a name:
```php
class FindSummandsSpecimen extends Evolutive\Specimen {
	public $desiredResult = 10;
	private $result;

	public function FindSummandsSpecimen($dna = false) {
		$this->baseDna = new Evolutive\Dna([
			"summandA" => new Evolutive\Gene(),
			"summandB" => new Evolutive\Gene(),
		]);
		parent::Specimen($dna);
	}
}
```
> In our example, since we want to find two numbers, we consider each number to be a Gene in our Specimen, but this is only one way of approaching this specific problem.

> Note that Genes can take two optional arguments that represent the minimum and maximum numeric value that the specific Gene can take. This might come in handy when you need Gene values to represent a discrete indicators, that is: Genes that can only take a value within certain limits.

4. Each Specimen must have some function that represents its reason of being. In our example, that would be the function that adds up the two summands and stores the result. We must implement that function by creating a run() method in our Specimen:
```php
class FindSummandsSpecimen extends Evolutive\Specimen {
	public $desiredResult = 10;
	private $result;

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
}
```
> The run method can accept an array of parameters to perform its calculations. This parameters are passed when calling Nursery::evolve, although we don't need them for our example.
5. To finalize our Specimen, we need to implement the getFitness method, which must return a value representing how much fitted is this Specimen, where bigger numbers represent better fitted Specimens. We do this like so:
```php
class FindSummandsSpecimen extends Evolutive\Specimen {
	public $desiredResult = 10;
	private $result;

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
```
> In our case, we could say that a better Specimen is the one that has discovered the best approximation to the desired sum result. Thus, in our getFitness method we return a value that represents how close this Specimen was to the desired result (not the difference between the specimen's result and the desired result, but its inverse, since higher values represent better fitness)
> The returned fitness values can be in any range and scale, as long as better fitness is represented with higher values.
6. Lastly, we make use of the Evolutive\Nursery class to run the evolutive process, like so:
```php
$nursery = new Evolutive\Nursery;
$specimen = $nursery->evolve([
	"isDebug" => true,
	"specimenClassName" => "FindSummandsSpecimen",
	"specimensPerGeneration" => 10,
	"generations" => 1000,
	"maxAttemptsPerGeneration" => 10
]);
```
> The returned $specimen will be an evolved Specimen that has hopefully reached a great ability to solve our problem.

When calling Evolutive\Nursery::evolve, these are the parameters:
- **isDebug** Outputs information about the whole process to the standard output.
- **specimenClassName** The name of the class of your Specimen.
- **specimensPerGeneration** The number of Specimens that will be breed for each generation.
- **generations** The number of generations that will be breed.
- **maxAttemptsPerGeneration** When a generation is less fitted than the previous one, it will be breed again to try to find a best fitted one this many maximum times.

## Final considerations
Take into account that evolutive genetic machine-learning algorithms are not suited to find specific results like the two summands of our example, as it will almost always find only approximate results. In that example, even with a really high number of iterations, you'll never reach the certainty of always obtaining a precise result. (A simple try-and-guess recursion algorithm might be far more effective)

It is up to you to find meaningful or fun applications for this kind of algorithms. What about using a genetic evolutive algorithm to determine whether to buy or sell stocks at a given time?