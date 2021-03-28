<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Category;
use App\Entity\City;
use App\Entity\ActiveType;
use App\Entity\CategoryType;

class CategoryController extends AbstractController
{
    /**
     * @Route("/admin/category", name="admin_category")
     */
    public function admin_index(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach($categories as $category) {
            $categorytype = $this->getDoctrine()->getRepository(CategoryType::class)->find($category->getTypeId());
            $category->type = $categorytype->getName();
            $activetype = $this->getDoctrine()->getRepository(ActiveType::class)->find($category->getActiveTypeId());
            $category->activetype = $activetype->getName();
            $city = $this->getDoctrine()->getRepository(City::class)->find($category->getCityId());
            $category->city = $city->getName();
        }
        return $this->render('pages/admin/category/index.html.twig', [
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/admin/category/create", name="admin_category_create")
     */
    public function admin_create(): Response
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/admin/category/create.html.twig', [
            'categories' => $categories,
            'categorytypes' => $categorytypes,
            'cities' => $cities,
            'activetypes' => $activetypes,
        ]);
    }

    /**
     * @Route("/admin/category/store", name="admin_category_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        $city_id = $request->request->get("city_id");
        $type_id = $request->request->get("type_id");
        $active_type_id = $request->request->get("active_type_id");
        $input = [
            'city_id' => $city_id,
            'type_id' => $type_id,
            'active_type_id' => $active_type_id,
        ];
        $constraints = new Assert\Collection([
            'city_id' => [new Assert\NotBlank],
            'type_id' => [new Assert\NotBlank],
            'active_type_id' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
            $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
            $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
            return $this->render('pages/admin/category/create.html.twig', [
                'categories' => $categories,
                'categorytypes' => $categorytypes,
                'cities' => $cities,
                'activetypes' => $activetypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $category = new Category();
        $city_id = $request->request->get('city_id');
        $category->setCityId($city_id);
        $type_id = $request->request->get('type_id');
        $category->setTypeId($type_id);
        $active_type_id = $request->request->get('active_type_id');
        $category->setActiveTypeId($active_type_id);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($category);
        $doct->flush();
        return $this->redirectToRoute('admin_category');
    }

    /**
     * @Route("/admin/category/edit/{id}", name="admin_category_edit")
     */
    public function admin_edit($id): Response
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/admin/category/edit.html.twig', [
            'category' => $category,
            'categorytypes' => $categorytypes,
            'cities' => $cities,
            'activetypes' => $activetypes,
        ]);
    }

    /**
     * @Route("/admin/category/update/{id}", name="admin_category_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
        $city_id = $request->request->get("city_id");
        $type_id = $request->request->get("type_id");
        $active_type_id = $request->request->get("active_type_id");
        $input = [
            'city_id' => $city_id,
            'type_id' => $type_id,
            'active_type_id' => $active_type_id,
        ];
        $constraints = new Assert\Collection([
            'city_id' => [new Assert\NotBlank],
            'type_id' => [new Assert\NotBlank],
            'active_type_id' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
            $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
            $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
            return $this->render('pages/admin/category/edit.html.twig', [
                'categories' => $categories,
                'categorytypes' => $categorytypes,
                'cities' => $cities,
                'activetypes' => $activetypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $category = $doct->getRepository(Category::class)->find($id);
        $city_id = $request->request->get('city_id');
        $category->setCityId($city_id);
        $type_id = $request->request->get('type_id');
        $category->setTypeId($type_id);
        $active_type_id = $request->request->get('active_type_id');
        $category->setActiveTypeId($active_type_id);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_category', [
            'id' => $category->getId()
        ]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="admin_category_delete")
     */
    public function admin_delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $category = $doct->getRepository(Category::class)->find($id);
        $doct->remove($category);
        $doct->flush();
        return $this->redirectToRoute('admin_category', [
            'id' => $category->getId()
        ]);
    }

    /**
     * @Route("/admin/categorytype", name="admin_categorytype")
     */
    public function admin_type_index(): Response
    {
        $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        return $this->render('pages/admin/categorytype/index.html.twig', [
            'categorytypes' => $categorytypes
        ]);
    }

    /**
     * @Route("/admin/categorytype/create", name="admin_categorytype_create")
     */
    public function admin_type_create(): Response
    {
        return $this->render('pages/admin/categorytype/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/categorytype/store", name="admin_categorytype_store")
     */
    public function admin_type_store(Request $request, ValidatorInterface $validator): Response
    {
        $name = $request->request->get("name");
        $input = [
            'name' => $name,
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            return $this->render('pages/admin/categorytype/create.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $categorytype = new CategoryType();
        $name = $request->request->get('name');
        $categorytype->setName($name);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($categorytype);
        $doct->flush();
        return $this->redirectToRoute('admin_categorytype');
    }

    /**
     * @Route("/admin/categorytype/edit/{id}", name="admin_categorytype_edit")
     */
    public function admin_type_edit($id): Response
    {
        $categorytype = $this->getDoctrine()->getRepository(CategoryType::class)->find($id);
        return $this->render('pages/admin/categorytype/edit.html.twig', [
            'categorytype' => $categorytype,
        ]);
    }

    /**
     * @Route("/admin/categorytype/update/{id}", name="admin_categorytype_update")
     */
    public function admin_type_update($id, Request $request, ValidatorInterface $validator): Response
    {
        $name = $request->request->get("name");
        $input = [
            'name' => $name,
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            $categorytype = $this->getDoctrine()->getRepository(CategoryType::class)->find($id);
            return $this->render('pages/admin/categorytype/edit.html.twig', [
                'categorytype' => $categorytype,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $categorytype = $doct->getRepository(CategoryType::class)->find($id);
        $name = $request->request->get('name');
        $categorytype->setName($name);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_categorytype', [
            'id' => $categorytype->getId()
        ]);
    }

    /**
     * @Route("/admin/categorytype/delete/{id}", name="admin_categorytype_delete")
     */
    public function admin_type_delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $categorytype = $doct->getRepository(CategoryType::class)->find($id);
        $doct->remove($categorytype);
        $doct->flush();
        return $this->redirectToRoute('admin_categorytype', [
            'id' => $categorytype->getId()
        ]);
    }
}
