<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Form\Category\AttachSubcategoryFormType;
use App\Form\Category\CreateCategoryFormType;
use App\Form\Category\DetachSubcategoryFormType;
use App\Form\Category\EditCategoryFormType;
use App\Model\Category\Domain\Entity\Category;
use App\Model\Category\Message\Command\AttachSubcategories;
use App\Model\Category\Message\Command\ChangePosition;
use App\Model\Category\Message\Command\CreateCategory;
use App\Model\Category\Message\Command\DetachSubcategories;
use App\Model\Category\Message\Command\EditCategory;
use App\ReadModel\Category\CategoryFetcher;
use App\ReadModel\User\UserFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class CategoriesController extends AbstractController
{
    private UserFetcher $userFetcher;
    private CategoryFetcher $categoryFetcher;
    private $name;

    public function __construct(
        UserFetcher $userFetcher,
        CategoryFetcher $categoryFetcher
    ) {
        $this->userFetcher = $userFetcher;
        $this->categoryFetcher = $categoryFetcher;
    }

    /**
     * @Route("/categories", name="admin_categories")
     */
    public function show(Request $request)
    {
        $name = $this->userFetcher->getProfileName($this->getUser()->getId());

        $categories = $this->categoryFetcher->getAll();

        return $this->render('admin/categories/categories.html.twig', [
            'profileFullName' => $name,
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/categories/create", name="create_category")
     */
    public function createCategory(Request $request, CreateCategory $category)
    {
        $name = $this->userFetcher->getProfileName($this->getUser()->getId());

        $form = $this->createForm(CreateCategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($category);
                $this->addFlash('success', 'Category successfully created');
                return $this->redirectToRoute('create_category');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            };
        }

        return $this->render('admin/categories/create_category.html.twig', [
            'profileFullName' => $name,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/categories/{id}/edit", name="edit_category")
     */
    public function editCategory($id, Request $request)
    {
        $name = $this->userFetcher->getProfileName($this->getUser()->getId());
        $category = $this->categoryFetcher->getById($id);

        $editCategory = new EditCategory(
            $category->id,
            $category->name,
            $category->description,
            $category->image
        );

        $form = $this->createForm(EditCategoryFormType::class, $editCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($editCategory);
                $this->addFlash('success', 'Category successfully edited');
                return $this->redirectToRoute('edit_category', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            };
        }

        return $this->render('admin/categories/edit_category.html.twig', [
            'profileFullName' => $name,
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    /**
     * @Route("/categories/{id}/edit/status", name="edit_category_status")
     */
    public function changeStatus($id, Request $request)
    {
        if (!$this->isCsrfTokenValid('change_status', $request->request->get('token'))) {
            return $this->redirectToRoute('edit_category', ['id' => $id]);
        }

        $changeStatus = new ChangeStatus(
            $id,
            $date = $request->get('status')
        );

        try {
            $this->dispatchMessage($changeStatus);
            $this->addFlash('success', 'Status successfully changed');
            return $this->redirectToRoute('edit_category', ['id' => $id]);
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('edit_category', ['id' => $id]);
        }
    }

    /**
     * @Route("/categories/{id}/edit/subcategories/attach", name="attach_subcategories")
     */
    public function attachSubcategories($id, Request $request)
    {
        $attachSubcategories = new AttachSubcategories($id);
        $form = $this->createForm(AttachSubcategoryFormType::class, $attachSubcategories);
        $form->handleRequest($request);

        $name = $this->userFetcher->getProfileName($this->getUser()->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($attachSubcategories);
                $this->addFlash('success', 'Subcategories successfully attached');
                return $this->redirectToRoute('attach_subcategories', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('attach_subcategories', ['id' => $id]);
            }
        }

        return $this->render('admin/categories/attach_subcategories.html.twig', [
            'profileFullName' => $name,
            'form' => $form->createView(),
            'categoryId' => $id
        ]);
    }

    /**
     * @Route("/categories/{id}/edit/subcategories/detach", name="detach_subcategories")
     */
    public function detachSubcategories($id, Request $request)
    {
        $detachSubcategories = new DetachSubcategories($id);
        $form = $this->createForm(DetachSubcategoryFormType::class, $detachSubcategories);
        $form->handleRequest($request);

        $name = $this->userFetcher->getProfileName($this->getUser()->getId());

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->dispatchMessage($detachSubcategories);
                $this->addFlash('success', 'Subcategories successfully deleted');
                return $this->redirectToRoute('detach_subcategories', ['id' => $id]);
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
                return $this->redirectToRoute('detach_subcategories', ['id' => $id]);
            }
        }

        return $this->render('admin/categories/detach_subcategories.html.twig', [
            'profileFullName' => $name,
            'form' => $form->createView(),
            'categoryId' => $id
        ]);
    }

    /**
     * @Route("/categories/{id}/edit/subcategories/position", name="subcategories_position")
     */
    public function showSubcategoriesPosition($id, Request $request)
    {
        $name = $this->userFetcher->getProfileName($this->getUser()->getId());

        $subcategories = $this->categoryFetcher->findAllByParent($id);
        $category = $this->categoryFetcher->getById($id);

        return $this->render('admin/categories/position.html.twig', [
            'profileFullName' => $name,
            'categories' => $subcategories,
            'parentCategory' => $category,
            'id' => $id
        ]);
    }

    /**
     * @Route("/categories/{id}/edit/category/position", name="category_position")
     */
    public function showCategoryPosition($id, Request $request)
    {
        $name = $this->userFetcher->getProfileName($this->getUser()->getId());

        /**@var $parentId string */
        if ($parentId = $this->categoryFetcher->findParentId($id)) {
            $category = $this->categoryFetcher->getById($parentId);
            $categories = $this->categoryFetcher->findAllByParent($parentId);
        } else {
            $category = null;
            $categories = $this->categoryFetcher->findAllByEmptyParent();
        }

        return $this->render('admin/categories/position.html.twig', [
            'profileFullName' => $name,
            'categories' => $categories,
            'parentCategory' => $category,
            'id' => $id
        ]);
    }

    /**
     * @Route("/categories/{id}/edit/position/change", name="change_category_position")
     */
    public function changePosition($id, Request $request, EntityManagerInterface $em)
    {
        if (!$this->isCsrfTokenValid('change_position', $request->request->get('token'))) {
            return $this->redirectToRoute('edit_category', ['id' => $id]);
        }

        $positions = json_decode($positions = $request->request->get('positions'), true);
        $changePosition = new ChangePosition($positions);

        try {
            $this->dispatchMessage($changePosition);
            return new Response(json_encode(['success'=>'Positions successfully changed']));
        } catch (\Exception $e) {
            return new Response(json_encode(['error' => $e]));
        }
    }

    /**
     * @Route("/categories/{id}/edit/delete", name="delete_category")
     */
    public function delete(Category $category, EntityManagerInterface $em, Request $request, $id)
    {
        if (!$this->isCsrfTokenValid('delete_category', $request->request->get('token'))) {
            return $this->redirectToRoute('edit_category', ['id' => $id]);
        }
        $em->remove($category);
        $em->flush();
        return $this->redirectToRoute('admin_categories');
    }

}