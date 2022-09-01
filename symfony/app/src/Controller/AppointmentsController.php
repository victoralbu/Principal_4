<?php

namespace App\Controller;

use App\Entity\Appointment;

use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AppointmentsController extends MyAbstractController
{
    #[Route('/date', name: 'date', methods: ['GET'])]
    public function getAppointments(Request $request, EntityManagerInterface $db): JsonResponse
    {
        $requestQueryParams = $request->query;

        $appointments = $db->getRepository(Appointment::class)->findBy([
            'date_start' => \DateTime::createFromFormat('Y-m-d', $requestQueryParams->get('date')),
            'location' => $requestQueryParams->get('locationId'),
        ]);

        $appointmentServiceProvider = new AppointmentServiceProvider();
        return new JsonResponse($appointmentServiceProvider->prepareAppointmentsForJson($appointments, $this->getUser()->getId()));
    }

    #[Route('/date', name: 'datePost', methods: ['POST'])]
    public function setAppointment(Request $request, EntityManagerInterface $db): Response|null
    {
        $requestParsedBody = $request->request;

        $location = $db->getRepository(Location::class)->find(['id' => $requestParsedBody->get('id_location')]);

        $user = $this->getUser();

        $appointmentServiceProvider = new AppointmentServiceProvider();

        if ($appointmentServiceProvider->validate($request, $db, $user)) {

            $appointment = new Appointment();

//            $appointment->fill([
//                "user" => $user,
//                'date_start' => "s",
//                'date_end' => \DateTime::createFromFormat('Y-m-d', $requestParsedBody['date_end']),
//                "location" => $location,
//            ]);

            $appointment->setUser($user);
            $appointment->setDateStart(\DateTime::createFromFormat('Y-m-d', $requestParsedBody->get('date_start')));
            $appointment->setLocation($location);

            $user->setProfilePicture("assets/images/pp1.jpg");

            $db->persist($appointment);
            $db->flush();

            return new JsonResponse(['state' => 'good']);
        }
        return new JsonResponse($appointmentServiceProvider->errorMessage);
    }
}

