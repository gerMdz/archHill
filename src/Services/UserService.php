<?php

namespace App\Services;

use App\Entity\User;
use App\Repository\UserRepository;

class UserService
{
    private UserRepository $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function updateOrCreate($service, $dataUser): User
    {
        $user = $this->userRepository->findOneBy(['serviceId' =>  $service]);

        if($user){
            return $user;
        }
        $user = new User();
        $user->setServiceId()
            ->setAccessToken($dataUser->accessToken);
        return $user;
    }
}