<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Emergency;
use AppBundle\Entity\Skill;
use Doctrine\Common\Collections\Collection;

/**
 * VolunteerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VolunteerRepository extends \Doctrine\ORM\EntityRepository
{
    public function findBySkills($kills)
    {
        return $this->createQueryBuilder("vol")
            ->innerJoin("vol.skills", 'skills')
            ->where("skills IN (:skills)")->setParameter('skills', $kills)
            ->getQuery()
            ->execute();
    }

    public function findUnenrolledBySkills(Emergency $emergency, Collection $kills)
    {

        $ids = $this->getEnrolledVolunteersIds($emergency);

        $ids = count($ids) ? $ids : [-1];

        return $this->createQueryBuilder("vol")
            ->innerJoin("vol.skills", 'skills')
            ->where("skills IN (:skills)")->setParameter('skills', $kills)
            ->andWhere('vol.id NOT IN (:exclude)')->setParameter('exclude', $ids)
            ->getQuery()
            ->execute();
    }

    public function getEnrolledVolunteersIds(Emergency $emergency)
    {

        $results = $this->createQueryBuilder("vol")
            ->select("vol.id")
            ->innerJoin("vol.enrolments", 'enrol')
            ->where("enrol.emergency = :emergency")->setParameter('emergency', $emergency)
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'id');

    }

}
