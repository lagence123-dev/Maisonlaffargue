<?php

namespace MoneticoPaiement\Sdk\Response\PaymentService;

use MoneticoPaiement\Sdk\Response\PaymentService\Data;

class NextStep
{
    private string $step;
    private array $recommended_implementation;
    private Data $data;
    private string $url;

    public function __construct(array $nextStepData)
    {
        $this->setStep($nextStepData['step']);
        $this->setRecommendedImplementation($nextStepData['recommended_implementation']);

        if (isset($nextStepData['data'])) {
            $this->setData(new Data($nextStepData['data']));
        }
        if (isset($nextStepData['url'])) {
            $this->setUrl($nextStepData['url']);
        }
    }

    /**
     * @return string
     */
    public function getStep(): string
    {
        return $this->step;
    }

    /**
     * @param string $step
     */
    public function setStep(string $step): void
    {
        $this->step = $step;
    }

    /**
     * @return array
     */
    public function getRecommendedImplementation(): array
    {
        return $this->recommended_implementation;
    }

    /**
     * @param array $recommended_implementation
     */
    public function setRecommendedImplementation(array $recommended_implementation): void
    {
        $this->recommended_implementation = $recommended_implementation;
    }

    public function getData(): Data
    {
        return $this->data;
    }

    public function setData(Data $data): void
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }


    /**
     *
     * @return bool|string
     */
    public function getNextStepHtml()
    {

        $curl = curl_init($this->getUrl());
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($this->getData()->toArray()));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        return curl_exec($curl);
    }

    public function getRecommendedImplementationData()
    {
        $data = '';
        $iframeHtml = $this->getNextStepHtml();

        $recommended_implementation = $this->getRecommendedImplementation();
        if (in_array("invisible_iframe", $recommended_implementation)) {
            $data = "<iframe id='MoneticoPaiementIframe' style='display:none;' sandbox='allow-top-navigation allow-scripts allow-forms allow-same-origin' srcdoc='" . htmlspecialchars($iframeHtml, ENT_QUOTES) . "'></iframe>";
        } else if (in_array("iframe", $recommended_implementation)) {
            $data = "<iframe id='MoneticoPaiementIframe' style='width:400px;height:600px; border:none;' sandbox='allow-top-navigation allow-scripts allow-forms allow-same-origin' srcdoc='" . htmlspecialchars($iframeHtml, ENT_QUOTES) . "'></iframe>";
        } else if (in_array("redirect", $recommended_implementation)) {
            echo $iframeHtml;
        }

        return $data;
    }

    public function getIframe(): string
    {
        // uncomment the line below to test the "old way" to handle HFs
        // return $this->getRecommendedImplementationData();

        $formFields = '';
        foreach ($this->getData()->toArray() as $key => $value) {
            $formFields .= '<input type="hidden" name="' . $key . '" value="' . $value . '" /> ';
        }

        $form = <<<HTML
        <form action="$this->url" method="POST" name="MoneticoPaiementHFEmbeddedForm">
            $formFields
        </form>
        <script>
            document.forms["MoneticoPaiementHFEmbeddedForm"].submit();
        </script>
        HTML;
        $form = htmlentities($form);

        $style = 'display:none;';
        if (in_array('iframe', $this->getRecommendedImplementation(), true)) {
            $style = 'width: 100%; height: 100%; border: none;';
        }

        // allow-same-origin below is *necessary*
        return <<<HTML
        <iframe 
            style="$style" 
            sandbox="allow-top-navigation allow-scripts allow-forms allow-same-origin"
            srcdoc="$form"
        ></iframe>
        HTML;
    }
}
