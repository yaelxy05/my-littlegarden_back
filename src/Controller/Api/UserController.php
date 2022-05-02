<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/api/register", name="api_register", methods="POST")
     */
    public function register(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, Request $request, ValidatorInterface $validator, SerializerInterface $serializer): Response
    {
        $jsonContent = $request->getContent();
        
        $user = $serializer->deserialize($jsonContent, User::class, 'json');

        $errors = $validator->validate($user);
        
        // If there is at least one error, we return a 400
        if (count($errors) > 0) {
            $errorsList = [];
            foreach ($errors as $erreur) {
                $input = $erreur->getPropertyPath();
                $errorsList[$input] = $erreur->getMessage();
            }

            return $this->json(
                [
                    'error' => $errorsList
                ],
                400
            );
        }
       
        $password = $user->getPassword();
        // This is where we encode the User password (found in $ user)
        $encodedPassword = $passwordEncoder->hashPassword($user, $password);
        // We reassign the password encoded in the User
        $user->setPassword($encodedPassword);
        $user->setRoles(['ROLE_USER']);
        $user->setCreatedAt(new \DateTimeImmutable());

        // We save the user
        $entityManager->persist($user);
        $entityManager->flush();
          
        return $this->json([
                'user' => $user
            ], Response::HTTP_CREATED);
    }
    /**
     * @Route("/api/users", name="api_users", methods="GET")
     */
    public function user(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $users = $userRepository->findByUserField($user);
       

        return $this->json($users, 200, [], ['groups' => 'user_read']);
    }

    /**
     * @Route("/api/user/update/{id<\d+>}", name="api_user_update", methods="PATCH")
     */
    public function reservationUpdate(User $user = null, EntityManagerInterface $em, SerializerInterface $serializer, Request $request, ValidatorInterface $validator): Response
    {
        // We want to modify the reservation whose id is transmitted via the URL

        // 404 ?
        if ($user === null) {
            // We return a JSON message + a 404 status
            return $this->json(['error' => "L'utilisateur' n\'a pas été trouvé."], Response::HTTP_NOT_FOUND);
        }

        // Our JSON which is in the body
        $jsonContent = $request->getContent();

        /* We will have to associate the JSON data received on the existing entity
        We deserialize the data received from the front ($ request-> getContent ()) ...
        ... in the reservation object to modify */
        $serializer->deserialize(
            $jsonContent,
            User::class,
            'json',
            // We have this additional argument which tells the serializer which existing entity to modify
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        // Validation of the deserialized entity
        $errors = $validator->validate($user);
        // Generating errors
        if (count($errors) > 0) {
            // We return the error table in Json to the front with a status code 422
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // On flush $reservation which has been modified by the Serializer
        $em->flush();

        // Condition the return message in case the entity is not modified
        return $this->json(['message' => "Les informations utilisateur ont bien été modifié."], Response::HTTP_OK);
    }

}
