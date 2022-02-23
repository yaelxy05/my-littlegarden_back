<?php

namespace App\Controller;

use App\Entity\Legume;
use App\Repository\LegumeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LegumeController extends AbstractController
{
    /**
     * @Route("/api/legumes", name="api_legume")
     */
    public function legume(LegumeRepository $legumeRepository): Response
    {
        $user = $this->getUser();
        $legumes = $legumeRepository->findLegumeForOneUser($user);
        return $this->json($legumes, 200, [], ['groups' => 'legume_read']);
    }


    /**
     * @Route("/api/legume/{id<\d+>}", name="api_legume_read", methods="GET")
     */
    public function legumeRead(Legume $legume = null): Response
    {
        // 404 error page
        if ($legume === null) {

            // Optional, message for the front
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Désolé ce légume n\'existe pas.',
            ];

            // We define a custom message and an HTTP 404 status code
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        // The 4th argument represents the "context" which will be transmitted to the serializer
        return $this->json($legume, 200, [], ['groups' => 'legume_read']);
    }

    /**
     * @Route("/api/legume/create", name="api_legume_create", methods="POST")
     */
    public function legumeCreate(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        // Retrieve the content of the request, i.e. the JSON
        $jsonContent = $request->getContent();

        // We deserialize this JSON into a reservation entity, thanks to the Serializer
        // We transform the JSON into an object of type App\Entity\Reservation
        $legume = $serializer->deserialize($jsonContent, Legume::class, 'json');

        // If linked objects (Users) they will be validated if @Valid annotation
        // present on the $user property of the Reservation class
        $errors = $validator->validate($legume);

        if (count($errors) > 0) {

            // The array of errors is returned as JSON with a status of 422
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = $this->getUser();
        $legume->setCreatedAt(new \DateTimeImmutable());
        $legume->setUser($user);

        // We save the legume
        $entityManager->persist($legume);
        $entityManager->flush();

        // We redirect to legume_read
        return $this->json($legume, 200, [], ['groups' => 'legume_read'], Response::HTTP_CREATED);
    }

    /**
     * @Route("/api/legume/update/{id<\d+>}", name="api_legume_update")
     */
    public function legumeUpdate(Legume $legume = null, EntityManagerInterface $em, SerializerInterface $serializer, Request $request, ValidatorInterface $validator): Response
    {
        // We want to modify the legume whose id is transmitted via the URL

        // 404 ?
        if ($legume === null) {
            // We return a JSON message + a 404 status
            return $this->json(['error' => 'Le légume n\'a pas été trouvé.'], Response::HTTP_NOT_FOUND);
        }

        // Our JSON which is in the body
        $jsonContent = $request->getContent();

        /* We will have to associate the JSON data received on the existing entity
        We deserialize the data received from the front ($ request-> getContent ()) ...
        ... in the reservation object to modify */
        $serializer->deserialize(
            $jsonContent,
            Reservation::class,
            'json',
            // We have this additional argument which tells the serializer which existing entity to modify
            [AbstractNormalizer::OBJECT_TO_POPULATE => $legume]
        );

        // Validation of the deserialized entity
        $errors = $validator->validate($legume);
        // Generating errors
        if (count($errors) > 0) {
            // We return the error table in Json to the front with a status code 422
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // On flush $legume which has been modified by the Serializer
        $em->flush();

        // Condition the return message in case the entity is not modified
        return $this->json(['message' => 'Le légume a été modifié.'], Response::HTTP_OK);
    }

    /**
     * @Route("/api/legume/delete/{id<\d+>}", name="api_legume_delete")
     */
    public function legumeDelete(Legume $legume = null, EntityManagerInterface $entityManager): Response
    {
        // 404
        if ($legume === null) {
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'légume non trouvé.',
            ];

            // We define a custom message and an HTTP 404 status code
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }

        // Otherwise we delete in base
        $entityManager->remove($legume);
        $entityManager->flush();

        // The $task object still exists in PHP memory until the end of the script
        return $this->json(
            ['message' => 'Le légume ' . $legume->getName() . ' a été supprimé !'],
            Response::HTTP_OK
        );
    }
}
