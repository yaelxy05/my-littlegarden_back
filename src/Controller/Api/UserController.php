<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
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
     * Edit user (PUT et PATCH)
     *
     * @Route("/api/user/update", name="api_user_update_put", methods={"PUT"})
     * @Route("/api/user/update", name="api_user_update_patch", methods={"PATCH"})
     */
    public function userUpdate(User $user = null, EntityManagerInterface $em, UserPasswordHasherInterface $passwordEncoder, SerializerInterface $serializer, Request $request, ValidatorInterface $validator): Response
    {
        // We want to modify the reservation whose id is transmitted via the URL

        // 404 ?
        if ($user === null) {
            // We return a JSON message + a 404 status
            return $this->json(['error' => "L'utilisateur n'a pas été trouvé."], Response::HTTP_NOT_FOUND);
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

        /**
     * @Route("/api/user/update/password", name="app_account_password")
     */
    /*public function userUpdatePassword(Request $request, UserPasswordHasherInterface $encoder): Response
    {
        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();

            if($encoder->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $password = $encoder->hashPassword($user, $new_pwd);
                $user->setPassword($password);
                $this->entityManager->flush();

                $notification = "Votre mot de passe à été mis à jour";
            } else {
                $notification = "Votre mot de passe est invalide";
            }
        }

        return $this->render('account/password.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
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
