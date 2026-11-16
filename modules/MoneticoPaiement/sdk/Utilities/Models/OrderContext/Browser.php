<?php

namespace MoneticoPaiement\Sdk\Utilities\Models\OrderContext;

class Browser implements \JsonSerializable
{
    /**
     * @var string
     */
    private string $accept_header;
    /**
     * @var bool
     */
    private bool $java_enabled;
    /**
     * @var bool
     */
    private bool $javascript_enabled;
    /**
     * @var string
     */
    private string $language;
    /**
     * @var int
     */
    private int $color_depth;
    /**
     * @var int
     */
    private int $screen_height;
    /**
     * @var int
     */
    private int $screen_width;
    /**
     * @var int
     */
    private int $timezone;
    /**
     * @var string
     */
    private string $user_agent;

    /**
     * @return array|mixed
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return array_filter([
            'accept_header' => $this->getAcceptHeader(),
            'java_enabled' => $this->isJavaEnabled(),
            'javascript_enabled' => $this->getJavascriptEnabled(),
            'language' => $this->getLanguage(),
            'color_depth' => $this->getColorDepth(),
            'screen_height' => $this->getScreenHeight(),
            'screen_width' => $this->getScreenWidth(),
            'timezone' => $this->getTimezone(),
            'user_agent' => $this->getUserAgent()
        ], function ($value) {
            return !is_null($value);
        });
    }

    /**
     * @return string
     */
    public function getAcceptHeader(): string
    {
        return $this->accept_header;
    }

    /**
     * @param string $accept_header
     */
    public function setAcceptHeader(string $accept_header): void
    {
        $this->accept_header = $accept_header;
    }

    /**
     * @return bool
     */
    public function isJavaEnabled(): bool
    {
        return $this->java_enabled;
    }

    /**
     * @param bool $java_enabled
     */
    public function setJavaEnabled(bool $java_enabled): void
    {
        $this->java_enabled = $java_enabled;
    }

    /**
     * @return bool
     */
    public function getJavascriptEnabled(): bool
    {
        return $this->javascript_enabled;
    }

    /**
     * @param bool $java_enabled
     */
    public function setJavascriptEnabled(bool $javascript_enabled): void
    {
        $this->javascript_enabled = $javascript_enabled;
    }



    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return int
     */
    public function getColorDepth(): int
    {
        return $this->color_depth;
    }

    /**
     * @param int $color_depth
     */
    public function setColorDepth(int $color_depth): void
    {
        $this->color_depth = $color_depth;
    }

    /**
     * @return int
     */
    public function getScreenHeight(): int
    {
        return $this->screen_height;
    }

    /**
     * @param int $screen_height
     */
    public function setScreenHeight(int $screen_height): void
    {
        $this->screen_height = $screen_height;
    }

    /**
     * @return int
     */
    public function getScreenWidth(): int
    {
        return $this->screen_width;
    }

    /**
     * @param int $screen_width
     */
    public function setScreenWidth(int $screen_width): void
    {
        $this->screen_width = $screen_width;
    }

    /**
     * @return int
     */
    public function getTimezone(): int
    {
        return $this->timezone;
    }

    /**
     * @param int $timezone
     */
    public function setTimezone(int $timezone): void
    {
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getUserAgent(): string
    {
        return $this->user_agent;
    }

    /**
     * @param string $user_agent
     */
    public function setUserAgent(string $user_agent): void
    {
        $this->user_agent = $user_agent;
    }
}
