<?php
namespace Cms\UserBundle\Form;

use Cms\UserBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserProfileEditionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', TextType::class,[
                    'attr' => array('class' => 'form-control'),
                    'label' => "Username",
                    'label_attr' => ['class' => 'field-name']
                ])
                ->add('email', TextType::class,  [
                    'attr' => array('class' => 'form-control'),
                    'label' => "Email",
                    'label_attr' => ['class' => 'field-name']
                ])
                ->add('dateOfBirthday', DateType::class, [
                    'label' => "Date of birthday",
                    'label_attr' => ['class' => 'field-name']
                ])
                ->add('about', TextType::class,[
                    'attr' => array('class' => 'form-control'),
                    'label' => "About",
                    'label_attr' => ['class' => 'field-name']
                ])
                ->add('profilePicturePath', FileType::class, [
                    'data_class' => null,
                    'label' => "File",
                    'required' => false
                ])
                ->add('save', SubmitType::class,[
                    'attr' => ['class' => 'form-control btn-info login-button', 'type' => 'submit']
                ]);
    }
    public function getName()
    {
        return 'user-profile-edition';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cms\UserBundle\Entity\User'
        ));
    }
}