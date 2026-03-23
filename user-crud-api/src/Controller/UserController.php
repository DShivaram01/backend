<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/users', name: 'api_users_')]
class UserController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    // -----------------------------------------------
    // READ - Get All Users
    // -----------------------------------------------
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        $data = array_map(fn(User $user) => $this->formatUser($user), $users);

        return $this->json([
            'success' => true,
            'count'   => count($data),
            'data'    => $data,
        ]);
    }

    // -----------------------------------------------
    // READ - Get Single User
    // -----------------------------------------------
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => "User with ID {$id} not found.",
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'success' => true,
            'data'    => $this->formatUser($user),
        ]);
    }

    // -----------------------------------------------
    // CREATE - Register New User
    // -----------------------------------------------
    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Check required fields
        if (empty($data['email']) || empty($data['password']) || empty($data['username']) || empty($data['fullName'])) {
            return $this->json([
                'success' => false,
                'message' => 'email, password, username and fullName are all required.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check email is valid
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return $this->json([
                'success' => false,
                'message' => 'Please provide a valid email address.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Check duplicate email
        if ($this->userRepository->findOneBy(['email' => $data['email']])) {
            return $this->json([
                'success' => false,
                'message' => "Email '{$data['email']}' is already registered.",
            ], Response::HTTP_CONFLICT);
        }

        // Check duplicate username
        if ($this->userRepository->findOneBy(['username' => $data['username']])) {
            return $this->json([
                'success' => false,
                'message' => "Username '{$data['username']}' is already taken.",
            ], Response::HTTP_CONFLICT);
        }

        // Check password length
        if (strlen($data['password']) < 6) {
            return $this->json([
                'success' => false,
                'message' => 'Password must be at least 6 characters.',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Create the user
        $user = new User();
        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setFullName($data['fullName']);
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);

        // 🔐 Hash the password — plain text never touches the database!
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'User created successfully.',
            'data'    => $this->formatUser($user),
        ], Response::HTTP_CREATED);
    }

    // -----------------------------------------------
    // UPDATE - Full Update (PUT)
    // -----------------------------------------------
    #[Route('/{id}', name: 'update', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => "User with ID {$id} not found.",
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        if (empty($data['email']) || empty($data['username']) || empty($data['fullName'])) {
            return $this->json([
                'success' => false,
                'message' => 'email, username and fullName are required for full update.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Check duplicate email (excluding current user)
        $existing = $this->userRepository->findOneBy(['email' => $data['email']]);
        if ($existing && $existing->getId() !== $user->getId()) {
            return $this->json([
                'success' => false,
                'message' => "Email '{$data['email']}' is already taken.",
            ], Response::HTTP_CONFLICT);
        }

        $user->setEmail($data['email']);
        $user->setUsername($data['username']);
        $user->setFullName($data['fullName']);
        $user->setRoles($data['roles'] ?? ['ROLE_USER']);

        // Only rehash if a new password is provided
        if (!empty($data['password'])) {
            $hashed = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashed);
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data'    => $this->formatUser($user),
        ]);
    }

    // -----------------------------------------------
    // UPDATE - Partial Update (PATCH)
    // -----------------------------------------------
    #[Route('/{id}', name: 'patch', methods: ['PATCH'])]
    public function patch(int $id, Request $request): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => "User with ID {$id} not found.",
            ], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Only update fields that are present in the request
        if (isset($data['email']))    $user->setEmail($data['email']);
        if (isset($data['username'])) $user->setUsername($data['username']);
        if (isset($data['fullName'])) $user->setFullName($data['fullName']);
        if (isset($data['roles']))    $user->setRoles($data['roles']);

        // 🔐 Rehash only if password is being changed
        if (!empty($data['password'])) {
            $hashed = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashed);
        }

        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => 'User partially updated.',
            'data'    => $this->formatUser($user),
        ]);
    }

    // -----------------------------------------------
    // DELETE
    // -----------------------------------------------
    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);

        if (!$user) {
            return $this->json([
                'success' => false,
                'message' => "User with ID {$id} not found.",
            ], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json([
            'success' => true,
            'message' => "User '{$user->getUsername()}' deleted successfully.",
        ]);
    }

    // -----------------------------------------------
    // Private Helper — format user for JSON response
    // -----------------------------------------------
    private function formatUser(User $user): array
    {
        return [
            'id'       => $user->getId(),
            'username' => $user->getUsername(),
            'fullName' => $user->getFullName(),
            'email'    => $user->getEmail(),
            'roles'    => $user->getRoles(),
            // ✅ Password is intentionally NOT returned in responses
        ];
    }
}