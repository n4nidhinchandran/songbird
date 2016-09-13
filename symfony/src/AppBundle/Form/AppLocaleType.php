<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppLocaleType extends AbstractType
{
    private $localeChoices;

    public function __construct(array $localeChoices)
    {
    	foreach ($localeChoices as $v) {
            $this->localeChoices[$v] = $v;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => $this->localeChoices,
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'app_locale';
    }
}
