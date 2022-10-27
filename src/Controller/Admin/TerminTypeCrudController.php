<?php

namespace App\Controller\Admin;

use App\Entity\TerminType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class TerminTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TerminType::class;
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
