<?php

declare(strict_types=1);

namespace spec\Hofff\Contao\Consent\Bridge\EventListener\Hook;

use Contao\ModuleModel;
use Hofff\Contao\Consent\Bridge\ConsentId;
use Hofff\Contao\Consent\Bridge\ConsentTool;
use Hofff\Contao\Consent\Bridge\ConsentToolManager;
use Hofff\Contao\Consent\Bridge\EventListener\Hook\RenderFrontendModuleListener;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class RenderFrontendModuleListenerSpec extends ObjectBehavior
{
    /** @var ConsentToolManager */
    private $consentToolManager;

    public function let(RequestScopeMatcher $scopeMatcher) : void
    {
        $this->consentToolManager = new ConsentToolManager();

        $this->beConstructedWith($this->consentToolManager, $scopeMatcher);
    }

    public function it_is_initializable() : void
    {
        $this->shouldHaveType(RenderFrontendModuleListener::class);
    }

    public function it_renders_supported_frontend_module(
        ConsentTool $consentTool,
        ConsentId $consentId,
        RequestScopeMatcher $scopeMatcher,
        ModuleModel $model
    ) : void {
        $scopeMatcher->isFrontendRequest()->willReturn(true);

        $consentTool->determineConsentIdFromModel($model)
            ->willReturn($consentId);

        $consentTool->renderHtml(Argument::type('string'), $consentId)
            ->shouldBeCalled()
            ->willReturn('wrapped');

        $this->consentToolManager->activate($consentTool->getWrappedObject());

        $this->onGetFrontendModule($model, '<html></html>')->shouldReturn('wrapped');
    }

    public function it_bypass_unsupported_frontend_module(
        ConsentTool $consentTool,
        ConsentId $consentId,
        RequestScopeMatcher $scopeMatcher,
        ModuleModel $model
    ) : void {
        $scopeMatcher->isFrontendRequest()->willReturn(true);

        $consentTool->determineConsentIdFromModel($model)
            ->willReturn(null);

        $consentTool->renderHtml(Argument::type('string'), $consentId)
            ->shouldNotBeCalled();

        $this->consentToolManager->activate($consentTool->getWrappedObject());

        $this->onGetFrontendModule($model, '<html></html>')->shouldReturn('<html></html>');
    }
}