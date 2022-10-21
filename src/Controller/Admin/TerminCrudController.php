<?php

namespace App\Controller\Admin;

use App\Entity\Termin;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;


class TerminCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud 
    {
        return $crud 
        ->setEntityLabelInSingular('Termin')
        ->setEntityLabelInPlural('Termine')
        ->setDefaultSort(['termindatum'=>'DESC'])
        ->setPaginatorPageSize(21);
    }
    public function configureFilters(Filters $filters): Filters 
    {
        return $filters
        ->add(EntityFilter::new('tn'))
        ;
    }
    public static function getEntityFqcn(): string
    {
        return Termin::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addPanel('Termin');
        yield AssociationField::new('tn','Teilnehmer');
        yield DateField::new('termindatum')->setFormat('dd.MM.yyyy');;
        yield AssociationField::new('termintype','Termin Type');
        yield TextareaField::new('bemerkung');
    }
    
}
