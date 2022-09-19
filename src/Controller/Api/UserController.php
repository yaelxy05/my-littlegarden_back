<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Service\UploaderHelper;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{
    /**
     * @Route("/api/register", name="api_register", methods="POST")
     */
    public function register(UploaderHelper $uploaderHelper, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder, Request $request, ValidatorInterface $validator)
    {
        $userData = $request->request->all();

        $errors = $validator->validate($userData);

        if (count($errors) > 0) {
            

            // The array of errors is returned as JSON
            // With an error status 422
            // @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        //$user = $this->getUser();

        //$user->setUser($user);
        $user->setEmail($userData['email']);
        $user->setFirstname($userData['firstname']);
        $user->setLastname($userData['lastname']);
        $user->setPassword($userData['password']);
        $user->setRoles(['ROLE_USER']);

        $password = $user->getPassword();
        // This is where we encode the User password (found in $ user)
        $encodedPassword = $passwordEncoder->hashPassword($user, $password);
        // We reassign the password encoded in the User
        $user->setPassword($encodedPassword);


        $user->setCreatedAt(new \DateTimeImmutable());


        // retrieves an instance of UploadedFile identified by picture
        $uploadedFile = $request->files->get('avatar');

        if ($uploadedFile) {
            $newFilename = $uploaderHelper->uploadImage($uploadedFile);
            $user->setAvatar($newFilename);
        }
        // We save the user
        $entityManager->persist($user);
        $entityManager->flush();


        // We redirect to api_user_read
        return $this->json([
            'user' => $user,
        ], Response::HTTP_CREATED, [], ['groups' => 'user_read']);
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
     * Method changes the user's password
     * @Route("/{id}/password-edit", name="password_edit", methods={"PATCH"}, requirements={"id"="\d+"})
     *
     * @return void
     */
    public function passwordEdit(User $user, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $jsonData = $request->getContent();
        $passwordObj = json_decode($jsonData);

        // We check if the password contains the minimum required
        if (!preg_match('@^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).{8,}$@', $passwordObj->newPassword)) {
            return $this->json([
                'message' => 'Votre mot de passe doit comporter au moins huit caractères, dont au moins une majuscule et minuscule, un chiffre et un symbole.'
            ], 400);
        } else {
            // We check if the password entered by the user is the same as the one in the database
            if (password_verify($passwordObj->oldPassword, $user->getPassword())) {
                $user->setPassword($passwordHasher->hashPassword(
                    $user,
                    $passwordObj->newPassword
                ));

                // If all is good, we hash and modify the user's password, and we send him an email to warn him
                $user->setUpdatedAt(new \DateTimeImmutable());
                
                // We save the user
                $entityManager->persist($user);
                $entityManager->flush();
                
                //$this->getDoctrine()->getManager()->flush();
                return $this->json([
                    'message' => 'Le mot de passe a bien été mis à jour.'
                ]);
            } else {
                return $this->json([
                    'message' => 'Le mot de passe actuel est incorrect.'
                ], 400);
            }
        }
    }

    /**
     * Edit user (PUT et PATCH)
     *
     * @Route("/api/user/update", name="api_user_update_put", methods={"PUT"})
     * @Route("/api/user/update", name="api_user_update_patch", methods={"PATCH"})
     */
    /*public function userUpdate(User $user = null, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder, SerializerInterface $serializer, Request $request, ValidatorInterface $validator): Response
    {
        $user = $this->getUser();
        // 1. We want to modify the refuge whose id is transmitted via the URL
        // 404 page error ?
        if ($user === null) {
            // We return a JSON message + a 404 status
            return $this->json(['error' => 'Désolé cet utilisateur n\'existe pas.'], Response::HTTP_NOT_FOUND);
        }

        // Our JSON which is in the body
        $jsonContent = $request->getContent();
        
        $serializer->deserialize(
            $jsonContent,
            User::class,
            'json',
            // We have this additional argument which tells the serializer which existing entity to modify
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );
        
        // Validate the deserialize entity
        $errors = $validator->validate($user);
        // Generate errors
        if (count($errors) > 0) {
            // We return the error table in Json to the front with a status code 422
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $password = $user->getPassword();
        $hashedPassword = $passwordEncoder->hashPassword($user, $user->getPassword());
        
        // We reassign the password encoded in the User
        $user->setPassword($hashedPassword);

        /* 
        $hashedPassword = $passwordEncoder->encodePassword($user, $user->getPassword());
        
        // We reassign the password encoded in the User
        $user->setPassword($hashedPassword);
        


        $password = $user->getPassword();
        // This is where we encode the User password (found in $ user)
        $encodedPassword = $passwordEncoder->hashPassword($user, $password);
        // We reassign the password encoded in the User
        $user->setPassword($encodedPassword);

        // On flush $user which has been modified by the Serializer
        $em->flush();

        return $this->json(['message' => 'Identifiants de connexion modifiées.'], Response::HTTP_OK);
    }*/


    /**
     * Edit user avatar (POST)
     * 
     * @Route("/api/user/{id<\d+>}/update/avatar", name="api_user_update_avatar", methods={"POST"})
     */
    public function updateUserAvatar(User $user, EntityManagerInterface $entityManager, UploaderHelper $uploaderHelper, Request $request, ValidatorInterface $validator)
    {
        
        // We should make an edit function specially for image because in API we couldn't use the methods PUT and PATCH with the multipart/form-data
        
        $userData = $request->request->all();
        
        $errors = $validator->validate($userData);
        
        if (count($errors) > 0) {

            // The array of errors is returned as JSON
            // With an error status 422
            // @see https://fr.wikipedia.org/wiki/Liste_des_codes_HTTP
            return $this->json($errors, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // retrieves an instance of UploadedFile identified by picture
        $uploadedFile = $request->files->get('avatar');

        if ($uploadedFile) {
            $newFilename = $uploaderHelper->uploadImage($uploadedFile);
            $user->setAvatar($newFilename);
        }
        // We save the animal
        $entityManager->persist($user);
        $entityManager->flush();
    

        // We redirect to api_animal_read
        return $this->json([
            'user' => $user,
        ], Response::HTTP_OK, [], ['groups' => 'user_read']);
    }

    /**
     * Delete a user
     * 
     * @Route("/api/user/delete", name="api_user_delete", methods={"DELETE"})
     */
    public function delete(EntityManagerInterface $entityManager): Response
    {
        // we take the current user to delete it.
        $user = $this->getUser();
    
        if ($user === null) {

            // Optional, message for the front
            $message = [
                'status' => Response::HTTP_NOT_FOUND,
                'error' => 'Utilisateur non trouvé.',
            ];
            // We define a custom message and an HTTP 404 status code
            return $this->json($message, Response::HTTP_NOT_FOUND);
        }
        
        $entityManager->remove($user);
        $entityManager->flush();

        return $this->json(
            ['message' => 'L\'utilisateur a bien été supprimé'],
            Response::HTTP_OK
        );
    }
}
