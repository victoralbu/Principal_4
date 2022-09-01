<?php declare(strict_types=1);

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Location;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

class AppointmentServiceProvider
{
    public string $errorMessage;

    public function __construct(protected Security $security, protected EntityManagerInterface $db)
    {
    }

    function prepareAppointmentsForJson(array $appointments): array
    {
        $loggedUserId = $this->security->getUser()->getId();

        $data = [];
        foreach ($appointments as $appointment) {
            $data[] = [
                "id" => $appointment->getId(),
                "user_name" => $appointment->getUser()->getName(),
                "date_start" => $appointment->getDateStart(),
                "location_address" => $appointment->getLocation()->getAddress(),
                "location_city" => $appointment->getLocation()->getCity(),
                "profile_picture" => $appointment->getUser()->getProfilePicture(),
                "isAppointmentFromLoggedInUser" => $appointment->getUser()->getId() === $loggedUserId,
            ];
        }
        return $data;
    }

    public function validate(Request $request): bool
    {
        $requestBody = $request->request;

        $locationEntity = $this->db->getRepository(Location::class);

        $appointmentEntity = $this->db->getRepository(Appointment::class);

        if (date("Y-m-d") <= date("Y-m-d", strtotime($requestBody->get('date_start')))) {

            $user = $this->security->getUser();

            $location = $locationEntity->findOneBy(['id' => $requestBody->get('id_location')]);

            $maxCapacity = $location->getCapacity();

            $appointmentsMadeByThisUser = $appointmentEntity->count([
                'user' => $user,
                'date_start' => \DateTime::createFromFormat('Y-m-d', $requestBody->get('date_start')),
            ]);

            $numberOfAppointmentsOnThisDay = $appointmentEntity->count([
                'date_start' => \DateTime::createFromFormat('Y-m-d', $requestBody->get('date_start')),
                'location' => $location,
            ]);

            $capacityLeft = $maxCapacity - $numberOfAppointmentsOnThisDay;

            if ($appointmentsMadeByThisUser > 0) {
                $this->errorMessage = 'You already made an appointment on ' . $requestBody->get('date_start');
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
