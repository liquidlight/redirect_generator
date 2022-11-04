<?php

declare(strict_types=1);

namespace GeorgRinger\RedirectGenerator\Domain\Model\Dto;

use TYPO3\CMS\Core\Routing\PageArguments;
use TYPO3\CMS\Core\Routing\SiteRouteResult;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class UrlResult
{

    /** @var SiteRouteResult */
    protected $siteRouteResult;

    /** @var PageArguments */
    protected $pageArguments;

    /**
     * UrlResult constructor.
     * @param SiteRouteResult $siteRouteResult
     * @param PageArguments $pageArguments
     */
    public function __construct(SiteRouteResult $siteRouteResult, PageArguments $pageArguments)
    {
        $this->siteRouteResult = $siteRouteResult;
        $this->pageArguments = $pageArguments;
    }

    /**
     * @return SiteRouteResult
     */
    public function getSiteRouteResult(): SiteRouteResult
    {
        return $this->siteRouteResult;
    }

    /**
     * @return PageArguments
     */
    public function getPageArguments(): PageArguments
    {
        return $this->pageArguments;
    }

    public function getLinkString(): string
    {
        $parameters = [
            'uid' => $this->pageArguments->getPageId()
        ];

        // language
        if ($this->siteRouteResult->getLanguage() && $this->siteRouteResult->getLanguage()->getLanguageId() > 0) {
            $parameters['L'] = $this->siteRouteResult->getLanguage()->getLanguageId();
        }

        if(isset($this->pageArguments->getArguments()['tx_llcatalog_pi'])) {
            $catalog = $this->pageArguments->getArguments()['tx_llcatalog_pi'];
            unset($catalog['filters']);

            if(count($catalog)) {
                $model = array_keys($catalog)[0];

                $parameters = GeneralUtility::implodeArrayForUrl('', $parameters);
                return sprintf(
                    't3://catalog?model=%s&root=%s&uid=%s',
                    $model,
                    $this->siteRouteResult->getSite()->getRootPageId(),
                    $catalog[$model],
                );
            }
        }

        $parameters = GeneralUtility::implodeArrayForUrl('', $parameters);
        return sprintf('t3://page?%s', trim($parameters, '&'));
    }


}
