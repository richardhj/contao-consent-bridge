<?php

declare(strict_types=1);

namespace Hofff\Contao\Consent\Bridge\EventListener\Hook;

use Contao\FrontendTemplate;
use Contao\Template;
use Hofff\Contao\Consent\Bridge\Render\ActiveConsentTool;

final class ParseFrontendTemplateListener extends ConsentListener
{
    public function onParseTemplate(Template $template) : void
    {
        if (! $template instanceof FrontendTemplate) {
            return;
        }

        $consentTool = $this->consentTool();
        if ($consentTool === null) {
            return;
        }

        if (isset($template->activeConsentTool)) {
            return;
        }

        $template->activeConsentTool = new ActiveConsentTool($consentTool, $this->consentIdParser);
    }

    public function onParseFrontendTemplate(string $buffer, string $templateName) : string
    {
        return $this->renderForTemplate($buffer, $templateName);
    }
}
