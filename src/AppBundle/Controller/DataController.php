<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Emergency;
use AppBundle\Entity\Notification;
use AppBundle\Entity\Report;
use AppBundle\Entity\Volunteer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $em = $this->em();
        $qb = $em->getRepository(Notification::class)
            ->createQueryBuilder('v')
            ->leftJoin('v.enrolment', 'e')
            ->where('v.volunteer = :volunteer')
            ->orWhere('e.volunteer = :volunteer')
            ->orWhere('e.volunteer = :volunteer')
            ->andWhere('v.seen IS NULL')
            ->setParameter('volunteer', $volunteer)
        ;
        $data = $qb->getQuery()->getResult();

        /** @var Notification $item */
        foreach ($data as $item) {
            $item->setSent(new \DateTime());
            $em->persist($item);
        }
        $em->flush();

        return $this->renderJSON($data);
    }

    /**
     * @Route("/notifications/{id}/read", name="api_notification_read")
     * @Method("GET")
     */
    public function listNotificationReadAction(Notification $notification)
    {
        $notification->setSeen(new \DateTime());
        $this->update($notification);

        return $this->renderJSON(true);
    }

    /**
     * @Route("/notifications/{id}/accept", name="api_notification_accept")
     * @Method("GET")
     */
    public function listNotificationAcceptAction(Notification $notification)
    {
        $enrollment = $notification->getEnrolment();
        $enrollment->setCanDoIt(true);
        $enrollment->setCanDoItDateTime(new \DateTime());
        $this->update($enrollment);

        return $this->renderJSON(true);
    }

    /**
     * @Route("/notifications/{id}/reject", name="api_notification_reject")
     * @Method("GET")
     */
    public function listNotificationRejectAction(Notification $notification)
    {
        $enrollment = $notification->getEnrolment();
        $enrollment->setCanDoIt(false);
        $enrollment->setCanDoItDateTime(new \DateTime());
        $this->update($enrollment);

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

    /**
     * @Route("/reports", name="api_reports_list")
     * @Method("GET")
     */
    public function listReportsAction()
    {
        $data = $this->em()->getRepository(Report::class)->findAll();
        return $this->renderJSON($data);
    }

    /**
     * Lists all report entities.
     *
     * @Route("/import", name="api_report_import")
     * @Method("GET")
     */
    public function importFromApp (Request $request) {

        $report = new Report();

        $report->setDescription(
            $request->get("description")
        );

        $report->setLng(
            $request->get("lng")
        );

        $report->setLat(
            $request->get("lat")
        );

        if($request->get("date")) {
            $report->setCreated(
                new \DateTime(date('Y-m-d H:i:s', round($request->get("date")/1000)))
            );
        }
        $report->setIp(
            $request->getClientIp()
        );

        $report->setUserAgent(
            $request->headers->get('User-Agent')
        );


        $this->em()->persist($report);
        $this->em()->flush();

        return $this->renderJSON([]);
    }
}
