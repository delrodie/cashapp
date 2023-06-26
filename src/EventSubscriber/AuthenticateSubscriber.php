<?php

namespace App\EventSubscriber;

use App\Repository\Main\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class AuthenticateSubscriber implements EventSubscriberInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'security.authentication.success' => 'onSecurityAuthenticationSuccess',
            'Symfony\Component\Security\Http\Event\LogoutEvent' => 'onSecurityLogout'
        ];
    }

    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $securityToken = $event->getAuthenticationToken();
        $userIdentify = $this->getUserIdentity($securityToken);

        // Mise a jour de la ligne utilisateur
        $userEntity = $this->userRepository->findOneBy(['username' => $userIdentify]);
        if ($userEntity){
            $userEntity->setConnexion((int)$userEntity->getConnexion()+1);
            $userEntity->setLastConnectedAt(new \DateTime());

            $this->userRepository->save($userEntity, true);
        }
    }

    public function onSecurityLogout(LogoutEvent $event): void
    {
        if (!$event->getResponse() || !$event->getToken()) return ;
    }

    private function getUserIdentity(TokenInterface $securityToken): string
    {
        return $securityToken->getUserIdentifier();
    }

}
