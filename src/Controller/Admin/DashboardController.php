<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        return $this->redirect(
            $adminUrlGenerator
                ->setController(ProductCrudController::class)
                ->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('MadeByLoop — Admin');
    }

    public function configureMenuItems(): iterable
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');

        yield MenuItem::section('Utilisateurs');
        yield MenuItem::linkToUrl('Utilisateurs', 'fa fa-users',
            $adminUrlGenerator->setController(UserCrudController::class)->generateUrl()
        );

        yield MenuItem::section('Boutique');
        yield MenuItem::linkToUrl('Produits', 'fa fa-box',
            $adminUrlGenerator->setController(ProductCrudController::class)->generateUrl()
        );
        yield MenuItem::linkToUrl('Catégories', 'fa fa-tags',
            $adminUrlGenerator->setController(CategoryCrudController::class)->generateUrl()
        );
        yield MenuItem::linkToUrl('Commandes', 'fa fa-shopping-cart',
            $adminUrlGenerator->setController(OrderCrudController::class)->generateUrl()
        );
    }
}