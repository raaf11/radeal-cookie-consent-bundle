<?php

declare(strict_types=1);

/*
 * This file is part of the ConnectHolland CookieConsentBundle package.
 * (c) Connect Holland.
 */

namespace ConnectHolland\CookieConsentBundle\Form;

use ConnectHolland\CookieConsentBundle\Cookie\CookieChecker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CookieConsentType extends AbstractType
{
    public function __construct(
        private CookieChecker $cookieChecker,
        private array         $cookieCategories,
        private bool          $csrfProtection = true
    )
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach ($this->cookieCategories as $category) {
            $builder->add($category, ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'data' => $this->cookieChecker->isCategoryAllowedByUser($category) ? 'true' : 'false',
                'choices' => [
                    ['ch_cookie_consent.yes' => 'true'],
                    ['ch_cookie_consent.no' => 'false'],
                ],
            ]);
        }
        $builder->add('save', SubmitType::class, ['label' => 'ch_cookie_consent.save', 'attr' => ['class' => 'btn btn-primary ch-cookie-consent__btn']]);
    }

    /**
     * Default options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'translation_domain' => 'CHCookieConsentBundle',
            'csrf_protection' => $this->csrfProtection,
        ]);
    }
}
