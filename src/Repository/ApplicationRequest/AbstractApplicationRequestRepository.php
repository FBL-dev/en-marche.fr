<?php

namespace AppBundle\Repository\ApplicationRequest;

use AppBundle\Entity\Adherent;
use AppBundle\Entity\ApplicationRequest\ApplicationRequest;
use AppBundle\Entity\ApplicationRequest\RunningMateRequest;
use AppBundle\Entity\ApplicationRequest\VolunteerRequest;
use AppBundle\Entity\ReferentTag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractApplicationRequestRepository extends ServiceEntityRepository
{
    private function createListQueryBuilder(string $alias): QueryBuilder
    {
        return $this->createQueryBuilder($alias)
            ->addSelect('tag')
            ->where("$alias.displayed = true")
            ->leftJoin("$alias.tags", 'tag')
            ->orderBy("$alias.createdAt", 'DESC')
        ;
    }

    /**
     * @var ReferentTag[]
     *
     * @return VolunteerRequest[]|RunningMateRequest[]
     */
    public function findForReferentTags(array $referentTags): array
    {
        return $this->createListQueryBuilder('r')
            ->innerJoin('r.referentTags', 'refTag')
            ->andWhere('refTag IN (:tags)')
            ->setParameter('tags', $referentTags)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return VolunteerRequest|RunningMateRequest|null
     */
    public function findOneByUuid(string $uuid): ?ApplicationRequest
    {
        return $this->findOneBy(['uuid' => $uuid]);
    }

    /**
     * @return VolunteerRequest[]|RunningMateRequest[]
     */
    public function findAllForInseeCodes(array $inseeCodes): array
    {
        $this->addFavoriteCitiesCondition($inseeCodes, $qb = $this->createListQueryBuilder('r'));

        return $qb->getQuery()->getResult();
    }

    public function countForInseeCodes(array $inseeCodes): int
    {
        $qb = $this->createQueryBuilder('r')
            ->select('COUNT(1)')
            ->where('r.displayed = true')
        ;

        $this->addFavoriteCitiesCondition($inseeCodes, $qb);

        return (int) $qb->getQuery()->getSingleScalarResult();
    }

    public function countTakenFor(array $inseeCodes): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(1)')
            ->where('r.displayed = true')
            ->andWhere('r.takenForCity IN (:cities)')
            ->setParameter('cities', $inseeCodes)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * @return VolunteerRequest[]|RunningMateRequest[]
     */
    public function findAllTakenFor(array $inseeCodes): array
    {
        return $this->createListQueryBuilder('r')
            ->andWhere('r.takenForCity IN (:cities)')
            ->setParameter('cities', $inseeCodes)
            ->getQuery()
            ->getResult()
        ;
    }

    public function updateAdherentRelation(string $email, ?Adherent $adherent): void
    {
        $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'candidate')
            ->where('candidate.emailAddress = :email')
            ->set('candidate.adherent', ':adherent')
            ->setParameter('email', $email)
            ->setParameter('adherent', $adherent)
            ->getQuery()
            ->execute()
        ;
    }

    public function hideDuplicates(ApplicationRequest $request): void
    {
        $this->_em->createQueryBuilder()
            ->update($this->_entityName, 'candidate')
            ->where('candidate.id != :id')
            ->andWhere('candidate.emailAddress = :email')
            ->andWhere('candidate.displayed = true')
            ->set('candidate.displayed', 0)
            ->setParameter('id', $request->getId())
            ->setParameter('email', $request->getEmailAddress())
            ->getQuery()
            ->execute()
        ;
    }

    private function addFavoriteCitiesCondition(array $inseeCodes, QueryBuilder $qb): void
    {
        $orExpression = new Orx();

        foreach ($inseeCodes as $key => $code) {
            $orExpression->add(":codes_$key = ANY_OF(string_to_array(r.favoriteCities, ','))");
            $qb->setParameter("codes_$key", $code);
        }

        $qb->andWhere($orExpression);
    }
}
