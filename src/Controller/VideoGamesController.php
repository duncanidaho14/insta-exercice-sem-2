<?php

namespace App\Controller;

use App\Entity\VideoGames;
use App\Form\VideoGameType;
use App\Repository\VideoGamesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class VideoGamesController extends AbstractController
{

    /**
     * This function will find all the games that belong to the user who is currently logged in and
     * display them on the page.
     * 
     * @param JeuxvideoRepository jeuxvideoInstance the repository instance
     * 
     * @return Response The user's games
     */
    #[Route('/mes-jeux', name: 'app_my_video_games')]
    public function index(VideoGamesRepository $videoGamesRepository): Response
    {
        return $this->render('video_games/mesjeux.html.twig', [
            'jeuxvideo' => $videoGamesRepository->findBy(array('user' => $this->getUser())),
        ]);
    }

    /**
     * I want to create a game, and I want to create a comment for this game. 
     * 
     * I want to create a game, and I want to create a category for this game. 
     * 
     * I want to create a game, and I want to create a comment for this game, and I want to create a
     * category for this game. 
     * 
     * I want to create a game, and I want to create a comment for this game, and I want to create a
     * category for this game, and I want to create a user for this game. 
     * 
     * I want to create a game, and I want to create a comment for this game, and I want to create a
     * category for this game, and I want to create a user for this game, and I want to create a user
     * for this comment. 
     * 
     * I want to create a game, and I want to create a comment
     * 
     * @param Request request The request object.
     * @param EntityManagerInterface entityManager The EntityManagerInterface instance.
     */
    #[Route('/jeuxvideo/creation', name:'app_create_videogame', methods:['GET', 'POST'])]
    public function createGame(Request $request, EntityManagerInterface $entityManager): Response
    {
        $jeuxvideo = new VideoGames();

        $form = $this->createForm(VideoGameType::class, $jeuxvideo);
        $jeuxvideo->setUser($this->getUser());
        
        foreach($jeuxvideo->getCategories() as $categories){
            $jeuxvideo->setUser($this->getUser());
            $categories->addGame($jeuxvideo);
            $entityManager->persist($categories);
        }
        
        foreach ($jeuxvideo->getComments() as $comments) {
            $jeuxvideo->setUser($this->getUser());
            $comments->setUser($this->getUser());
            $comments->setComment($jeuxvideo);
            $entityManager->persist($comments);
        }
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {
            
            
            $entityManager->persist($jeuxvideo);
            $entityManager->flush();
            $this->addFlash(
                'success',
                "Le jeux video a bien été crée {$jeuxvideo->getName()}! "
            );
            return $this->redirectToRoute('accueil');
        }
        return $this->render('video_games/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /** 
     * The function editGame() is called when the user clicks on the edit button. It displays the form
     * to edit the game. When the user clicks on the submit button, the function checks if the form is
     * valid. If it is, it persists the changes and flushes them. Then it displays a success message
     * 
     * @param Request request The request object.
     * @param Jeuxvideo jeuxvideo The Jeuxvideo object that will be edited.
     * @param EntityManagerInterface entityManager The EntityManagerInterface is the object that allows
     * you to persist and flush objects to the database.
     * 
     * @return Response The response is the render of the edit.html.twig file.
     */
    #[Route('/jeuxvideo/edition/{id}', name:'app_edit_videogame', methods:['GET', 'POST'])]
    public function editGame(Request $request, VideoGames $jeuxvideo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VideoGameType::class, $jeuxvideo);
        
        $jeuxvideo->setUser($this->getUser());
        /* Adding the user to the categories. */
        foreach($jeuxvideo->getCategories() as $categories){
            $jeuxvideo->setUser($this->getUser());
            $categories->addGame($jeuxvideo);
            //$categories->setImage($image);
            $entityManager->persist($categories);
        }
            /* Adding the user to the comments. */
        foreach ($jeuxvideo->getComments() as $comments) {
            $jeuxvideo->setUser($this->getUser());
            $comments->setUser($this->getUser());
            $comments->setComment($jeuxvideo);
            $entityManager->persist($comments);
        }
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
                
            

            $entityManager->persist($jeuxvideo);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Vous avez bien éditer votre jeux : ' . $jeuxvideo->getName() . ' !'
            );
        }

        return $this->render('video_games/edit.html.twig', [
            'form' => $form->createView(),
            'jeuxvideo' => $jeuxvideo
        ]);
    }

    /**
    * I want to display the game page with the comments form and the comments list
    * 
    * @param JeuxvideoRepository jeuxvideoRepository The repository for the Jeuxvideo entity.
    * @param Request request The request object.
    * @param EntityManagerInterface manager the EntityManagerInterface
    * @param slug The slug of the game to display
    * 
    * @return Response The response of the controller.
    */
    #[Route('/jeuxvideo/{id}', name:'app_show_videogame', methods:['GET', 'POST'])]
    public function displayJeuxvideo(VideoGamesRepository $jeuxvideoRepository, Request $request, EntityManagerInterface $manager, $slug): Response
    {
        
        return $this->render('video_games/show.html.twig', [
            'jeuxvideo' => $jeuxvideoRepository->findOneById($id),
            'form' => $form->createView()
        ]);
    }

    /** 
     * This function deletes a game from the database and redirects to the home page.
     * 
     * @param Jeuxvideo jeuxvideo The object to delete
     * @param EntityManagerInterface entityManager The EntityManagerInterface instance.
     */
    #[Route('/jeuxvideo/delete/{id}', name:'app_delete_videogame', methods:['GET', 'POST'])]
    public function delete(VideoGames $jeuxvideo, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($jeuxvideo);
        $entityManager->flush();

        $this->addFlash(
            'success',
            "Le jeux <strong>{$jeuxvideo->getName()}</strong> a bien été supprimée ! "
        );

        return $this->redirectToRoute("app_home");
    }
}
