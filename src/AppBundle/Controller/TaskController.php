<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Task;
use AppBundle\Repository\TaskRepository;
use AppBundle\Form\TaskType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @Route("/tasks", name="task_")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function list(TaskRepository $repo)
    {
        $tasks = $repo->findByNotDone();
        return $this->render('task/list.html.twig', ['tasks' => $tasks, 'list_type' => 'à faire']);
    }

    /**
     * @Route("/done", name="done_list")
     */
    public function doneList(TaskRepository $repo)
    {
        $tasks = $repo->findByDone();
        return $this->render('task/list.html.twig', ['tasks' => $tasks, 'list_type' => 'faites']);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $task->setCreatedBy($user);

            $manager->persist($task);
            $manager->flush();

            $this->addFlash('success', sprintf('La tâche %s a bien été ajoutée.', $task->getTitle()));

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/{id}/edit", name="edit", requirements={"id"="\d+"})
     */
    public function edit(Task $task, Request $request, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('edit', $task);
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash('success', sprintf('La tâche %s a bien été modifiée.', $task->getTitle()));

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/{id}/toggle", name="toggle", requirements={"id"="\d+"})
     */
    public function toggleTask(Task $task, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('toggle', $task);
        $task->toggle(!$task->isDone());
        $manager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/{id}/delete", name="delete", requirements={"id"="\d+"})
     */
    public function deleteTask(Task $task, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('delete', $task);
        $title = $task->getTitle();
        $manager->remove($task);
        $manager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été supprimée.', $title));

        return $this->redirectToRoute('task_list');
    }
}
