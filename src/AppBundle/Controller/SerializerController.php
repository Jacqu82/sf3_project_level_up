<?php

namespace AppBundle\Controller;

use AppBundle\Form\ExportType;
use AppBundle\Form\ImportType;
use AppBundle\Service\Export\Serializer;
use AppBundle\Service\Import\ImportProvider;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class SerializerController extends Controller
{
    /**
     * @Route("/entities", name="entities")
     */
    public function entitiesAction(): Response
    {
        return $this->render(
            'serializer/list.html.twig',
            [
                'entities' => $this->getEntityNames(),
            ]
        );
    }

    private function getEntityNames(): array
    {
        $directory = sprintf('%s/src/AppBundle/Entity', $this->getParameter('kernel.project_dir'));
        $finder = new Finder();

        $files = $finder->in($directory);

        $entities = [];
        foreach ($files as $file) {
            $entities[] = lcfirst(substr($file->getRelativePathname(), 0, -4));
        }

        return $entities;
    }

    /**
     * @Route("/entity-files/{entity}", name="file_entity")
     */
    public function entityFilesAction(string $entity): Response
    {
        $directory = sprintf('%s/web/export/%s', $this->getParameter('kernel.project_dir'), $entity);
        if (!file_exists($directory)) {
            $this->addFlash(
                'danger',
                sprintf('Nie znaleziono katalogu %s. Utwórz export dla encji: %s.', $entity, $entity)
            );
            return $this->redirectToRoute('entities');
        }

        $finder = new Finder();
        $files = $finder->in($directory);
        $entityFiles = [];
        foreach ($files as $file) {
            $entityFiles[] = $file->getRelativePathname();
        }

        return $this->render(
            'serializer/show.html.twig',
            [
                'entityFiles' => $entityFiles,
                'entity' => $entity
            ]
        );
    }

    /**
     * @Route("/download/{entity}/{file}")
     */
    public function downloadAction(string $entity, string $file): Response
    {
        $fileToDownload = sprintf('%s/web/export/%s/%s', $this->getParameter('kernel.project_dir'), $entity, $file);
        if (!file_exists($fileToDownload)) {
            throw new FileNotFoundException($fileToDownload);
        }

        $response = new BinaryFileResponse($fileToDownload);

        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();
        // Set the mimetype with the guesser or manually
        if ($mimeTypeGuesser::isSupported()) {
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guess($fileToDownload));
        } else {
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        $filenameFallback = preg_replace('#^.*\.#', md5($file) . '.', $file);
        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $file,
            $filenameFallback
        );

        return $response;
    }

    /**
     * @Route("/export-data")
     */
    public function exportDataToFileAction(Request $request, Serializer $serializer): Response
    {
        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settingsToSerialize = $form->getData();
            $entity = $settingsToSerialize['entity'];
            $format = $settingsToSerialize['format'];
            $backToImport = $settingsToSerialize['backToImport'];
            $dataId = $settingsToSerialize['dataId'];

            $serializer->serialize($entity, $format, $backToImport, $dataId);

            $this->addFlash('success', sprintf('Export encji %s do pliczku w formacie: %s ;)', $entity, $format));
        }

        return $this->render(
            'serializer/export.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/import-data")
     */
    public function deserializeAction(Request $request, ImportProvider $importProvider): Response
    {
        $form = $this->createForm(ImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['file']->getData();
            $entities = $this->getEntityNames();
            $originalFilename = $importProvider->moveUploadedFile($uploadedFile, $entities);

            $this->addFlash(
                'success',
                sprintf('Plik %s poprawnie uplodowany i gotowy do importu ;)', $originalFilename)
            );
        }

        return $this->render(
            'serializer/import.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}