<?php

namespace AppBundle\Entity\IdeasWorkshop;

use Algolia\AlgoliaSearchBundle\Mapping\Annotation as Algolia;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ideas_workshop_idea_notification_dates")
 *
 * @Algolia\Index(autoIndex=false)
 */
class IdeaNotificationDates
{
    /**
     * @var int|null
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastDate;

    /**
     * @var \DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $cautionLastDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastDate(): ?\DateTimeInterface
    {
        return $this->lastDate;
    }

    public function setLastDate(\DateTimeInterface $lastDate = null): void
    {
        $this->lastDate = $lastDate;
    }

    public function getCautionLastDate(): ?\DateTimeInterface
    {
        return $this->cautionLastDate;
    }

    public function setCautionLastDate(\DateTimeInterface $cautionLastDate = null): void
    {
        $this->cautionLastDate = $cautionLastDate;
    }
}
