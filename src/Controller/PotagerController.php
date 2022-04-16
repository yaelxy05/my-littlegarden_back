<?php

namespace App\Controller;

use App\Entity\Potager;
use App\Repository\PotagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PotagerController extends AbstractController
{
    /**
     * @Route("/api/potager", name="api_potager")
     */
    public function potager(PotagerRepository $potagerRepository): Response
    {
        $user = $this->getUser();
        $potager = $potagerRepository->findPotagerForOneUser($user);
        return $this->json($potager, 200, [], ['groups' => 'potager_read']);
    }

    /**
     * @Route("/api/potager/create", name="api_potager_create", methods="POST")
     */
    public function potagerCreate(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Retrieve the content of the request, i.e. the JSON
        $jsonContent = $request->getContent();

        // We deserialize this JSON into a reservation entity, thanks to the Serializer
        // We transform the JSON into an object of type App\Entity\Reservation
        $potager = $serializer->deserialize($jsonContent, Potager::class, 'json');

        // If linked objects (Users) they will be validated if @Valid annotation
        // present on the $user property of the Reservation class
        $errors = $validator->validate($potager);

        if (count($errors) > 0) {

            // The array of errors is returned as JSON with a status of 422
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->getUser();
        $potager->setCreatedAt(new \DateTimeImmutable());
        $potager->setUser($user);

        // We save the legume
        $entityManager->persist($potager);
        $entityManager->flush();

        // We redirect to legume_read
        return $this->json($potager, 200, [], ['groups' => 'potager_read'], Response::HTTP_CREATED);
    }

}
