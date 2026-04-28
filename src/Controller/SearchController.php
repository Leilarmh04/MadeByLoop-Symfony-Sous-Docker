<?php

namespace App\Controller;

use App\Form\ProductFormType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search')]
    public function index(
        Request $request,
        ProductRepository $productRepo,
        UserRepository $userRepo,
        CategoryRepository $categoryRepo
    ): Response {
        $query = trim($request->query->get('q', ''));
        $type = $request->query->get('type', 'all');
        $categoryId = $request->query->get('category');
        $priceMin = $request->query->get('price_min');
        $priceMax = $request->query->get('price_max');
        $selectedSizes = $request->query->all('sizes');
        $selectedColors = $request->query->all('colors');
        $sort = $request->query->get('sort', 'newest');

        $products = [];
        $users = [];
        $shops = [];

        // Recherche produits
        if ($type === 'all' || $type === 'products') {
            $qb = $productRepo->createQueryBuilder('p')
                ->leftJoin('p.category', 'c')
                ->leftJoin('p.seller', 's');

            if ($query !== '') {
                $qb->andWhere('p.name LIKE :q OR p.description LIKE :q')
                   ->setParameter('q', '%' . $query . '%');
            }

            if ($categoryId) {
                $qb->andWhere('p.category = :cat')
                   ->setParameter('cat', $categoryId);
            }

            if ($priceMin !== null && $priceMin !== '') {
                $qb->andWhere('p.price >= :pmin')
                   ->setParameter('pmin', (float) $priceMin);
            }

            if ($priceMax !== null && $priceMax !== '') {
                $qb->andWhere('p.price <= :pmax')
                   ->setParameter('pmax', (float) $priceMax);
            }

            if (!empty($selectedSizes)) {
                $sizeConditions = [];
                foreach ($selectedSizes as $i => $size) {
                    $sizeConditions[] = "p.sizes LIKE :size_$i";
                    $qb->setParameter("size_$i", '%"' . $size . '"%');
                }
                $qb->andWhere('(' . implode(' OR ', $sizeConditions) . ')');
            }

            if (!empty($selectedColors)) {
                $colorConditions = [];
                foreach ($selectedColors as $i => $color) {
                    $colorConditions[] = "p.colors LIKE :color_$i";
                    $qb->setParameter("color_$i", '%"' . $color . '"%');
                }
                $qb->andWhere('(' . implode(' OR ', $colorConditions) . ')');
            }

            switch ($sort) {
                case 'price_asc':
                    $qb->orderBy('p.price', 'ASC');
                    break;
                case 'price_desc':
                    $qb->orderBy('p.price', 'DESC');
                    break;
                default:
                    $qb->orderBy('p.createdAt', 'DESC');
                    break;
            }

            $products = $qb->getQuery()->getResult();
        }

        // Recherche utilisateurs
        if ($type === 'all' || $type === 'users') {
            if ($query !== '') {
                $users = $userRepo->createQueryBuilder('u')
                    ->where('u.username LIKE :q OR u.email LIKE :q')
                    ->setParameter('q', '%' . $query . '%')
                    ->getQuery()
                    ->getResult();
            }
        }

        // Recherche boutiques
        if ($type === 'all' || $type === 'shops') {
            $shopQb = $userRepo->createQueryBuilder('u')
                ->innerJoin('u.products', 'p');

            if ($query !== '') {
                $shopQb->andWhere('u.username LIKE :q OR u.email LIKE :q')
                       ->setParameter('q', '%' . $query . '%');
            }

            $shopQb->groupBy('u.id')
                   ->having('COUNT(p.id) > 0');

            $shops = $shopQb->getQuery()->getResult();
        }

        $categories = $categoryRepo->findAll();

        return $this->render('search/index.html.twig', [
            'query' => $query,
            'type' => $type,
            'products' => $products,
            'users' => $users,
            'shops' => $shops,
            'categories' => $categories,
            'currentCategory' => $categoryId,
            'currentPriceMin' => $priceMin,
            'currentPriceMax' => $priceMax,
            'selectedSizes' => $selectedSizes,
            'selectedColors' => $selectedColors,
            'currentSort' => $sort,
            'allSizes' => array_keys(ProductFormType::SIZES),
            'allColors' => array_keys(ProductFormType::COLORS),
        ]);
    }

    #[Route('/search/suggest', name: 'app_search_suggest')]
    public function suggest(Request $request, ProductRepository $productRepo, UserRepository $userRepo): JsonResponse
    {
        $query = trim($request->query->get('q', ''));
        $results = [];

        if (strlen($query) >= 2) {
            $products = $productRepo->createQueryBuilder('p')
                ->where('p.name LIKE :q')
                ->setParameter('q', '%' . $query . '%')
                ->setMaxResults(5)
                ->getQuery()
                ->getResult();

            foreach ($products as $product) {
                $results[] = [
                    'type' => 'product',
                    'name' => $product->getName(),
                    'price' => $product->getPrice() . ' €',
                    'url' => $this->generateUrl('app_product_show', ['id' => $product->getId()]),
                ];
            }

            $users = $userRepo->createQueryBuilder('u')
                ->where('u.username LIKE :q')
                ->setParameter('q', '%' . $query . '%')
                ->setMaxResults(3)
                ->getQuery()
                ->getResult();

            foreach ($users as $user) {
                $results[] = [
                    'type' => 'user',
                    'name' => $user->getUsername() ?? $user->getEmail(),
                    'url' => $this->generateUrl('app_search', ['q' => $user->getUsername()]),
                ];
            }
        }

        return new JsonResponse($results);
    }
}