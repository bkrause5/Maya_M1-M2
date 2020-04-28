<?php

namespace App\Form;

use App\Entity\ProduitRecherche;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;

class ProduitRechercheType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'required' => false,
            ])
            ->add('prixMini', MoneyType::class, [
                'label' => 'Prix minimum',
                'required' => false,
                'invalid_message' => 'Nombre attendu'
            ])
            ->add('prixMaxi', MoneyType::class, [
                'label' => 'Prix maximum',
                'required' => false,
                'invalid_message' => 'Nombre attendu'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProduitRecherche::class,
        ]);
    }
}
