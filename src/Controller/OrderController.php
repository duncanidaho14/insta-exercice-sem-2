<?php

namespace App\Controller;

use App\Class\Basket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/commande', name: 'app_order')]
    public function index(Basket $basket, Request $request): Response
    {
        if(!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('account_address_add');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);
        
        foreach ($basket->getAllBasket() as $key => $value) {
            $value;
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'basket' => $basket->getAllBasket()
        ]);
    }

    /**
     * I want to get the value of the cache and put it in the variable .
     * I want to use this variable in the form.
     * I want to use this variable in the foreach loop.
     * I want to use this variable in the render
     * 
     * @param CacheInterface cache The cache service
     * @param Basket basket the basket object
     * @param Request request The request object.
     * 
     * @return Response The return is the render of the add.html.twig file.
     */
    #[Route('/commande/recapitulatif', name:'order_recap', methods:['POST'])]
    public function add(CacheInterface $cache, Basket $basket, Request $request): Response
    {
        $cache = new FilesystemAdapter();
        foreach ($basket->getAllBasket() as $key => $value) {
            $value;
        }
        $value = $cache->get($value['jeuxvideo'], function() use ($value) {
            return $value;
        });
        
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]);

        $form->handleRequest($request);
        
        
        
        if($form->isSubmitted() && $form->isValid()) { 
            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            if (!$delivery[0]) {
                $this->addFlash(
                    'error',
                    'Vous n\'avez pas choisie d\'adresse !'
                );
                
                return $this->redirectToRoute('order');
            }

            if (!$carriers[0]) {
                $this->addFlash(
                    'error',
                    'Vous n\'avez pas choisie d\'adresse !'
                );
                return $this->redirectToRoute('order');

            }
                $delivery_content = $delivery[0]->getFirstName() . ' ' . $delivery[0]->getLastName();
                $delivery_content .= '<br />' . $delivery[0]->getPhone();

                if($delivery[0]->getCompany()){
                    $delivery_content .= '<br/>' . $delivery[0]->getCompany();

                }

                
                $delivery_content .= '<br/>' . $delivery[0]->getAddress();
                $delivery_content .= '<br/>' . $delivery[0]->getZip() . ' ' . $delivery[0]->getCity();
                $delivery_content .= '<br/>' . $delivery[0]->getCountry();

                $order = new Order();
                $reference = $date->format('d-m-Y'). '-'.\uniqid();
                $order->setReference($reference); 
                $order->setUser($this->getUser());

                // if(!$order->getCarrierName()) {
                //     $this->addFlash(
                //         'danger',
                //         "Vous n'avez pas choisie de transporteur ! X"
                //     );
                //     return $this->redirectToRoute('order');
                // }
                
                $order->setCarrierName($carriers[0]->getName());
                $order->setCarrierPrice(($carriers[0]->getPrice())); // /100
                $order->setDelivery($delivery_content);
                $order->setIsPaid(0);

                $this->entityManager->persist($order);

                
                
                foreach ($basket->getAllBasket() as $product) {
                    $orderDetails = new OrderDetails();
                    $orderDetails->setMyOrder($order);
                    $orderDetails->setProduct($product['jeuxvideo']->getName());
                    $orderDetails->setQuantity($product['quantity']);
                    $orderDetails->setPrice(($product['jeuxvideo']->getPrice()));
                    $orderDetails->setTotal(($product['jeuxvideo']->getPrice())* $product['quantity']);

                    $this->entityManager->persist($orderDetails);
                }
            
                $this->entityManager->flush();
            
                
            

            return $this->render('order/add.html.twig', [
                'basket' => $basket->getAllBasket(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'reference' => $order->getReference()
            ]);
        }

        return $this->redirectToRoute('basket');
        
    }
}
