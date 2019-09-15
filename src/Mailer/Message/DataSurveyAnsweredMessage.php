<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Jecoute\DataSurvey;
use Ramsey\Uuid\Uuid;

final class DataSurveyAnsweredMessage extends Message
{
    public static function create(DataSurvey $dataSurvey): self
    {
        return new self(
            Uuid::uuid4(),
            $dataSurvey->getEmailAddress(),
            null,
            '',
            ['first_name' => $dataSurvey->getFirstName()]
        );
    }
}
