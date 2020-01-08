<?php


namespace AppBundle\Form;


use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BookFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class, array('required' => false, 'label' => 'title '))
            ->add('description', TextType::class, array('required' => false, 'label' => 'description '))
            ->add('authors', EntityType::class, array(
                'label' => 'authors',
                'multiple' => true,
                'required' => false,
                'class' => 'AppBundle:Author'))
            ->add('dateFrom', DateType::class, array(
                'label' => 'date from',
                'widget' => 'single_text',
                'input'  => 'datetime',
                'format' => 'yyyy-MM-dd',
                'attr' => array('class' => 'date'),
                'required' => false))
            ->add('dateTo', DateType::class, array(
                'label' => 'date to',
                'widget' => 'single_text',
                'input'  => 'datetime',
                'format' => 'yyyy-MM-dd',
                'attr' => array('class' => 'date'),
                'required' => false))
            ->add('queryType', ChoiceType::class, [
                'label' => 'Show books with > 2 authors(don\'t use other filters)',
                'choices'  => [
                    'No' => 0,
                    'Yes, with native query' => 1,
                    'Yes, with dql query' => 2,
                ],
            ]);
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'csrf_protection' => false,
        );
    }

    public function getName()
    {
        return 'search';
    }
}