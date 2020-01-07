<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\BookFilter;
use AppBundle\Service\ImageUploader;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Book controller.
 *
 * @Route("books")
 */
class BookController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/", name="books_index", methods={"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $bookFilter = new BookFilter();
        $filterForm = $this->createForm('AppBundle\Form\BookFilterType', $bookFilter);
        $filterForm->handleRequest($request);

        $em = $this->getDoctrine()->getManager();
        $query = $em->getRepository('AppBundle:Book')->getBooksWithFilter($bookFilter);
        $books = $query->getResult();

        $editFormViews = [];
        foreach ($books as $book){
            $editFormViews[] = $this->createEditForm($book)->createView();
        }

        return $this->render('book/index.html.twig', array(
            'books' => $books,
            'filter_form' => $filterForm->createView(),
            'edit_forms' => $editFormViews,
        ));
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/new", name="books_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request, ImageUploader $imageUploader)
    {
        $book = new Book();
        $form = $this->createForm('AppBundle\Form\BookType', $book, ['image_required' => True]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setImage($imageUploader->upload($book->getImage()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('books_show', array('id' => $book->getId()));
        }

        return $this->render('book/new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a book entity.
     *
     * @Route("/{id}", name="books_show", methods={"GET"})
     */
    public function showAction(Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);

        return $this->render('book/show.html.twig', array(
            'book' => $book,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * @Route("/{id}/edit", name="books_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Book $book, ImageUploader $imageUploader)
    {
        $imagePath = $book->getImage();
        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createEditForm($book);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            if ($book->getImage() !== $imagePath && $book->getImage() !== null) {
                $book->setImage($imageUploader->upload($book->getImage()));
            }
            else{
                $book->setImage($imagePath);
            }
            $this->getDoctrine()->getManager()->flush();

            if ($request->isXmlHttpRequest()) {
                $authors = [];
                foreach ($book->getAuthors() as $author){
                    $authors[] = $author->getName();
                }
                $arr = ['title' => $book->getTitle(),
                    'description' => $book->getDescription(),
                    'publicationDate' => $book->getPublicationDate()->format('d-m-Y'),
                    'authors' => $authors,
                    'image' => $book->getImage()];
                return new Response(json_encode($arr), 200);
            }

            return $this->redirectToRoute('books_show', array('id' => $book->getId()));
        }

        return $this->render('book/edit.html.twig', array(
            'book' => $book,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a book entity.
     *
     * @Route("/{id}", name="books_delete", methods={"DELETE"})
     */
    public function deleteAction(Request $request, Book $book)
    {
        $form = $this->createDeleteForm($book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($book);
            $em->flush();
        }

        return $this->redirectToRoute('books_index');
    }

    /**
     * Creates a form to delete a book entity.
     *
     * @param Book $book The book entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Book $book)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('books_delete', array('id' => $book->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * @param Book $book The book entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Book $book)
    {
        return $this->get('form.factory')
            ->createNamedBuilder('book' . $book->getId(), 'AppBundle\Form\BookType', $book, ['image_required' => False])
            ->setAction($this->generateUrl('books_edit', array('id' => $book->getId())))
            ->getForm();
    }
}
