<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EventController extends AbstractController {
    #[Route('/', name: 'app_event')]
    public function index(): Response {
        return $this->render(
            'event/index.html.twig',
        );
    }


    /**
     * Formulaire create Event
     *
     * @param  Request  $request
     * @param  ManagerRegistry  $doctrine
     * @return void
     */
    #[Route('/evenement/creer', name: 'event_create')]
    public function eventCreate(Request $request, ManagerRegistry $doctrine) {
        $event = new Event;
        $form = $this->createForm(EventType::class, $event, ['validation_groups' => ['Default', 'create']]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if ($file = $event->getPosterFile()) {
                $filename = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move('./image/events', $filename);
                $event->setPoster($filename);
            }

            $em = $doctrine->getManager();
            $em->persist($event);

            $em->flush();

            return $this->redirect('/evenement');
        }

        return $this->render('/event/creer.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/evenement/{id}/edit', name: 'event_edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, Event $event) {

        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($file = $event->getPosterFile()) {
            if ($poster = $event->getPoster()) {
                @unlink('./image/events/' . $poster);
            }
            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move('./image/events', $filename);
            $event->setPoster($filename);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->flush();
            return $this->redirect('/evenement');
        }

        return $this->render('/event/edit.html.twig', [
            'form' => $form,
            'event' => $event,
        ]);
    }
    #[Route('/evenement/{id}/delete', name: 'event_delete')]
    public function delete(Request $request, ManagerRegistry $doctrine, Event $event) {

        if ($this->isCsrfTokenValid('delete-' . $event->getId(), $request->get('token'))) {
            if ($poster = $event->getPoster()) {
                @unlink('./image/events/' . $poster);
            }
            $em = $doctrine->getManager();
            $em->remove($event);
            $em->flush();
        }
        return $this->redirect('/evenement');
    }

    #[Route('/evenement', name: 'app_eventList')]
    public function evenementList(ManagerRegistry $doctrine): Response {
        $events = $doctrine->getRepository(Event::class)->findAll();

        return $this->render(
            '/event/evenement.html.twig',
            [
                'events' => $events,
            ]
        );
    }

    #[Route('/event/{id}', requirements: ['id' => '\d+'])]
    #[Route('/evenement/{id}', name: 'app_eventId', requirements: ['id' => '\d+'])]
    public function evenement(Event $event): Response {
        return $this->render(
            '/event/eventId.html.twig',
            ['event' => $event],
        );
    }
}
