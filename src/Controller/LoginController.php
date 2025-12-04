<?php

namespace App\Controller;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Services\TokenService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function loginAction(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository,
        TokenService $tokenService,
    ): JsonResponse {
        $data = $request->toArray();
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (empty($email) || empty($password)) {
            return $this->json([
                'error' => 'email and password are required',
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user instanceof User || !$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json([
                'error' => 'Invalid credentials',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $tokens = $tokenService->createTokenPair($user);

        return $this->json([
            'message' => 'Identification successful',
            'tokens' => $tokens,
        ], Response::HTTP_OK);
    }

    #[Route('/token/refresh', name: 'app_token_refresh', methods: ['POST'])]
    public function refreshAction(
        Request $request,
        TokenService $tokenService,
    ): JsonResponse {
        $data = $request->toArray();
        $refreshTokenString = $data['refresh_token'] ?? null;

        if (empty($refreshTokenString)) {
            return $this->json([
                'error' => 'Refresh token required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $refreshToken = $tokenService->validateRefreshToken($refreshTokenString);

        if (!$refreshToken instanceof RefreshToken) {
            return $this->json([
                'error' => 'Invalid or expired refresh token',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $user = $refreshToken->getUser();

        $tokenService->revokeRefreshToken($refreshTokenString);
        $tokens = $tokenService->createTokenPair($user);

        return $this->json([
            'message' => 'Refresh successful',
            'tokens' => $tokens,
        ], Response::HTTP_OK);
    }

    #[Route('/logout', name: 'app_logout', methods: ['POST'])]
    public function logoutAction(
        Request $request,
        TokenService $tokenService,
    ): JsonResponse {
        $data = $request->toArray();
        $refreshTokenString = $data['refreshToken'] ?? null;

        if (empty($refreshTokenString)) {
            return $this->json([
                'error' => 'Refresh token required'
            ], Response::HTTP_BAD_REQUEST);
        }

        $tokenService->revokeRefreshToken($refreshTokenString);

        return $this->json([
            'message' => 'Logout successful',
        ]);
    }
}
