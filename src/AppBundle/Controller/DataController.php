<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Emergency;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Volunteer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/api/v1")
 */
class DataController extends AppController
{
    /**
     * @Route("/notifications/{email}", name="api_notification_list")
     * @Method("GET")
     */
    public function listNotificationsAction(Volunteer $volunteer)
    {
        $qb = $this->em()->getRepository(Notification::class)
            ->createQueryBuilder('v')
            ->leftJoin('v.enrolment', 'e')
            ->where('v.volunteer = :volunteer')
            ->orWhere('e.volunteer = :volunteer')
            ->orWhere('e.volunteer = :volunteer')
            ->setParameter('volunteer', $volunteer)
        ;
        $data = $qb->getQuery()->getResult();

        $qb->set('v.sent', new \DateTime())->getQuery()->execute();

        return $this->renderJSON($data);
    }

    /**
     * @Route("/notifications/{id}/read", name="api_notification_list")
     * @Method("GET")
     */
    public function listNotificatioReadAction(Notification $notification)
    {
        $notification->setRead(new \DateTime());
        $this->update($notification);

        return $this->renderJSON(true);
    }

    /**
     * @Route("/volunteer", name="api_volunteer_list")
     * @Method("GET")
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function listVolunteersAction()
    {
        $data = $this->em()->getRepository(Volunteer::class)->findAll();
        return $this->renderJSON($data);
    }

    /**
     * @Route("/emergencies", name="api_emergency_list")
     * @Method("GET")
     */
    public function listEmergenciesAction()
    {
        $data = $this->em()->getRepository(Emergency::class)->findAll();
        return $this->renderJSON($data);
    }
}
