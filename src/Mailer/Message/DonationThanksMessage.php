<?php

namespace AppBundle\Mailer\Message;

use AppBundle\Entity\Transaction;

final class DonationThanksMessage extends Message
{
    public static function createFromTransaction(Transaction $transaction): self
    {
        $donation = $transaction->getDonation();

        return new self(
            $donation->getUuid(),
            $donation->getEmailAddress(),
            $donation->getFullName(),
            'Merci pour votre engagement',
            [
                'target_firstname' => self::escape($donation->getFirstName()),
                'donation_amount' => $transaction->getDonation()->getAmountInEuros(),
            ]
        );
    }
}
