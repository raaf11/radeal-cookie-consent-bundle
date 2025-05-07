<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Cookie;

use ConnectHolland\CookieConsentBundle\Entity\CookieConsentLog;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\RequestStack;

class CookieLogger
{
    private EntityManagerInterface $entityManager;

    public function __construct(
        ManagerRegistry      $registry,
        private RequestStack $requestStack)
    {
        $this->entityManager = $registry->getManagerForClass(CookieConsentLog::class);
    }

    /**
     * Logs users preferences in database.
     */
    public function log(array $categories, string $key): void
    {
        if ($this->requestStack->getCurrentRequest() === null) {
            throw new \RuntimeException('No request found');
        }

        $ip = $this->anonymizeIp($this->requestStack->getCurrentRequest()->getClientIp());

        foreach ($categories as $category => $value) {
            $this->persistCookieConsentLog($category, $value, $ip, $key);
        }

        $this->entityManager->flush();
    }

    protected function persistCookieConsentLog(string $category, string $value, string $ip, string $key): void
    {
        $cookieConsentLog = (new CookieConsentLog())
            ->setIpAddress($ip)
            ->setCookieConsentKey($key)
            ->setCookieName($category)
            ->setCookieValue($value)
            ->setTimestamp(new \DateTime());

        $this->entityManager->persist($cookieConsentLog);
    }

    /**
     * GDPR required IP addresses to be saved anonymized.
     */
    protected function anonymizeIp(?string $ip): string
    {
        if ($ip === null) {
            return 'unknown';
        }

        $lastDot = strrpos($ip, '.') + 1;

        return substr($ip, 0, $lastDot) . str_repeat('x', strlen($ip) - $lastDot);
    }
}
