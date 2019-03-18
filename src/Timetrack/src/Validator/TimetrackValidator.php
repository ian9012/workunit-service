<?php

namespace Timetrack\Validator;

use Timetrack\Entity\Timetrack;

class TimetrackValidator
{
    private $config;

    public function __construct($config)
    {
        if (!isset($config['timetrack_validator'])) {
            throw new \Exception('Config instance of timetrack_validator needed');
        }
        $this->config = $config['timetrack_validator'];
    }

    public function validate(Timetrack $timetrack)
    {
        if (!$this->isValidInteger($timetrack->getIdUser())) {
            throw new \Exception('Invalid id user.', 400);
        }

        if (!$this->isValidInteger($timetrack->getIdWorkunit())) {
            throw new \Exception('Invalid id workunit.', 400);
        }

        if (!$this->isValidDate($timetrack->getDate())) {
            throw new \Exception('Invalid date.', 400);
        }

        if (empty($timetrack->getDescription())) {
            throw new \Exception('Invalid description.', 400);
        }

        if (!$this->isValidDescription($timetrack->getDescription())) {
            throw new \Exception('Description should not be more than 250 characters.', 400);
        }

        if (!$this->isValidDuration($timetrack->getDuration())) {
            throw new \Exception('Invalid Duration.', 400);
        }
    }

    /**
     * @param $data
     * @return bool
     */
    public function isValidInteger($data): bool
    {
        return !empty($data) && filter_var($data, FILTER_VALIDATE_INT) && $data > 0;
    }

    /**
     * @param $date
     * @return bool
     */
    public function isValidDate($date): bool
    {
        return !empty($date) && $this->validateDate($date);
    }

    /**
     * @param $description
     * @return bool
     */
    public function isValidDescription($description): bool
    {
        return !empty($description) && strlen($description) <= 250;
    }

    /**
     * Must match the pattern :
     * ?h,
     * ?m,
     * ?h?m,
     * where hour(h) cannot be more than 12 and minute(m) cannot be more than 59
     * @param $duration
     * @return mixed
     */
    public function isValidDuration($duration)
    {
        $regexDuration = $this->config['regex_duration'];
        //check pattern
        // if pattern ?h
        if (preg_match($regexDuration['h']['h_pattern'], $duration) === 1) {
            return preg_match($regexDuration['h']['valid_pattern'], $duration) === 1;
        }
        // if pattern ?m
        if (preg_match($regexDuration['m']['m_pattern'], $duration) === 1) {
            return preg_match($regexDuration['m']['valid_pattern'], $duration) === 1;
        }
        // if pattern ?h?m
        if (preg_match($regexDuration['h_m']['h_m_pattern'], $duration) === 1) {
            return preg_match($regexDuration['h_m']['valid_pattern'], $duration) === 1;
        }

        return false;
    }

    private function validateDate($date)
    {
        $dateExploded = explode("-", $date);

        if (count($dateExploded) != 3) {
            return false;
        }

        $day = $dateExploded[0];
        $month = $dateExploded[1];
        $year = $dateExploded[2];

        if (!checkdate($month, $day, $year)) {
            return false;
        }

        return true;
    }
}
