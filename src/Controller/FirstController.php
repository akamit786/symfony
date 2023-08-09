<?php

namespace App\Controller;

use App\Entity\TodoList;
use App\Form\TodoListType;
use App\Repository\TodoListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FirstController extends AbstractController
{
    #[Route (path: '/form{todoId}', name: 'index')]
    public function homepage(Request $request, EntityManagerInterface $em, TodoListRepository $todoListRepository, $todoId = null): Response
    {
        $todoList = new TodoList();
        $temp = 'pages/homepage.html.twig';

        if (!empty($todoId)) {
            $todoList = $todoListRepository->findOneBy(['id' => $todoId]);
            $temp = 'pages/edit_page.html.twig';
        }

        $form = $this->createForm(TodoListType::class, $todoList);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($todoList);
            $em->flush();
            return $this->redirectToRoute('index');
        }
        $todoListData = $todoListRepository->findAll();

        return $this->render($temp, [
            'form' => $form,
            'todoListData' => $todoListData,
        ]);
    }

    #[Route (path: '/delete/{todoId}', name: 'delete')]
    public function delete(TodoListRepository $todoListRepository,  EntityManagerInterface $em,$todoId): Response
    {
        $todoList = $todoListRepository->findOneBy(['id' => $todoId]);
        $em->remove($todoList);
        $em->flush();
        return $this->redirectToRoute('index');
    }
}