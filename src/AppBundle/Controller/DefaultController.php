<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Document;
use AppBundle\Entity\User;
use AppBundle\Form\DocumentType;
use AppBundle\Repository\DocumentRepository;
use AppBundle\Service\DocumentService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('::index.html.twig');
    }

    /**
     * @Route("/admin", name="admin")
     */
    public function adminAction()
    {
        if (!$this->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN')) {
            return $this->render(':admin:accessDenied.html.twig');
        }

        return $this->render(':admin:index.html.twig');
    }

    public function listDocumentAction(Request $request)
    {
        /** @var DocumentService $documentService */
        $documentService = $this->container->get('app_document');

        $documents = $documentService->getAllDocuments();

        return $this->render(':Document:list-document.html.twig', [
            'documents' => $documents
        ]);
    }

    public function editDocumentAction(Request $request)
    {
        $documentId = $request->get('id');

        /** @var DocumentRepository $documentRepository */
        $documentRepository = $this->container->get('doctrine')->getManager()->getRepository('AppBundle:Document');

        $document = $documentRepository->findOneBy(array('id' => 2));

        $form = $this->createForm('AppBundle\Form\DocumentType');

        if (null === $document) {
            $this->get('session')->getFlashBag()->add('error', 'No Document Found');

            $documents = $documentRepository->findAll();

            return $this->render(':Document:list-document.html.twig', [
                'documents' => $documents,
                'form' => $form->createView()
            ]);
        }

        $form->setData($document);

        return $this->render(':Document:edit-document.html.twig', [
            'documents' => [],
            'form' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addDocumentAction(Request $request)
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $entityManager = $this->container->get('doctrine')->getManager();

            $document = $form->getData();

            $entityManager->persist($document);
            $entityManager->flush();

            $this->get('session')->getFlashBag()->add('success', 'Document salvat cu success');
            $this->get('session')->getFlashBag()->add('error', 'Eroare la salvarea documentului');

            return $this->render(':Document:add-document.html.twig', [
                'form' => $form->createView()
            ]);

            $url = $this->generateUrl('app_list_document');
            return new RedirectResponse($url);
        }

        return $this->render(':Document:add-document.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function deleteDocumentAction(Request $request)
    {
        $form = $this->createForm('AppBundle\Form\DocumentType');

        return $this->render(':Document:add-document.html.twig', [
            'documents' => [],
            'form' => $form->createView()
        ]);
    }
}