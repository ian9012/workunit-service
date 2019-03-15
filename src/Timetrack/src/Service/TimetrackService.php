<?php

namespace Timetrack\Service;

use Timetrack\Entity\Timetrack;
use Timetrack\Validator\TimetrackValidator;

class TimetrackService
{
    /**
     * @var Timetrack[]
     */
    private $collections = [];
    /**
     * @var TimetrackValidator
     */
    private $validator;
    public function __construct(TimetrackValidator $validator)
    {
        $this->validator = $validator;
    }

    public function get($id)
    {
        $index = array_search($id, array_column($this->collections, 'id'));
        if ($index !== 0 && $index === false) {
            throw new \Exception('Timetrack not exist of id : '.$id, 400);
        }

        return $this->collections[$index];
    }

    public function create(Timetrack $timetrack) : Timetrack
    {
        try {
            $timetrack->setId(rand(1, 9999));
            $timetrack->setDuration(preg_replace('/\s+/', '', $timetrack->getDuration()));
            $this->validator->validate($timetrack);
            $this->collections[] = $timetrack;
            return $timetrack;
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }
}
