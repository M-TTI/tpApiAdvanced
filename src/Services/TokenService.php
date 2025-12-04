<?php

namespace App\Services;

use App\Entity\RefreshToken;
use App\Entity\User;
use App\Repository\RefreshTokenRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class TokenService
{
    public const ACCESS_TOKEN_EXPIRATION = 900; // 15 minutes
    public const REFRESH_TOKEN_EXPIRATION = 2592000; // 30 jours
    public const REFRESH_TOKEN_TYPE = 'refresh_token';

    public function __construct(
        private readonly JWTTokenManagerInterface $tokenManager,
        private readonly RefreshTokenRepository $refreshTokenRepository,
    ) {
    }

    public function createAccessToken(User $user): string
    {
        return $this->tokenManager->create($user);
    }

    public function createRefreshToken(User $user): array
    {
        $jti = bin2hex(random_bytes(16));
        $expiresAt = new \DateTimeImmutable('+' . self::REFRESH_TOKEN_EXPIRATION . ' seconds');

        $payload = [
            'sub' => $user->getUserIdentifier(),
            'jti' => $jti,
            'type' => self::REFRESH_TOKEN_TYPE,
            'iat' => time(),
            'exp' => $expiresAt->getTimestamp(),
        ];

        $refreshTokenString = $this->tokenManager->createFromPayload($user, $payload);

        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        $refreshToken->setToken($refreshTokenString);
        $refreshToken->setJti($jti);
        $refreshToken->setExpiresAt($expiresAt);
        $refreshToken->setCreatedAt(new \DateTimeImmutable());

        $this->refreshTokenRepository->save($refreshToken);

        return [
            'token' => $refreshTokenString,
            'expiresAt' => $expiresAt,
            'jti' => $jti,
        ];
    }

    public function createTokenPair(User $user): array
    {
        $accessToken = $this->createAccessToken($user);
        $refreshToken = $this->createRefreshToken($user);

        return [
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken['token'],
            'expires_in' => self::ACCESS_TOKEN_EXPIRATION,
            'refresh_expires_at' => $refreshToken['expiresAt']->format('Y-m-d H:i:s'),
        ];
    }

    public function validateRefreshToken(string $tokenString): ?RefreshToken
    {
        try {
            $payload = $this->tokenManager->parse($tokenString);

            if (($payload['type'] ?? null) !== 'refresh') {
                return null;
            }

            if (($payload['exp']?? 0) < time()) {
                return null;
            }

            $jti = $payload['jti'] ?? null;
            if (empty($jti)) {
                return null;
            }

            $refreshToken = $this->refreshTokenRepository->findOneByJti($jti);

            if (!$refreshToken instanceof RefreshToken ||
                $refreshToken->getToken() !== $tokenString ||
                !$refreshToken->isValid()) {
                return null;
            }

            return $refreshToken;
        } catch (\Exception) {
            return null;
        }
    }

    public function revokeRefreshToken(string $tokenString): bool
    {
        $refreshToken = $this->refreshTokenRepository->findOneByToken($tokenString);

        if (!$refreshToken instanceof RefreshToken) {
            return false;
        }

        $this->refreshTokenRepository->delete($refreshToken);
        return true;
    }

    public function revokeAllRefreshTokenForUser(User $user): int
    {
        return $this->refreshTokenRepository->deleteAllForUser($user);
    }

    public function cleanupExpiredTokens(): int
    {
        return $this->refreshTokenRepository->deleteExpired();
    }
}
