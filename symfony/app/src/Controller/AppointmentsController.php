<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Location;
use App\Service\AppointmentServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AppointmentsController extends MyAbstractController
{
    #[Route('/date', name: 'date', methods: ['GET'])]
    public function getAppointments(Request $request, EntityManagerInterface $db, AppointmentServiceProvider $appointmentServiceProvider): JsonResponse
    {
        $requestParams = $request->query;

        $appointments = $db->getRepository(Appointment::class)->findBy([
            'date_start' => \DateTime::createFromFormat('Y-m-d', $requestParams->get('date')),
            'location' => $requestParams->get('locationId'),
        ]);

        return new JsonResponse($appointmentServiceProvider->prepareAppointmentsForJson($appointments));
    }

    #[Route('/date', name: 'datePost', methods: ['POST'])]
    public function setAppointment(Request $request, EntityManagerInterface $db, AppointmentServiceProvider $appointmentServiceProvider): Response
    {
        $requestBody = $request->request;

        $location = $db->getRepository(Location::class)->find(['id' => $requestBody->get('id_location')]);

        $user = $this->getUser();

        if (!$appointmentServiceProvider->validate($request)) {
            return new JsonResponse($appointmentServiceProvider->errorMessage);
        }

        $appointment = new Appointment();

        $appointment->setUser($user);
        $appointment->setDateStart(\DateTime::createFromFormat('Y-m-d', $requestBody->get('date_start')));
        $appointment->setLocation($location);

        $user->setProfilePicture("assets/images/pp1.jpg");

        $db->persist($appointment);
        $db->flush();

        return new JsonResponse(['state' => 'good']);
    }

    #[Route('/deleteAppointment', name: 'delete_appointment')]
    public function deleteAppointment(Request $request, EntityManagerInterface $db): Response
    {
        $requestParams = $request->query;

        $appointment = $db->getRepository(Appointment::class)->find($requestParams->get('appointmentId')) ?? null;

        if (strtotime($requestParams->get('appointmentDate')) < strtotime(date("Y-m-d"))) {
            return new JsonResponse("You can't delete appointments from the past!");
        }

        if ($appointment !== null) {
            $db->remove($appointment);
            $db->flush();
            return new JsonResponse(['state' => 'good']);
        }

        return new JsonResponse('We could not remove the appointment!');
    }
}