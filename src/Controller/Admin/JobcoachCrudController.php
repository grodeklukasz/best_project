<?php

namespace App\Controller\Admin;

use App\Entity\Jobcoach;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;

class JobcoachCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Jobcoach::class;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
        ->disable(Action::DELETE);
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
