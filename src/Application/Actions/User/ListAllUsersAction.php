<?php
declare(strict_types=1);

namespace App\Application\Actions\User;

use Psr\Http\Message\ResponseInterface as Response;

use App\Domain\Image\Image;


class ListAllUsersAction extends UserAction
{
    /**
     * {@inheritdoc}
     */
    protected function action(): Response
    {
        
        $users = $this->userRepository->all();

        foreach($users as $user)
        {   
            $imageKey = Image::class.$user->imageId;

            if($this->cache->contain($imageKey))
            {
                $image = $this->cache->get($imageKey);
            } else {
                $image = $this->imageRepository->byId($user->imageId);
                $this->cache->set($imageKey, $image );    
            }            
                
            $user->image = $image;    
        }

        $this->logger->info("Users list was viewed.");

        return $this->respondWithData($users);
    }
}
