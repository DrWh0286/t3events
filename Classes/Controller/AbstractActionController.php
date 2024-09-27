<?php

declare(strict_types=1);

namespace DWenzel\T3events\Controller;

use DWenzel\T3events\Event\EntityNotFoundErrorWasTriggered;
use DWenzel\T3events\Utility\SettingsInterface as SI;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\ImmediateResponseException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Page\PageAccessFailureReasons;

abstract class AbstractActionController extends ActionController
{
    /**
     * @var string
     */
    protected $entityNotFoundMessage = 'The requested entity could not be found';

    public function getEntityNotFoundMessage(): string
    {
        return $this->entityNotFoundMessage;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Mvc\RequestInterface $request
     * @param \TYPO3\CMS\Extbase\Mvc\ResponseInterface $response
     * @return void
     * @throws \Exception
     * @override \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
     */
    public function processRequest(RequestInterface $request): ResponseInterface
    {
        try {
            $response = parent::processRequest($request);
        } catch (\Exception $exception) {
            if (
                (($exception instanceof PropertyException\TargetNotFoundException)
                    || ($exception instanceof PropertyException\InvalidSourceException))
                && $request instanceof Request
            ) {
                $controllerName = lcfirst($request->getControllerName());
                $actionName = $request->getControllerActionName();
                if (isset($this->settings[$controllerName][$actionName][SI::ERROR_HANDLING])) {
                    $configuration = $this->settings[$controllerName][$actionName][SI::ERROR_HANDLING];
                    // @todo: Return a Response here
                    $this->handleEntityNotFoundError($configuration);
                }

            }
            throw $exception;
        }

        return $response;
    }

    /**
     * Error handling if requested entity is not found
     *
     * @param string $configuration Configuration for handling
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws ImmediateResponseException
     */
    public function handleEntityNotFoundError(string $configuration): void
    {
        if ($configuration === '' || $configuration === '0') {
            return;
        }
        $conf = GeneralUtility::trimExplode(',', $configuration);
        switch ($conf[0]) {
            case 'redirectToListView':
                $this->redirect('list');
                break;
            case 'redirectToPage':
                if (count($conf) === 1 || count($conf) > 3) {
                    $msg = sprintf('If error handling "%s" is used, either 2 or 3 arguments, splitted by "," must be used', $configuration[0]);
                    throw new \InvalidArgumentException($msg);
                }
                $this->uriBuilder->reset();
                $this->uriBuilder->setTargetPageUid((int)$conf[1]);
                $this->uriBuilder->setCreateAbsoluteUri(true);
                if ($this->isSSLEnabled()) {
                    $this->uriBuilder->setAbsoluteUriScheme('https');
                }
                $url = $this->uriBuilder->build();
                if (isset($conf[2])) {
                    $this->redirectToUri($url, 0, (int)$conf[2]);
                } else {
                    $this->redirectToUri($url);
                }
                break;
            case 'pageNotFoundHandler':
                /** @var ErrorController $errorController */
                $errorController = GeneralUtility::makeInstance(ErrorController::class);
                $response = $errorController->pageNotFoundAction($this->request, $this->entityNotFoundMessage, ['code' => PageAccessFailureReasons::PAGE_NOT_FOUND]);
                //@todo: Check if ImmediateResponseException ist the correct way to do this.
                throw new ImmediateResponseException($response);
                break;
            default:
                $params = [
                    SI::CONFIG => $conf,
                    'requestArguments' => $this->request->getArguments(),
                    SI::ACTION_NAME => $this->request->getControllerActionName()
                ];

                /** @var EntityNotFoundErrorWasTriggered $entityNotFoundErrorWasTriggered */
                $entityNotFoundErrorWasTriggered = $this->eventDispatcher->dispatch(new EntityNotFoundErrorWasTriggered($params));
                $params = $entityNotFoundErrorWasTriggered->getParameters();

                if (isset($params[SI::REDIRECT_URI])) {
                    $this->redirectToUri($params[SI::REDIRECT_URI]);
                }
                if (isset($params[SI::REDIRECT])) {
                    $this->redirect(
                        $params[SI::REDIRECT][SI::ACTION_NAME],
                        $params[SI::REDIRECT][SI::CONTROLLER_NAME],
                        $params[SI::REDIRECT][SI::KEY_EXTENSION_NAME],
                        $params[SI::REDIRECT][SI::ARGUMENTS],
                        $params[SI::REDIRECT]['pageUid'],
                        $params[SI::REDIRECT]['delay'],
                        $params[SI::REDIRECT]['statusCode']
                    );
                }
                if (isset($params[SI::FORWARD])) {
                    $this->forward(
                        $params[SI::FORWARD][SI::ACTION_NAME],
                        $params[SI::FORWARD][SI::CONTROLLER_NAME],
                        $params[SI::FORWARD][SI::KEY_EXTENSION_NAME],
                        $params[SI::FORWARD][SI::ARGUMENTS]
                    );
                }
        }
    }

    /**
     * Tells if TYPO3 SSL is enabled
     *
     * Wrapper method for static call
     *
     * @return bool
     */
    private function isSSLEnabled()
    {
        return GeneralUtility::getIndpEnv('TYPO3_SSL');
    }
}
