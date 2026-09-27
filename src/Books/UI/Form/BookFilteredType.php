<?php

namespace App\Books\UI\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;


class BookFilteredType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Récupération des valeurs distinctes passées dans les options
        $status = $options['status'] ?? [];
        $authorName = $options['authorName'] ?? [];


        
        // Préparer le tableau choices en ajoutant l'option "Tous" (null)
        $statusChoices = ['Tous' => null];
        foreach ($status as $statu) {
            $statusChoices[$statu] = $statu;
        }       

        $authorNameChoices = ['Tous' => null];
        foreach ($authorName as $author) {
            $authorNameChoices[$author] = $author;
        }




        $builder
        //champ "status" avec le type ChoiceType
            ->add('status', ChoiceType::class, [
                'choices'  => $statusChoices,
                'expanded' => false,   //true: bouton radio
                'multiple' => false,  // un seul choix possible
                'attr' => ['class' => 'form-select'], // Ajout de la classe Bootstrap ici
                'placeholder' => 'Choisir un status',
                'required' => false,  // pas obligatoire
                'label' => 'Filtrer par status: ',
            ])
        //champ "authorName" avec le type ChoiceType
            ->add('authorName', ChoiceType::class, [
                'choices'  => $authorNameChoices,
                'expanded' => false,  
                'multiple' => false,  
                'attr' => ['class' => 'form-select'], 
                'placeholder' => 'Choisir un(e) auteur(e)',
                'required' => false,  
                'label' => 'filtrer par auteur(e): ',
            ]);

            
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'status' => [],         // par défaut un tableau vide
            'authorName' => [], 
            
        ]);
    }
}