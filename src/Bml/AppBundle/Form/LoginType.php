<?php
/**
 * Created by PhpStorm.
 * User: bml
 * Date: 29.04.14
 * Time: 13:15
 */

namespace Bml\AppBundle\Form;


use Bml\AppBundle\Entity\Round;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login')
            ->add('password', 'password');
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'login_type';
    }
}
