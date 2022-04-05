<?php

namespace App\Controller;

use App\Entity\Plant;
use App\Repository\PlantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PlantController extends AbstractController
{
     /**
     * @Route("/api/plant", name="api_plant")
     */
    public function plant(PlantRepository $plantRepository): Response
    {
        $user = $this->getUser();
        $plant = $plantRepository->findLegumeForOneUser($user);
        return $this->json($plant, 200, [], ['groups' => 'plant_read']);
    }

    /**
     * @Route("/api/plant/create", name="api_plant_create", methods="POST")
     */
    public function plantCreate(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Retrieve the content of the request, i.e. the JSON
        $jsonContent = $request->getContent();

        // We deserialize this JSON into a reservation entity, thanks to the Serializer
        // We transform the JSON into an object of type App\Entity\Reservation
        $plant = $serializer->deserialize($jsonContent, Plant::class, 'json');

        // If linked objects (Users) they will be validated if @Valid annotation
        // present on the $user property of the Reservation class
        $errors = $validator->validate($plant);

        if (count($errors) > 0) {

            // The array of errors is returned as JSON with a status of 422
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }


        $plant->setCreatedAt(new \DateTimeImmutable());
        

        // We save the legume
        $entityManager->persist($plant);
        $entityManager->flush();

        // We redirect to legume_read
        return $this->json($plant, 200, [], ['groups' => 'plant_read'], Response::HTTP_CREATED);
    }
}
