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

class RoundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minConfirmations')
            ->add('payoutPercent')
            ->add('lastPayoutPercent')
            ->add('adminLastPayoutPercent')
            ->add('referrerPayoutPercent')
            ->add('minDeposit')
            ->add('maxDeposit')
            ->add('payoutFeePercent')
            ->add('roundEndRemainingReturnPercent')
            ->add('roundTime', null, [
                'label' => 'Round time (in hours)'
            ])
            ->add('roundTimeType', 'choice', [
                'label' => 'Round time type',
                'choices' => [
                    Round::ROUND_TIME_TYPE_LAST_PAYOUT => 'last payout',
                    Round::ROUND_TIME_TYPE_LAST_DEPOSIT => 'last deposit'

                ]
            ]);
    }


    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'round_type';
    }
}
