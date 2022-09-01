<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Location;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class AppointmentServiceProvider
{
    public string $errorMessage;

    function prepareAppointmentsForJson(array $appointments, int $authUserId): array
    {
        $data = [];
        foreach ($appointments as $appointment) {
            $data[] = [
                "id" => $appointment->getId(),
                "user_name" => $appointment->getUser()->getName(),
                "date_start" => $appointment->getDateStart(),
                "location_address" => $appointment->getLocation()->getAddress(),
                "location_city" => $appointment->getLocation()->getCity(),
                "profile_picture" => $appointment->getUser()->getProfilePicture(),
                "isAppointmentFromLoggedInUser" => $this->isAppointmentFromLoggedInUser($appointment->getUser()->getId(), $authUserId),
            ];
        }
        return $data;
    }

    protected function isAppointmentFromLoggedInUser(int $userId, int $authUserId): bool
    {
        return $authUserId === $userId;
    }

    public function validate(Request $request, EntityManagerInterface $db, UserInterface $user): bool
    {
        $requestParsedBody = $request->request;

        $locationEntity = $db->getRepository(Location::class);

        $appointmentEntity = $db->getRepository(Appointment::class);

        if (date("Y-m-d") <= date("Y-m-d", strtotime($requestParsedBody->get('date_start')))) {

            $location = $locationEntity->findOneBy(['id' => $requestParsedBody->get('id_location')]);

            $maxCapacity = $location->getCapacity();

            $appointmentsMadeByThisUser = $appointmentEntity->count([
                'user' => $user,
                'date_start' => \DateTime::createFromFormat('Y-m-d', $requestParsedBody->get('date_start')),
            ]);

            $numberOfAppointmentsOnThisDay = $appointmentEntity->count([
                'date_start' => \DateTime::createFromFormat('Y-m-d', $requestParsedBody->get('date_start')),
                'location' => $location,
            ]);

            $capacityLeft = $maxCapacity - $numberOfAppointmentsOnThisDay;

            if ($appointmentsMadeByThisUser > 0) {
                $this->errorMessage = 'You already made an appointment on ' . $requestParsedBody->get('date_start');
                return false;
            } else if ($capacityLeft <= 0) {
                $this->errorMessage = 'The max capacity of this location ( ' . $maxCapacity . ' ) has already been exceeded!';
                return false;
            }
            return true;
        } else
            $this->errorMessage = "Undefined ERROR! We couldn't process this request!";
        return false;
    }
}
