<?php

use App\Training\Factories\TrainingStepFactory;
use Database\Seeders\TopWordSeeder;
use Tests\TestCase;
use App\Training\Models\Training;
use App\Models\Dictionary;
use App\Models\TopWord;
use App\Training\Enums\TrainingStepType;
use App\Training\Steps\ChooseCorrectAnswerStep;
use App\Training\Steps\WriteAnswerStep;
use App\Training\Steps\EstablishComplianceStep;
use Mockery;
use Illuminate\Support\Collection;

class TrainingStepFactoryTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;
    private TrainingStepFactory $factory;
    private Training $training;
    private Dictionary $dictionary;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new TrainingStepFactory();

        $this->dictionary = Dictionary::factory()->make(['id' => 1, 'language_from_id' => 1, 'language_to_id' => 2]);

        $this->training = new Training(['id' => 1]);
        $this->training->setRelation('dictionary', $this->dictionary);

        $this->seed(TopWordSeeder::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateChooseCorrectAnswer()
    {
        $step = $this->factory->create($this->training, TrainingStepType::ChooseCorrectAnswer);
        $this->assertInstanceOf(ChooseCorrectAnswerStep::class, $step);
    }

    public function testCreateWriteAnswer()
    {
        $step = $this->factory->create($this->training, TrainingStepType::WriteCorrectAnswer);
        $this->assertInstanceOf(WriteAnswerStep::class, $step);
    }

    public function testCreateEstablishCompliance()
    {
        $step = $this->factory->create($this->training, TrainingStepType::EstablishCompliance);
        $this->assertInstanceOf(EstablishComplianceStep::class, $step);
    }

}
