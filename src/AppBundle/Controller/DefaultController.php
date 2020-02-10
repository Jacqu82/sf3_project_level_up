<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\Serializer\Encoder;
use AppBundle\Service\StopWatchService;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\MimeType\FileinfoMimeTypeGuesser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\YamlEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @author Jacek Wesołowski <jacqu25@yahoo.com>
 */
class DefaultController extends Controller
{
    /**
     * @Route("/test")
     */
    public function testAction()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        //$email = ['email' => $user->getEmail()];
        //$value = $propertyAccessor->getValue($user, 'email');

        foreach ($users as $user) {
            if (null === $user->getUniversityName()) {
                $propertyAccessor->setValue($user, 'universityName', 'foo_bar');
            }
        }

        $values = [];
        foreach ($users as $key => $user) {
            $propertyAccessor->setValue($user, 'is_scientist', true);
            if ($key % 2 === 0) {
                $propertyAccessor->setValue($user, 'is_scientist', false);
            }
            $values['users'][] = [
                'email' => $propertyAccessor->getValue($user, 'email'),
                'is_scientist' => $propertyAccessor->getValue($user, 'is_scientist'),
                'university_name' => $propertyAccessor->getValue($user, 'university_name'),
            ];
        }

        return new JsonResponse($values);
    }

    /**
     * @Route("/serialize")
     */
    public function serilaizeAction(Encoder $encoder)
    {
        $encoders = [new XmlEncoder(), new JsonEncoder(), new CsvEncoder(), new YamlEncoder()];
        $normalizer = new ObjectNormalizer(null, new CamelCaseToSnakeCaseNameConverter());
        $normalizer->setIgnoredAttributes(array('studiedGenuses'));
        $normalizers = array($normalizer);
        $serializer = new Serializer($normalizers, $encoders);


        $user = $this->getDoctrine()->getRepository(User::class)->find(1);
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();

        $toArray = $serializer->normalize($user);

        $format = $this->getParameter('format_to_encode');
        $data = ['users' => []];
        foreach ($users as $user) {
            $data['users'][] = $user;
        }

        $data = $serializer->serialize($data, $format);
        $encoder->encode($data, $format);


//        $format = $serializer->encode($toArray, 'json');
//        $backToArray = $serializer->decode($format, 'json');
//        $toObject = $serializer->denormalize($backToArray, User::class);
//        $deserialize = $serializer->deserialize($format, User::class, 'json');
//
//        dump($deserialize);die;

//        $data = ['users' => []];
//        foreach ($users as $user) {
//            $data['users'][] = $user;
//        }
//
//        $json = $serializer->serialize($data, 'json');


        return new Response('<body>Pliczek zapisany w formacie: ' . $format . '</body>');
    }

    /**
     * @Route("/jms")
     */
    public function jmsSerialize()
    {
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $user = $this->getDoctrine()->getRepository(User::class)->find(1);

        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $serializer = $this->get('jms_serializer');
        $data = ['users' => []];
        foreach ($users as $user) {
            $data['users'][] = $user;
        }

        $json = $serializer->serialize($data, 'xml', $context);

        $file = sprintf('%s/users.xml', $this->getParameter('kernel.project_dir'));
        file_put_contents($file, $json, FILE_APPEND);

        $response = new Response($json);
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }

    /**
     * @Route("/stop-watch")
     */
    public function stopWatchAction(StopWatchService $stopWatchService)
    {
        $result = $stopWatchService->testStopWatch();

        return new Response('<body>' . $result . '</body>');
    }

    /**
     * @Route("/entities", name="entities")
     */
    public function entitiesAction()
    {
        $directory = sprintf('%s/src/AppBundle/Entity', $this->getParameter('kernel.project_dir'));
        $finder = new Finder();
        $files = $finder->in($directory);

        $entities = [];
        foreach ($files as $file) {
            $entities[] = lcfirst(substr($file->getRelativePathname(), 0, -4));
        }

        return $this->render(
            'default/list.html.twig',
            [
                'entities' => $entities,
            ]
        );
    }

    /**
     * @Route("/entity-files/{entity}", name="file_entity")
     */
    public function entityFilesAction(string $entity)
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
            'default/show.html.twig',
            [
                'entityFiles' => $entityFiles,
                'entity' => $entity
            ]
        );
    }

    /**
     * @Route("/download/{entity}/{file}")
     */
    public function downloadAction(string $entity, string $file)
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
}
