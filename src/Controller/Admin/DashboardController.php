<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render('@EasyAdmin/page/content.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('MadeByLoop — Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToUrl('Utilisateurs', 'fa fa-users', $this->generateUrl('admin', [
            'crudControllerFqcn' => UserCrudController::class,
            'crudAction' => 'index',
        ]));

        yield MenuItem::section('Boutique');
        yield MenuItem::linkToUrl('Produits', 'fa fa-box', $this->generateUrl('admin', [
            'crudControllerFqcn' => ProductCrudController::class,
            'crudAction' => 'index',
        ]));
        yield MenuItem::linkToUrl('Catégories', 'fa fa-tags', $this->generateUrl('admin', [
            'crudControllerFqcn' => CategoryCrudController::class,
            'crudAction' => 'index',
        ]));
        yield MenuItem::linkToUrl('Commandes', 'fa fa-shopping-cart', $this->generateUrl('admin', [
            'crudControllerFqcn' => OrderCrudController::class,
            'crudAction' => 'index',
        ]));
    }
}