<?php

use Timetrack\Validator\TimetrackValidator;
use Timetrack\Entity\Timetrack;

class TimetrackValidatorTest extends \Codeception\Test\Unit
{
    /**
     * @var TimetrackValidator
     */
    private $validator;
    protected function _before()
    {
        $this->validator = new TimetrackValidator($this->getConfig());
    }

    /**
     * @test
     * @dataProvider provideValidInteger
     */
    public function iShouldSeeValidationPassedIfIInputValidInteger($data)
    {
        $this->assertTrue($this->validator->isValidInteger($data));
    }

    /**
     * @test
     * @dataProvider provideInvalidInteger
     */
    public function iShouldSeeValidationFailingIfIInputInvalidInteger($data)
    {
        $this->assertFalse($this->validator->isValidInteger($data));
    }

    /**
     * @test
     * @dataProvider provideValidDate
     */
    public function iShouldSeeValidationPassedIfIInputValidDate($data)
    {
        $this->assertTrue($this->validator->isValidDate($data));
    }

    /**
     * @test
     * @dataProvider provideInvalidDate
     */
    public function iShouldSeeValidationFailingIfIInputInvalidDate($data)
    {
        $this->assertFalse($this->validator->isValidDate($data));
    }

    /**
     * @test
     * @dataProvider provideValidDescription
     */
    public function iShouldSeeValidationPassedIfIInputValidDescription($data)
    {
        $this->assertTrue($this->validator->isValidDescription($data));
    }

    /**
     * @test
     * @dataProvider provideInvalidDescription
     */
    public function iShouldSeeValidationFailingIfIInputInvalidDescription($data)
    {
        $this->assertFalse($this->validator->isValidDescription($data));
    }

    /**
     * @test
     * @dataProvider provideValidDuration
     */
    public function iShouldSeeValidationPassedIfIInputValidDuration($data)
    {
        $this->assertTrue($this->validator->isValidDuration($data));
    }

