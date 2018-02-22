<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Adherent;
use Ramsey\Uuid\Uuid;

final class CitizenProjectContactActorsMessage extends Message
{
    public static function create(Adherent $host, array $recipients, string $subject, string $content): self
    {
        if (!$recipients) {
            throw new \InvalidArgumentException('At least one recipient is required.');
        }

        $recipient = array_shift($recipients);
        if (!$recipient instanceof Adherent) {
            throw new \InvalidArgumentException(sprintf('This message builder requires a collection of %s instances', Adherent::class));
        }

        $message = new self(
            Uuid::uuid4(),
            $recipient->getEmailAddress(),
            $recipient->getFullName(),
            static::getTemplateVars($host, $subject, $content),
            [],
            $host->getEmailAddress()
        );

        $message->setSenderName($host->getFullName());

        foreach ($recipients as $recipient) {
            if (!$recipient instanceof Adherent) {
                throw new \InvalidArgumentException(sprintf('This message builder requires a collection of %s instances', Adherent::class));
            }

            $message->addRecipient(
                $recipient->getEmailAddress(),
                $recipient->getFullName()
            );
        }

        return $message;
    }

    private static function getTemplateVars(Adherent $host, string $subject, string $content): array
    {
        return [
            'citizen_project_host_firstname' => self::escape($host->getFirstName()),
            'citizen_project_host_subject' => $subject,
            'citizen_project_host_message' => $content,
        ];
    }
}
