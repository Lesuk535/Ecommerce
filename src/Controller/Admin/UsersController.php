<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\Users\CreateUserFormType;
use App\Form\Users\UserFilterFormType;
use App\Form\Users\UserFormType;
use App\Model\User\Domain\Entity\User;
use App\Model\User\Message\Command\User\CreateUser;
use App\Model\User\Message\Command\User\EditUser;
use App\ReadModel\User\DTO\UserFilter;
use App\ReadModel\User\UserFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class UsersController extends AbstractController
{
    private UserFetcher$userFetcher;

    /**
     * UsersController constructor.
     * @param $userFetcher
     */
    public function __construct(UserFetcher $userFetcher)
    {
        $this->userFetcher = $userFetcher;
    }

    /**
     * @Route("/users", name="users")
     */
    public function users(Request $request, UserFilter $filter)
    {
        $name = $this->userFetcher->getProfileName($this->getUser()->getId());
        $form = $this->createForm(UserFilterFormType::class, $filter);

        $form->handleRequest($request);

        $users = $this->userFetcher->getAll(
            $filter,
            $request->query->getInt('page', 1),
            10,
            $request->query->get('sort', 'date'),
            $request->query->get('direction', 'desc')
            );

        return $this->render('admin/users/users.html.twig', [
            'profileFullName' => $name,
            'users' => $users,
            'form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}", name="show_user")
     */
    public function showUser(string $id, Request $request)
    {
        $showUser = $this->userFetcher->getById($id);

        $editUser = new EditUser(
            $id,
            $this->getUser()->getId(),
            $showUser->email,
            $showUser->role,
            $showUser->avatar
        );

        $form = $this->createForm(UserFormType::class, $editUser);
        $form->handleRequest($request);

        $name = $this->userFetcher->getProfileName($editUser->profileId);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($editUser);
                return $this->redirectToRoute('show_user', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            };
        }

        return $this->render('admin/users/show_user.html.twig', [
            'profileFullName' => $name,
            'form' => $form->createView(),
            'user' => $showUser
        ]);
    }

    /**
     * @Route("/users/create", name="create_user", priority=2)
     */
    public function createUser(Request $request, CreateUser $createUser)
    {
        $form = $this->createForm(CreateUserFormType::class, $createUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($createUser);
                $this->addFlash('success', 'User successfully created');
                return $this->redirectToRoute('create_user');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            };
        }

        $name = $this->userFetcher->getProfileName($this->getUser()->getId());

        return $this->render('admin/users/create_user.html.twig', ['profileFullName' => $name, 'form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/ban", name="user_ban")
     */
    public function ban(string $id, Request $request, EntityManagerInterface $em)
    {
        $user = $this->userFetcher->getById($id);

        if (!$this->isCsrfTokenValid('change_status', $request->request->get('token'))) {
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }

        if ($user->id === $this->getUser()->getId()) {
            $this->addFlash('error', 'Unable to ban yourself.');
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }

        try {
            $banUser = $em->getRepository(User::class)->find($id);
            $banUser->ban();
            $em->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }

        return $this->redirectToRoute('show_user', ['id' => $id]);
    }

    /**
     * @Route("/users/{id}/activate", name="user_activate")
     */
    public function activate(string $id, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('change_status', $request->request->get('token'))) {
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }

        try {
            $user = $em->getRepository(User::class)->find($id);
            $user->activate();
            $em->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('show_user', ['id' => $id]);
        }

        return $this->redirectToRoute('show_user', ['id' => $id]);
    }
}