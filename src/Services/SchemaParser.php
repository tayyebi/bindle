<?php

namespace App\Services;

class SchemaParser
{
    private ?string $lastError = null;

    public function getLastError(): ?string
    {
        return $this->lastError;
    }

    public function parse(string $url, ?string $shopId = null): ?array
    {
        $this->lastError = null;
        $startTime = microtime(true);
        $httpCode = 0;
        $success = false;
        $parserUsed = '';
        $errorMessage = '';
        $productData = null;

        try {
            $html = $this->fetchUrl($url, $httpCode);
            if ($html === null) {
                $errorMessage = $httpCode === 0 ? 'cURL error: failed to connect' : "HTTP {$httpCode}";
                $this->lastError = $errorMessage;
                return null;
            }

            $productData = $this->parseJsonLd($html);
            if ($productData) {
                $parserUsed = 'jsonld';
                $success = true;
                return $productData;
            }

            $productData = $this->parseMicrodata($html);
            if ($productData) {
                $parserUsed = 'microdata';
                $success = true;
                return $productData;
            }

            if (preg_match('/<script[^>]*type="application\/ld\+json"[^>]*>/si', $html)) {
                $errorMessage = 'JSON-LD found but no Product with valid price';
            } elseif (preg_match('/itemscope[^>]*itemtype="?[^"]*\/Product"?/si', $html)) {
                $errorMessage = 'Microdata found but no price extracted';
            } else {
                $errorMessage = 'No schema.org/Product markup found on page';
            }
            $this->lastError = $errorMessage;
            return null;
        } catch (\Throwable $e) {
            $errorMessage = 'Exception: ' . $e->getMessage();
            $this->lastError = $errorMessage;
            return null;
        } finally {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            RequestLogger::logCrawl(
                url: $url,
                shopId: $shopId,
                httpCode: $httpCode,
                durationMs: $durationMs,
                success: $success,
                parserUsed: $parserUsed,
                errorMessage: $errorMessage,
                productData: $success ? $productData : null
            );
        }
    }

    private function fetchUrl(string $url, ?int &$httpCode = null): ?string
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_USERAGENT => 'BindleBot/1.0',
            CURLOPT_MAXREDIRS => 5,
        ]);

        $html = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($html === false || $httpCode !== 200) {
            if ($html === false && $curlError) {
                $httpCode = 0;
            }
            return null;
        }

        return $html;
    }

    private function parseJsonLd(string $html): ?array
    {
        $pattern = '/<script[^>]*type="application\/ld\+json"[^>]*>(.*?)<\/script>/si';
        if (!preg_match_all($pattern, $html, $matches)) return null;

        foreach ($matches[1] as $json) {
            $data = json_decode(trim($json), true);
            if (!$data) continue;

            $data = $this->normalizeJsonLd($data);

            if (!isset($data['@type'])) continue;
            $types = (array) $data['@type'];
            if (!in_array('Product', $types)) continue;

            $price = $this->extractPrice($data);
            if ($price === null) continue;

            return [
                'name' => $data['name'] ?? '',
                'price' => $price,
                'currency' => $this->extractCurrency($data),
                'description' => $data['description'] ?? '',
                'image_url' => $this->extractImage($data),
                'type' => $this->detectProductType($data),
            ];
        }

        return null;
    }

    private function normalizeJsonLd(array $data): array
    {
        if (isset($data['@graph'])) {
            foreach ($data['@graph'] as $item) {
                $types = (array) ($item['@type'] ?? []);
                if (in_array('Product', $types)) {
                    return $item;
                }
            }
        }
        return $data;
    }

    private function parseMicrodata(string $html): ?array
    {
        if (!preg_match('/itemscope[^>]*itemtype="?[^"]*\/Product"?(.*?)>/si', $html)) {
            return null;
        }

        libxml_use_internal_errors(true);
        $doc = new \DOMDocument();
        $doc->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        $xpath = new \DOMXPath($doc);

        $productNodes = $xpath->query('//*[@itemscope and contains(@itemtype, "schema.org/Product")]');
        if ($productNodes->length === 0) return null;

        $node = $productNodes->item(0);

        return [
            'name' => $this->getMicrodataValue($xpath, $node, 'name'),
            'price' => $this->extractMicrodataPrice($xpath, $node),
            'currency' => $this->extractMicrodataCurrency($xpath, $node),
            'description' => $this->getMicrodataValue($xpath, $node, 'description'),
            'image_url' => $this->getMicrodataAttribute($xpath, $node, 'image', 'src'),
            'type' => 'physical',
        ];
    }

    private function getMicrodataValue(\DOMXPath $xpath, \DOMElement $context, string $prop): string
    {
        $nodes = $xpath->query('.//*[@itemprop="' . $prop . '"]', $context);
        if ($nodes->length === 0) return '';
        return trim($nodes->item(0)->textContent);
    }

    private function getMicrodataAttribute(\DOMXPath $xpath, \DOMElement $context, string $prop, string $attr): string
    {
        $nodes = $xpath->query('.//*[@itemprop="' . $prop . '"]', $context);
        if ($nodes->length === 0) return '';
        return $nodes->item(0)->getAttribute($attr) ?? '';
    }

    private function extractMicrodataPrice(\DOMXPath $xpath, \DOMElement $context): ?float
    {
        $nodes = $xpath->query('.//*[@itemprop="price"]', $context);
        if ($nodes->length > 0) {
            $content = $nodes->item(0)->getAttribute('content');
            if ($content === '') $content = $nodes->item(0)->textContent;
            $price = (float) str_replace([',', ' '], ['.', ''], trim($content));
            return $price > 0 ? $price : null;
        }
        return null;
    }

    private function extractMicrodataCurrency(\DOMXPath $xpath, \DOMElement $context): string
    {
        $nodes = $xpath->query('.//*[@itemprop="priceCurrency"]', $context);
        if ($nodes->length > 0) {
            $content = $nodes->item(0)->getAttribute('content');
            if ($content === '') $content = $nodes->item(0)->textContent;
            return strtoupper(trim($content));
        }
        return 'USD';
    }

    private function extractPrice(array $data): ?float
    {
        if (isset($data['offers'])) {
            $offers = $data['offers'];
            if (isset($offers['price'])) {
                return (float) $offers['price'];
            }
            if (isset($offers[0]['price'])) {
                return (float) $offers[0]['price'];
            }
        }
        if (isset($data['price'])) {
            return (float) $data['price'];
        }
        return null;
    }

    private function extractCurrency(array $data): string
    {
        if (isset($data['offers'])) {
            $offers = $data['offers'];
            if (isset($offers['priceCurrency'])) {
                return strtoupper($offers['priceCurrency']);
            }
            if (isset($offers[0]['priceCurrency'])) {
                return strtoupper($offers[0]['priceCurrency']);
            }
        }
        return 'USD';
    }

    private function extractImage(array $data): string
    {
        if (isset($data['image'])) {
            if (is_string($data['image'])) return $data['image'];
            if (is_array($data['image']) && isset($data['image']['url'])) {
                return $data['image']['url'];
            }
            if (is_array($data['image']) && isset($data['image'][0])) {
                return is_string($data['image'][0]) ? $data['image'][0] : '';
            }
        }
        return '';
    }

    private function detectProductType(array $data): string
    {
        if (isset($data['productType']) && stripos($data['productType'], 'digital') !== false) {
            return 'digital';
        }
        $categories = $data['category'] ?? '';
        if (is_string($categories) && stripos($categories, 'digital') !== false) {
            return 'digital';
        }
        return 'physical';
    }
}
