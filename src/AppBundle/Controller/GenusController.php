<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Genus;
use AppBundle\Entity\GenusNote;
use AppBundle\Entity\GenusScientist;
use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GenusController extends Controller
{
    /**
     * @Route("/genus/new")
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $subFamily = $em->getRepository('AppBundle:SubFamily')
            ->findAny();

        $genus = new Genus();
        $genus->setName('Octopus' . rand(1, 100));
        $genus->setSubFamily($subFamily);
        $genus->setFunFact('Corporis impedit porro ab.');
        $genus->setSpeciesCount(rand(100, 99999));
        $genus->setFirstDiscoveredAt(new \DateTime('50 years'));

        $note = new GenusNote();
        $note->setUsername('AquaWeaver');
        $note->setUserAvatarFilename('ryan.jpeg');
        $note->setNote('I counted 8 legs... as they wrapped around me');
        $note->setCreatedAt(new \DateTime('-1 month'));
        $note->setGenus($genus);

        $user = $em->getRepository(User::class)->findOneBy(['email' => 'aquanaut1@example.org']);

        $genusScientist = new GenusScientist();
        $genusScientist->setGenus($genus);
        $genusScientist->setUser($user);
        $genusScientist->setYearsStudied(10);

        $em->persist($genus);
        $em->persist($genusScientist);
        $em->persist($note);
        $em->flush();

        return new Response(sprintf(
            '<html><body>Genus created! <a href="%s">%s</a></body></html>',
            $this->generateUrl('genus_show', ['slug' => $genus->getSlug()]),
            $genus->getName()
        ));
    }

    /**
     * @Route("/genus")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $genuses = $em->getRepository(Genus::class)->findAllPublishedOrderedByRecentlyActive();

        return $this->render('genus/list.html.twig', [
            'genuses' => $genuses
        ]);
    }


    /**
     * @Route("/genus/{slug}", name="genus_show")
     */
    public function showAction(Genus $genus)
    {
        $transformer = $this->get('app.markdown_transformer');
        $funFact = $transformer->parse($genus->getFunFact());

        $this->get('logger')
            ->info('Showing genus: ' . $genus->getName());

//        $recentNotes = $genus->getNotes()
//            ->filter(function(GenusNote $note) {
//                return $note->getCreatedAt() > new \DateTime('-3 months');
//            });

        $recentNotes = $this->getDoctrine()->getRepository(GenusNote::class)->findAllRecentNotesForGenus($genus);

        return $this->render('genus/show.html.twig', [
            'genus' => $genus,
            'funFact' => $funFact,
            'recentNotesCount' => count($recentNotes)
        ]);
    }

    /**
     * @Route("/genus/{slug}/notes", name="genus_show_notes")
     * @Method("GET")
     */
    public function getNotesAction(Genus $genus)
    {
        //$notes = $this->getDoctrine()->getRepository(GenusNote::class)->findBy(['genus' => $genus]);

        $notes = [];
        foreach ($genus->getNotes() as $note) {
            $notes[] = [
                'id' => $note->getId(),
                'username' => $note->getUsername(),
                'avatarUri' => '/images/' . $note->getUserAvatarFilename(),
                'note' => $note->getNote(),
                'date' => $note->getCreatedAt()->format('M d, Y')
            ];
        }

//        $notes = [
//            ['id' => 1, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Octopus asked me a riddle, outsmarted me', 'date' => 'Dec. 10, 2015'],
//            ['id' => 2, 'username' => 'AquaWeaver', 'avatarUri' => '/images/ryan.jpeg', 'note' => 'I counted 8 legs... as they wrapped around me', 'date' => 'Dec. 1, 2015'],
//            ['id' => 3, 'username' => 'AquaPelham', 'avatarUri' => '/images/leanna.jpeg', 'note' => 'Inked!', 'date' => 'Aug. 20, 2015'],
//        ];

        $data = ['notes' => $notes];

        return new JsonResponse($data);
    }

    /**
     * @Route("/genus/{genusId}/scientists/{userId}", name="genus_scientists_remove")
     * @Method("DELETE")
     */
    public function removeGenusScientistAction($genusId, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var GenusScientist $genusScientist */
        $genusScientist = $em->getRepository(GenusScientist::class)
            ->findOneBy(['user' => $userId, 'genus' => $genusId]);
        if (!$genusScientist) {
            throw $this->createNotFoundException('scientist not found');
        }

        $em->remove($genusScientist);
        $em->flush();

        return new Response(null, 204);
    }
}
