<?php


namespace AppBundle\DataFixtures;


use AppBundle\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BookFixtures extends Fixture
{
    private $container;
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function randString($length){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }

    private function randDate(){
        $int= rand(631152000,1893456000);  // 1-1-1990, 1-1-2030
        $date = new \DateTime();
        $date->setTimestamp($int);
        return $date;
    }

    private function getImages(){
        $images = [];
        $path = $this->container->getParameter('images_directory');
        $extensions = array('png', 'jpg', 'jpeg');

        $directoryIterator = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
        $iteratorIterator  = new RecursiveIteratorIterator($directoryIterator, RecursiveIteratorIterator::LEAVES_ONLY);

        foreach ($iteratorIterator as $file) {
            if (in_array($file->getExtension(), $extensions)) {
                $images[] = $file->getFilename();
            }
        }
        return $images;
    }

    private function addRandBooks($booksCount, $authorsCount, ObjectManager $manager){
        $images = $this->getImages();
        $authors = $manager->getRepository('AppBundle:Author')->findAll();
        if ($authorsCount > count($authors)){ return; }
        $books = [];
        for ($i = 0; $i < $booksCount; $i++){
            $books[] = new Book();
            end($books)->setTitle($this->randString(10));
            end($books)->setDescription($this->randString(20));
            end($books)->setPublicationDate($this->randDate());
            end($books)->setImage($images[rand(0, count($images) - 1)]);
            $arr = [];
            for ($j = 0; $j < $authorsCount; $j++){
                do {
                    $a = $authors[rand(0, count($authors) - 1)];
                }while (in_array($a, $arr));
                $arr[] = $a;
            }
            end($books)->setAuthors($arr);
            $manager->persist(end($books));
        }
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->addRandBooks(5, 1, $manager);
        $this->addRandBooks(3, 2, $manager);
        $this->addRandBooks(2, 3, $manager);
    }

    public function getDependencies()
    {
        return array(
            AuthorFixtures::class,
        );
    }
}