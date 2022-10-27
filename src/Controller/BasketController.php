<?php

namespace App\Controller;



use App\Class\Basket;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BasketController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * It renders the basket/index.html.twig template, passing it the basket variable
     * 
     * @param Basket basket The basket service
     * 
     * @return Response The basket is being returned.
     */
    #[Route('/mon-panier', name:'app_basket')]
    public function index(Basket $basket): Response
    {
        return $this->render('basket/index.html.twig', [
            'basket' => $basket->getAllBasket(),
        ]);
    }

    
    /**
     * It adds the product with the id  to the basket
     * 
     * @param Basket basket The basket object
     * @param id The id of the product to add to the basket
     * 
     * @return Response A redirect to the basket route.
     */
    #[Route('/mon-panier/add/{id}', name:'add_to_basket')]
    public function addBasket(Basket $basket, $id): Response
    {
        $basket->add($id);

        return $this->redirectToRoute('app_basket');
    }

    
    /**
     * It removes the basket from the session
     * 
     * @param Basket basket
     * 
     * @return Response A Response object
     */
    #[Route('/mon-panier/remove', name:'remove_to_basket')]
    public function remove(Basket $basket): Response
    {
        $basket->remove();

        return $this->redirectToRoute('app_home');
    }

    /**
     * It deletes the product from the basket
     * 
     * @param Basket basket The basket object
     * @param id The id of the product to delete
     * 
     * @return Response a response object.
     */
    #[Route('/mon-panier/delete/{id}', name:'delete_to_basket')]
    public function delete(Basket $basket, $id): Response
    {
        $basket->delete($id);

        return $this->redirectToRoute('app_basket');
    }

    /**
     * It decreases the quantity of a product in the basket
     * 
     * @param Basket basket The basket object
     * @param id The id of the product to be added to the basket
     * 
     * @return Response A Response object
     */
    #[Route('/mon-panier/decrease/{id}', name:'decrease_to_basket')]
    public function decrease(Basket $basket, $id): Response
    {
        $basket->decrease($id);
        return $this->redirectToRoute('app_basket');
    }
}
