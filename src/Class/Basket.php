<?php

namespace App\Class;


use App\Entity\VideoGames;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Basket
{
    
    private $entityManager;
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager,  RequestStack $requestStack )
    {
        
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function add($id)
    {
        $basket = $this->requestStack->getSession()->get('basket', []);
        
        if(!empty($basket[$id])) {
            $basket[$id]++;
        } else {
            $basket[$id] = 1;
        }

        $this->requestStack->getSession()->set('basket', $basket);
        
    }

    public function get()
    {
        return $this->requestStack->getSession()->get('basket');
    }

    public function remove()
    {
        return $this->requestStack->getSession()->remove('basket');
    }

    public function delete($id)
    {
        
        $basket = $this->requestStack->getSession()->get('basket', []);

        unset($basket[$id]);

        return $this->requestStack->getSession()->set('basket', $basket);
    }

    public function decrease($id)
    {
        
        $basket = $this->requestStack->getSession()->get('basket', []);

        if ($basket[$id] > 1) {
            $basket[$id]--;
        } else {
            unset($basket[$id]);
        }

        return $this->requestStack->getSession()->set('basket', $basket);
    }

    public function getAllBasket()
    {
        $basketOver = [];
        if ($this->get()) {
            foreach($this->get() as $id => $quantity) {
                $jeuxvideo = $this->entityManager->getRepository(VideoGames::class)->findOneById($id);
                if (!$jeuxvideo) {
                    $this->delete($id);
                    continue;
                }
                $basketOver[] = [
                    'jeuxvideo' => $jeuxvideo,
                    'quantity' => $quantity
                ];
            }
        }
        return $basketOver;
    }
}