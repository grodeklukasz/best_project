<?php

namespace App\Controller\Admin;

use App\Entity\Tn;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;

class TnCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Tn::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        yield FormField::addTab('Basic Dateils');
        yield TextField::new('nachname')->setRequired(True);
        yield TextField::new('vorname')->setRequired(True);
        yield DateField::new('gebdatum')->setFormat('dd.MM.yyyy')->hideOnIndex()->setRequired(True);

        yield FormField::addTab('Kontakt Daten');
        yield TextField::new('telefonnummer')->setRequired(True);
        yield TextField::new('email')->setRequired(True);

        yield FormField::addTab('Angaben zur Teilnahme');
        yield TextField::new('pseudonym')->setRequired(True);
        yield DateField::new('starttermin')->setFormat('dd.MM.yyyy');
        yield DateField::new('ausgeschieden')->setFormat('dd.MM.yyyy');;
        yield ChoiceField::new('grundausgeschieden','Grund der Ausgeschieden')->setChoices([
            "Erfolgreich beendet" => "EB",
            "Vorzeitig beendet nach RS mit FM" => "VAB",
            "Nicht angetreten-RS an FM" => "NA",
            "FM von Maßnahme abgemeldet" => "NA"
        ])->hideOnIndex();
        yield BooleanField::new('status');
        yield TextareaField::new('bemerkung')->hideOnIndex();

        yield FormField::addTab('Ansprechpartner');
        yield FormField::addPanel('Jobcoach');
        yield AssociationField::new('jobcoach')->setRequired(True);
        yield FormField::addPanel('FM');
        yield AssociationField::new('fm')->setRequired(True);


    }
    
}
