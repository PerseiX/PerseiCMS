<?php
namespace Cms\UserBundle\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', TextType::class,[
                'attr' => array('class' => 'form-control'),
                'label' => "Role",
                'mapped' => false,
                'label_attr' => ['class' => 'field-name']
            ])
            ->add('name', TextType::class,[
                'attr' => array('class' => 'form-control'),
                'label' => "Name",
                'mapped' => false,
                'label_attr' => ['class' => 'field-name']
            ])
            ->add('isActive', ChoiceType::class, [
                    'attr' => ['class' => 'form-control'],
                    'choices' =>
                        [
                            'Yes' => 1,
                            'No' => 0,
                        ],
                    'data' => 1,
                    'label' => "Is active",
                    'mapped' => false,
                ])
            ->add('save', SubmitType::class,[
                'attr' => ['class' => 'form-control btn-info login-button', 'type' => 'submit']

            ]);
    }
}
