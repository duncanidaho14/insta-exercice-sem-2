<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Images;
use App\Entity\Orders;
use DateTimeImmutable;
use App\Entity\Address;
use App\Entity\Carriers;
use App\Entity\Comments;
use App\Entity\Platforms;
use App\Entity\Categories;
use App\Entity\VideoGames;
use App\Entity\OrderDetails;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('FR-fr');
        
        // $adminRole = new Role();
        // $adminRole->setTitle('ROLE_ADMIN');
        // $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setFirstName('elhadi')
                    ->setLastName('beddarem')
                    ->setEmail('elhadibeddarem@gmail.com')
                    ->setPassword($this->encoder->hashPassword($adminUser, 'password'))
                    ->setAvatar('https://pbs.twimg.com/profile_images/1184794615951560704/MuK0y8MA.png')
                    //->setImageFile('8c9b82bc035d2ec941c0eb426c31f34f79931076.gif')
                    // ->addGrade($adminRole)
                    // ->setRoles(["ROLE_ADMIN"])
                    
                    
        ;

        $adminUserM = new User();
        $adminUserM->setFirstName('Meddy')
                    ->setLastName('Seize')
                    ->setEmail('meddy.seize@gmail.com')
                    ->setPassword($this->encoder->hashPassword($adminUserM, 'password'))
                    ->setAvatar('https://cdn.shopify.com/s/files/1/0262/4516/9245/products/image_ca191039-f492-4d10-8bdb-3c15495586af_1024x1024.jpg?v=1565831183')
                    // ->addGrade($adminRole)
                    // ->setRoles(["ROLE_ADMIN"])
        ;


        $manager->persist($adminUser);
        $manager->persist($adminUserM);
        
        // les utilisateurs

        $users = [];
        $genres = ['male', 'female'];

        for ($i=0; $i < 20; $i++) { 
            $user = new User();
            $genre = $faker->randomElement($genres);

            $picture = 'https://randomuser.me/api/portraits/';
            $pictureId = $faker->numberBetween(1, 99). '.jpg';

            $picture .= ($genre == 'male' ? 'men/' : 'women/'). $pictureId;

            $hash = $this->encoder->hashPassword($user, 'password');
            
            $user->setFirstName($faker->firstname($genre))
                ->setLastName($faker->lastname())
                ->setEmail($faker->email())
                ->setPassword($hash)
                ->setAvatar($picture)
                
            ;

            $manager->persist($user);
            $users[] = $user;
        }
        $addresses = [];
        for ($addr=0; $addr < 20; $addr++) { 
            $address = new Address();

            $address->setName($faker->name())
                    ->setFirstName($faker->firstName())
                    ->setLastName($faker->lastName())
                    ->setCompany($faker->company())
                    ->setAddress($faker->address())
                    ->setCity($faker->city())
                    ->setZip($faker->postcode())
                    ->setPhone($faker->phoneNumber())
                    ->setCountry($faker->country())
                    ->setUser($users[mt_rand(0, count($users) -1)])
            ;

            $manager->persist($address);
            $addresses[] = $address;
        }

        

        
        
        $jeuxvideos = [];
        for ($jeu=0; $jeu < 20; $jeu++) { 
                $jeuxvideo = new VideoGames();
                $jeuxvideo->setName($faker->name())
                            ->setIntroduction($faker->text())
                            ->setDescription($faker->text())
                            ->setImage($faker->imageUrl())
                            ->setPrice($faker->numberBetween(0, 80))
                            ->setUser($users[mt_rand(0, count($users) -1)])
                            ->setReleaseDate($faker->dateTime())
                ;

                for ($plat=0; $plat < 1; $plat++) { 
                    $platform = new Platforms();
                    $platform->setName($faker->firstName)
                            ->setImage($faker->imageUrl())
                            ->addVideoGame($jeuxvideo)
                    ;

                    $manager->persist($platform);
                }
                $jeuxvideo->addPlatform($platform);

                for ($img=0; $img < 5; $img++) { 
                    $images = new Images();
                    $images->setTitle($faker->name())
                            ->setUrl($faker->imageUrl())
                            ->setVideoGame($jeuxvideo)
                    ;
                    $manager->persist($images);
                }

                for ($cat=0; $cat < 1; $cat++) { 
                    $categorie = new Categories();

                    $categorie->setName($faker->name())
                                ->setImage($faker->imageUrl())
                                ->addVideoGame($jeuxvideo)
                                
                    ;

                        $manager->persist($categorie);
                }

                $jeuxvideo->addCategory($categorie);
                            

                if (mt_rand(1, 12)) {
                    $comment = new Comments();

                    $comment->setTitle($faker->company())
                            ->setComment($faker->text())
                            ->setGame($jeuxvideo)
                            ->setUser($users[mt_rand(0, count($users) -1)])
                            ->setCreatedAt($faker->dateTime())
                            ->setUpdatedAt($faker->dateTime())
                    ;
                    $manager->persist($comment);
                }

                $jeuxvideo->addComment($comment)
                ;
            
            $manager->persist($jeuxvideo);
            $jeuxvideos[] = $jeuxvideo;
        }
        
        

        $carriers = [];
        for ($carr=0; $carr < 5; $carr++) { 
            $carrier = new Carriers();
            $carrier->setName($faker->company())
                    ->setDescription($faker->text())
                    ->setPrice($faker->numberBetween(5, 100) + 2)
            ;

            $manager->persist($carrier);
            $carriers[] = $carrier;
        }

        $orderss = [];
        for ($orde=0; $orde < 15; $orde++) { 
            $orders = new Orders();

            $orders->setCarrierName($faker->company())//$carriers[\mt_rand(3, count($carriers) - 1)]
                    ->setDelivery($faker->company())//$addresses[\mt_rand(0, count($addresses) - 1)]
                    ->setCarrierPrice($faker->numberBetween(3, 20))
                    ->setIsPaid(\mt_rand(0, 1))
                    ->setReference($faker->sentence())
                    ->setUser($users[mt_rand(0, count($users) -1)])
                    ->setStripeSessionId($faker->name())
                    //->setCreatedAt($faker->dateTime())
            ;

            $manager->persist($orders);
            $orderss[] = $orders;

            $price = ($faker->numberBetween(0, 100) + 2);
            $quantity = $faker->numberBetween(1, 10);

            for ($deta=0; $deta < count($orderss); $deta++) { 
                $details = new OrderDetails();

                $details->setMyOrder($orderss[mt_rand(0, count($orderss) -1)])
                        ->setPrice($price)
                        ->setProduct($faker->name())//$jeuxvideos[mt_rand(0, count($jeuxvideos) -1)]
                        ->setQuantity($quantity)
                        ->setTotal($price + $quantity)
                ;

                $manager->persist($details);
            }
        }
        
      
        $manager->flush();
    }
}
