<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Service\Serializer\Encoder;
use AppBundle\Service\StopWatchService;
use JMS\Serializer\SerializationContext;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
}
