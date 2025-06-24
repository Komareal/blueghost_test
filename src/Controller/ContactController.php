<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactForm;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ContactController extends AbstractController
{

    /**
     * Deletes a contact entity by id.
     *
     * @param Request $request The current request
     * @param ContactRepository $contactRepository The contact repository
     * @param EntityManagerInterface $entityManager The entity manager
     * @param int $id The numeric id of the contact
     * @return Response Redirects to the contact index
     */
    #[Route('/{id}/delete', name: 'app_contact_delete', methods: ['POST'])]
    public function delete(Request $request, ContactRepository $contactRepository, EntityManagerInterface $entityManager, int $id): Response
    {
        $contact = $contactRepository->find($id);
        if (!$contact) {
            throw new NotFoundHttpException();
        }
        if ($this->isCsrfTokenValid('delete' . $contact->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contact);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * Edits an existing contact entity using a form, identified by slug.
     *
     * @param Request $request The current request
     * @param ContactRepository $contactRepository The contact repository
     * @param EntityManagerInterface $entityManager The entity manager
     * @param string $slug The unique slug of the contact
     * @return Response Renders the edit form or redirects on success
     */
    #[Route('/{slug}', name: 'app_contact_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactRepository $contactRepository, EntityManagerInterface $entityManager, string $slug): Response
    {
        $contact = $contactRepository->findOneBy(['slug' => $slug]);
        if (!$contact) {
            throw new NotFoundHttpException();
        }
        $form = $this->createForm(ContactForm::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setSlug(
                $contactRepository->generateUniqueSlug($contact->getName(), $contact->getSurname(), $contact->getId())
            );
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/edit.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    /**
     * Displays the contact index page (table is rendered here, but data are fetched via API).
     * Validates the page parameter to ensure it is a valid number.
     *
     * @param Request $request The current request
     * @param ValidatorInterface $validator The validator for page parameter
     * @return Response Renders the contact index page
     */
    #[Route(name: 'app_contact_index', methods: ['GET'])]
    public function index(Request $request,
        ValidatorInterface $validator,
    ): Response
    {
        $pageNumber = $request->get('page', 1);
        $errors = $validator->validate($pageNumber, [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'digit']),
            new Assert\GreaterThanOrEqual(1),
        ]);
        if (count($errors) > 0) {
            return $this->redirectToRoute('app_contact_index', ['page' => 1], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/index.html.twig', [
            'page' => $pageNumber,
        ]);
    }

    /**
     * Creates a new contact entity using a form.
     * Generates a unique slug for the contact based on name and surname.
     *
     * @param Request $request The current request
     * @param EntityManagerInterface $entityManager The entity manager
     * @param ContactRepository $contactRepository The contact repository
     * @return Response Renders the new contact form or redirects on success
     */
    #[Route('/contact/new', name: 'app_contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactForm::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact->setSlug(
                $contactRepository->generateUniqueSlug($contact->getName(), $contact->getSurname())
            );
            $entityManager->persist($contact);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    /**
     * Returns a paginated list of contacts as JSON for table use.
     * Uses slug as the identifier in the returned data.
     *
     * @param Request $request The current request
     * @param ContactRepository $contactRepository The contact repository
     * @param ValidatorInterface $validator The validator for page parameter
     * @return JsonResponse Paginated contacts data
     */
    #[Route('/api/table', name: 'app_contact_table', methods: ['GET'])]
    public function table(Request $request,
        ContactRepository $contactRepository,
        ValidatorInterface $validator,
    ): JsonResponse
    {
        $pageNumber = $request->get('page', 1);
        $errors = $validator->validate($pageNumber, [
            new Assert\NotBlank(),
            new Assert\Type(['type' => 'digit']),
            new Assert\GreaterThanOrEqual(1),
        ]);
        $pageSize = 10;
        $contactCount = $contactRepository->count();

        $totalPages = (int) ceil($contactCount / $pageSize);

        // If invalid page, just return page 1 data
        if (count($errors) > 0) {
            $pageNumber = 1;
        }

        if ($contactCount <= $pageSize) {
            return new JsonResponse([
                'data' => $contactRepository->findAll(),
                'totalPages' => 1,
                'page' => 1,
            ]);
        }

        if ($pageNumber > $totalPages) {
            $pageNumber = $totalPages;
        }

        $contacts = $contactRepository->findBy([], ['id' => 'ASC'], $pageSize, ($pageNumber - 1) * $pageSize);

        return new JsonResponse([
            'data' => $contacts,
            'totalPages' => $totalPages,
            'page' => $pageNumber,
        ]);
    }
}