    /**
     * @test
     * @dataProvider provideNonExistingAndInvalidDuration
     */
    public function iShouldSeeValidationFailingIfIInputInvalidDuration($data)
    {
        $this->assertFalse($this->validator->isValidDuration($data));
    }



    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid id user.
     * @dataProvider provideInvalidTimetrackIdUser
     */
    public function iCannotCreateTimetrackWithInvalidIdUser(Timetrack $timetrack)
    {
        $this->validator->validate($timetrack);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid id workunit.
     * @dataProvider provideInvalidTimetrackIdWorkunit
     */
    public function iCannotCreateTimetrackWithInvalidIdWorkunit(Timetrack $timetrack)
    {
        $this->validator->validate($timetrack);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid date.
     * @dataProvider provideInvalidTimetrackDateObject
     */
    public function iCannotCreateTimetrackWithInvalidDate(Timetrack $timetrack)
    {
        $this->validator->validate($timetrack);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid description.
     */
    public function iCannotCreateTimetrackWithEmptyDescription()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDuration('8h');
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setIdWorkunit(rand(1, 9999));
        $this->validator->validate($timetrack);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Description should not be more than 250 characters.
     */
    public function iCannotCreateTimetrackWithMorethan250CharacterDescription()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDuration('8h');
        $timetrack->setDescription("Contrary to popular belief, Lorem Ipsum is not simply random text.
         It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.
          Richard McClintock, 
         a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words,
          consectetur,
          from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the
           undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\"
            (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory
             of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor
              sit amet..\", comes from a line in section 1.10.32.");
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setIdWorkunit(rand(1, 9999));
        $this->validator->validate($timetrack);
    }

    /**
     * @test
     * @expectedException \Exception
     * @expectedExceptionCode 400
     * @expectedExceptionMessage Invalid Duration.
     * @dataProvider provideNonExistingAndInvalidDurationObject
     */
    public function iCannotCreateTimetrackWithInvalidDuration(Timetrack $timetrack)
    {
        $this->validator->validate($timetrack);
    }

    public function provideValidInteger()
    {
        return [
            [1],
            ['1'],
            [1234],
            [99999],
        ];
    }

    public function provideInvalidInteger()
    {
        return [
            [-1],
            [null],
            ['hello world'],
            ['!@#$%%@'],
        ];
    }

    public function provideInvalidDate()
    {
        return [
            [date("Y-d-m")],
            [date("m-d-Y")],
            ['hello world'],
            [1234],
            [0]
        ];
    }

    public function provideValidDate()
    {
        return [
            [date("d-m-Y")],
            ['15-3-2019'],
            ['05-08-2019'],
        ];
    }

    public function provideValidDescription()
    {
        return [
            ["Contrary to popular belief, Lorem Ipsum is not simply random text."],
            ["Contrary to popular belief, Lorem Ipsum is not simply random text.
         It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.
          Richard McClintock, 
         a Latin professor at "]
        ];
    }

    public function provideInvalidDescription()
    {
        return [
            [null],
            ["Contrary to popular belief, Lorem Ipsum is not simply random text.
         It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old.
          Richard McClintock, 
         a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words,
          consectetur,
          from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the
           undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of \"de Finibus Bonorum et Malorum\"
            (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory
             of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, \"Lorem ipsum dolor
              sit amet..\", comes from a line in section 1.10.32."],
            ['']
        ];
    }

    public function provideNonExistingAndInvalidDuration()
    {
        return [
            [null],
            ['60m'],
            ['13h'],
            ['03h'],
            ['09m'],
            ['29h3m'],
            ['12h80m'],
            ['12h   80m'],
            ['!12h   !80m'],
            ['  !12h   !80m  '],
            ['@!!!!'],
            [12345666],
        ];
    }

    public function provideValidDuration()
    {
        return [
            ['59m'],
            ['1m'],
            ['7h3m'],
            ['12h12m'],
            ['1h35m'],
            ['4h59m']
        ];
    }

    public function provideNonexistingAndInvalidTimetrack()
    {
        return [
            [null],
            [999991],
            ['0'],
            [0],
            [-1],
            ['aeiou'],
            ['hello world'],
            ['!!!qqqq']
        ];
    }

    public function provideInvalidTimetrackIdUser()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDescription('Do testing');
        $timetrack->setDuration('8h');
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setIdWorkunit(rand(1, 9999));
        $timetrackNegativeIdUser = clone $timetrack;
        $timetrackNegativeIdUser->setIdUser(-1);
        $timetrackNoIdUser = clone $timetrackNegativeIdUser;
        $timetrackNoIdUser->setIdUser(null);
        $timetrackInvalidIdUser = clone $timetrackNoIdUser;
        $timetrackInvalidIdUser->setIdUser('hello world');

        return [
            [$timetrackNegativeIdUser],
            [$timetrackNoIdUser],
            [$timetrackInvalidIdUser],
        ];
    }

    public function provideInvalidTimetrackIdWorkunit()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDescription('Do testing');
        $timetrack->setDuration('8h');
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setIdWorkunit(rand(1, 9999));
        $timetrackNegativeIdWorkunit = clone $timetrack;
        $timetrackNegativeIdWorkunit->setIdWorkunit(-1);
        $timetrackNoIdWorkunit = clone $timetrackNegativeIdWorkunit;
        $timetrackNoIdWorkunit->setIdWorkunit(null);
        $timetrackInvalidIdWorkunit = clone $timetrackNoIdWorkunit;
        $timetrackInvalidIdWorkunit->setIdWorkunit('hello world');

        return [
            [$timetrackNegativeIdWorkunit],
            [$timetrackNoIdWorkunit],
            [$timetrackInvalidIdWorkunit]
        ];
    }

    public function provideInvalidTimetrackDateObject()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDescription('Do testing');
        $timetrack->setDuration('8h');
        $timetrack->setIdWorkunit(rand(1, 9999));
        $timetrackInvalidDate = clone $timetrack;
        $timetrackInvalidDate->setDate(date("Y-d-m"));
        $timetrackInvalidDate2 = clone $timetrackInvalidDate;
        $timetrackInvalidDate2->setDate('hello world');
        $timetrackInvalidDate3 = clone $timetrackInvalidDate2;
        $timetrackInvalidDate3->setDate(1234);

        return [
            [$timetrackInvalidDate],
            [$timetrackInvalidDate2],
            [$timetrackInvalidDate3]
        ];
    }

    public function provideNonExistingAndInvalidDurationObject()
    {
        $timetrack = new Timetrack();
        $timetrack->setIdUser(rand(1, 9999));
        $timetrack->setDuration('8h');
        $timetrack->setDate(date("d-m-Y"));
        $timetrack->setDescription('Do testing');
        $timetrack->setIdWorkunit(rand(1, 9999));
        $timetrackEmptyDuration = clone $timetrack;
        $timetrackEmptyDuration->setDuration(null);
        $timetrackInvalidDuration = clone $timetrackEmptyDuration;
        $timetrackInvalidDuration->setDuration('60m');
        $timetrackInvalidDuration2 = clone $timetrackEmptyDuration;
        $timetrackInvalidDuration2->setDuration('13h');
        $timetrackInvalidDuration3 = clone $timetrackInvalidDuration2;
        $timetrackInvalidDuration3->setDuration('29h3m');
        $timetrackInvalidDuration4 = clone $timetrackInvalidDuration3;
        $timetrackInvalidDuration4->setDuration('12h80m');
        $timetrackInvalidDuration5 = clone $timetrackInvalidDuration4;
        $timetrackInvalidDuration5->setDuration('12h   80m');
        $timetrackInvalidDuration6 = clone $timetrackInvalidDuration5;
        $timetrackInvalidDuration6->setDuration('!12h   !80m');
        $timetrackInvalidDuration7 = clone $timetrackInvalidDuration6;
        $timetrackInvalidDuration7->setDuration('@!!!!');
        $timetrackInvalidDuration8 = clone $timetrackInvalidDuration7;
        $timetrackInvalidDuration8->setDuration(12345666);

        return [
            [$timetrackInvalidDuration],
            [$timetrackInvalidDuration2],
            [$timetrackInvalidDuration3],
            [$timetrackInvalidDuration4],
            [$timetrackInvalidDuration6],
            [$timetrackInvalidDuration7],
            [$timetrackInvalidDuration8]
        ];
    }

    public function provideNonexistingAndInvalidTimetrackObject()
    {
        return [
            [null],
            [999991],
            ['0'],
            [0],
            [-1],
            ['aeiou'],
            ['hello world'],
            ['!!!qqqq']
        ];
    }

    private function getConfig()
    {
        return [
                'regex_duration' => [
                    'h' => [
                        'h_pattern' => '/^(\d)+h$/',
                        'valid_pattern' => '/^([1-9]|1[0-2])h$/'
                    ],
                    'm' => [
                        'm_pattern' => '/^(\d)+m$/',
                        'valid_pattern' => '/^([1-9]|[1-5][0-9])m$/'
                    ],
                    'h_m' => [
                        'h_m_pattern' => '/^(\d)+h(\d)+m$/',
                        'valid_pattern' => '/^([1-9]|1[0-2])h([1-9]|[1-5][0-9])m$/'
                    ],
                ]
            ];
    }
}
