<?php

declare(strict_types=1);

namespace spec\Hofff\Contao\Consent\Bridge\EventListener\Hook;

use Contao\ContentModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Hofff\Contao\Consent\Bridge\Bridge;
use Hofff\Contao\Consent\Bridge\ConsentId;
use Hofff\Contao\Consent\Bridge\ConsentId\ConsentIdParser;
use Hofff\Contao\Consent\Bridge\ConsentTool;
use Hofff\Contao\Consent\Bridge\ConsentToolManager;
use Hofff\Contao\Consent\Bridge\EventListener\Hook\RenderComponentsListener;
use Hofff\Contao\Consent\Bridge\Exception\InvalidArgumentException;
use Hofff\Contao\Consent\Bridge\Plugin\BasePlugin;
use Netzmacht\Contao\Toolkit\Routing\RequestScopeMatcher;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

final class RenderComponentsListenerSpec extends ObjectBehavior
{
    /** @var Bridge */
    private $bridge;

    public function let(
        RequestScopeMatcher $scopeMatcher,
        ConsentToolManager $consentToolManager,
        ConsentIdParser $consentIdParser,
        ConsentTool $consentTool
    ) : void {
        $this->bridge = new Bridge();

        $consentToolManager->activeConsentTool()->willReturn($consentTool);

        $this->beConstructedWith($consentToolManager, $scopeMatcher, $consentIdParser, $this->bridge);
    }

    public function it_is_initializable() : void
    {
        $this->shouldHaveType(RenderComponentsListener::class);
    }

    public function it_renders_supported_content_element(
        ConsentIdParser $consentIdParser,
        ConsentTool $consentTool,
        ConsentId $consentId,
        RequestScopeMatcher $scopeMatcher,
        ContentModel $model
    ) : void {
        $scopeMatcher->isFrontendRequest()->willReturn(true);

        $model->getWrappedObject()->type                     = 'foo';
        $model->getWrappedObject()->hofff_consent_bridge_tag = 'consent_id';

        $consentTool->renderContent(Argument::type('string'), Argument::type(ConsentId::class), $model)
            ->shouldBeCalled()
            ->willReturn('wrapped');

        $consentIdParser->parse(Argument::type('string'))->willReturn($consentId);

        $this->bridge->load(
            new class extends BasePlugin {
                public function supportedContentElements() : array
                {
                    return ['foo'];
                }
            }
        );

        $this->onGetContentElement($model, '<html></html>')->shouldReturn('wrapped');
    }

    public function it_bypass_unsupported_content_element(
        ConsentTool $consentTool,
        RequestScopeMatcher $scopeMatcher,
        ContentModel $model,
        ConsentIdParser $consentIdParser,
        ConsentId $consentId
    ) : void {
        $scopeMatcher->isFrontendRequest()->willReturn(true);

        $model->getWrappedObject()->type                     = 'foo';
        $model->getWrappedObject()->hofff_consent_bridge_tag = 'consent_id';

        $consentIdParser->parse(Argument::type('string'))->willReturn($consentId);

        $consentTool->renderContent(Argument::type('string'), Argument::type(ConsentId::class), $model)
            ->shouldBeCalled()
            ->willReturn('wrapped');

        $this->bridge->load(
            new class extends BasePlugin {
                public function supportedContentElements() : array
                {
                    return ['foo'];
                }
            }
        );

        $this->onGetContentElement($model, '<html></html>')->shouldReturn('wrapped');
    }

    public function it_renders_supported_frontend_module(
        ConsentTool $consentTool,
        RequestScopeMatcher $scopeMatcher,
        ModuleModel $model,
        ConsentIdParser $consentIdParser,
        ConsentId $consentId
    ) : void {
        $scopeMatcher->isFrontendRequest()->willReturn(true);

        $model->getWrappedObject()->type                     = 'foo';
        $model->getWrappedObject()->hofff_consent_bridge_tag = 'consent_id';

        $consentIdParser->parse(Argument::type('string'))->willReturn($consentId);

        $consentTool->renderContent(Argument::type('string'), Argument::type(ConsentId::class), $model)
            ->shouldBeCalled()
            ->willReturn('wrapped');

        $this->bridge->load(
            new class extends BasePlugin {
                public function supportedFrontendModules() : array
                {
                    return ['foo'];
                }
            }
        );

        $this->onGetFrontendModule($model, '<html></html>')->shouldReturn('wrapped');
    }

    public function it_bypass_unsupported_frontend_module(
        ConsentTool $consentTool,
        RequestScopeMatcher $scopeMatcher,
        ModuleModel $model,
        ConsentIdParser $consentIdParser,
        ConsentId $consentId
    ) : void {
        $scopeMatcher->isFrontendRequest()->willReturn(true);

        $model->getWrappedObject()->type                     = 'foo';
        $model->getWrappedObject()->hofff_consent_bridge_tag = 'consent_id';

        $consentIdParser->parse(Argument::type('string'))->willReturn($consentId);

        $consentTool->renderContent(Argument::type('string'), Argument::type(ConsentId::class), $model)
            ->shouldBeCalled()
            ->willReturn('wrapped');

        $this->bridge->load(
            new class extends BasePlugin {
                public function supportedFrontendModules() : array
                {
                    return ['foo'];
                }
            }
        );

        $this->onGetFrontendModule($model, '<html></html>')->shouldReturn('wrapped');
    }
}