<?php

namespace App\Filament\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\Checkbox;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;
use SensitiveParameter;

/**
 * Student registration with an explicit consent checkbox for the Terms
 * and the Privacy Policy, as the personal data protection law requires
 * before we collect documents and profile data.
 */
class Register extends BaseRegister
{
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->getNameFormComponent(),
            $this->getEmailFormComponent(),
            $this->getPasswordFormComponent(),
            $this->getPasswordConfirmationFormComponent(),
            $this->getConsentFormComponent(),
        ]);
    }

    protected function getConsentFormComponent(): Component
    {
        $link = fn (string $route, string $label) => '<a href="'.e(route($route)).'" target="_blank" class="underline font-semibold">'.e($label).'</a>';

        return Checkbox::make('consent')
            ->label(new HtmlString(__('I agree to the :terms and the :privacy, including the processing of my personal data for job placement in Japan.', [
                'terms'   => $link('legal.terms', __('Terms & Conditions')),
                'privacy' => $link('legal.privacy', __('Privacy Policy')),
            ])))
            ->accepted()
            ->validationMessages(['accepted' => __('Please agree to the Terms & Conditions and Privacy Policy to continue.')]);
    }

    protected function mutateFormDataBeforeRegister(#[SensitiveParameter] array $data): array
    {
        unset($data['consent']);

        return $data;
    }
}
